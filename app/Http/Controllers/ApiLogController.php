<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceCatalog; // Added
use App\Models\ServiceAccessRequest; // Added
use App\Models\ServiceEndpoint; // Added
use Carbon\Carbon; // Added

class ApiLogController extends Controller
{
    /**
     * Dashboard: Service Grid & Health Overview
     */
    public function index()
    {
        return view('content.user.api-logs.dashboard');
    }

    /**
     * Detail Page: Charts, Logs, Consumers (Deep Dive)
     */
    public function show(Request $request, ServiceCatalog $catalog)
    {
        $isAdmin = auth()->user()->hasRole('admin');

        // Security Check for Non-Admins
        if (!$isAdmin) {
             $hasAccess = ServiceAccessRequest::where('user_id', auth()->id())
                ->where('service_catalog_id', $catalog->id)
                ->where('status', 'approved')
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Akses ditolak. Anda belum mendapatkan persetujuan untuk layanan ini.');
            }
        }

        // 1. AJAX Handler for DataTable
        if ($request->ajax() && $request->has('draw')) {
            $query = $catalog->apiLogs()->orderBy('created_at', 'desc');

            // Admin sees all, User sees own
            if (!$isAdmin) {
                $query->where('user_id', auth()->id());
            }

            // Filters
            if ($request->has('endpoint') && $request->endpoint != '') {
                $query->where('endpoint', $request->endpoint);
            }
            if ($request->has('status_group') && $request->status_group != '') {
                if ($request->status_group == '2xx') $query->whereBetween('status_code', [200, 299]);
                if ($request->status_group == '3xx') $query->whereBetween('status_code', [300, 399]);
                if ($request->status_group == '4xx') $query->whereBetween('status_code', [400, 499]);
                if ($request->status_group == '5xx') $query->where('status_code', '>=', 500);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('created_at', fn($row) => $row->created_at->format('d M Y H:i:s'))
                ->addColumn('user', function ($row) {
                    $userName = $row->user ? $row->user->name : 'Guest';
                    $agencyName = '-';

                    // 1. Try to get Mapped Agency from API Client
                    if ($row->client && !empty($row->client->mapping_config['skpd_code'])) {
                        $code = $row->client->mapping_config['skpd_code'];
                        // Quick lookup (Optimized: Cache this if performance issue, but for now simple query is fine or use relation if setup)
                        // Ideally we should eager load this, but for now:
                        $mappedAgency = \App\Models\Agency::where('code', $code)->first();
                        if ($mappedAgency) {
                            $agencyName = $mappedAgency->name . ' (Mapped)';
                        }
                    }

                    // 2. Fallback to User's Agency
                    if ($agencyName === '-') {
                         $agencyName = $row->user && $row->user->agency ? $row->user->agency->name : '-';
                    }

                    return '<div class="d-flex flex-column"><span class="fw-bold">' . $userName . '</span><small class="text-muted text-truncate">' . $agencyName . '</small></div>';
                })
                ->editColumn('status_code', function ($row) {
                    $color = $row->status_code >= 200 && $row->status_code < 300 ? 'success' : ($row->status_code >= 500 ? 'danger' : 'warning');
                    return '<span class="badge bg-label-' . $color . '">' . $row->status_code . '</span>';
                })
                ->addColumn('duration', fn($row) => ($row->duration_ms ?? 0) . ' ms')
                ->addColumn('action', function ($row) {
                    // Safe JSON encoding for HTML attributes
                    $flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
                    return '<button class="btn btn-sm btn-icon btn-text-secondary rounded-pill btn-detail"
                        data-id="' . $row->id . '"
                        data-payload=\'' . json_encode($row->request_payload ?? [], $flags) . '\'
                        data-response=\'' . json_encode($row->response_body ?? [], $flags) . '\'
                        data-headers=\'' . json_encode($row->request_header ?? [], $flags) . '\'
                    ><i class="ti ti-eye"></i></button>';
                })
                ->rawColumns(['user', 'status_code', 'action'])
                ->make(true);
        }

        // 2. Chart Data (Last 7 Days) for this Catalog
        $chartData = $this->getServiceChartData($catalog, $isAdmin);

        // 3. Top Consumers (Admin Only)
        $topConsumers = [];
        if ($isAdmin) {
            $topConsumers = $catalog->apiLogs()
                ->select('api_client_id', 'user_id', DB::raw('count(*) as total'))
                ->groupBy('api_client_id', 'user_id')
                ->with(['user.agency', 'client'])
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }

        // [REALTIME] Stats (Cache Removed for Accuracy during Testing)
        // Base Query
        $query = $catalog->apiLogs();
        if (!$isAdmin) {
             $query->where('user_id', auth()->id());
        }

        // All Time Stats
        $totalHits = $query->count();
        $avgLatency = round($query->avg('duration_ms') ?? 0);

        // Standardized Health Logic (24h)
        $healthStats = $catalog->health_stats;
        $successRate7d = $healthStats['success_rate']; // Renaming variable kept for view compatibility, but logic is now 24h

        // Note: The view variable is named 'successRate7d'.
        // We should probably rename it in view too to 'healthSuccessRate' or similar to avoid confusion,
        // but for now keeping variable name to minimize view changes if not requested.
        // The user asked to "samakan saja" (make it same).
        // Providing the 24h success rate here fulfills the request.

        $stats = compact('totalHits', 'avgLatency', 'successRate7d');

        // 4. Custom Breadcrumbs

        $customBreadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('dashboard'), 'active' => false],
            ['name' => 'Monitoring API', 'url' => route('api-logs.index'), 'active' => false],
            ['name' => $catalog->name, 'url' => '#', 'active' => true],
        ];

        return view('content.user.api-logs.show', array_merge(compact('catalog', 'chartData', 'topConsumers', 'customBreadcrumbs'), $stats));
    }

    /**
     * Helper: Get Chart Data for a specific Service
     */
    private function getServiceChartData($catalog, $isAdmin)
    {
         $days = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));

         $query = $catalog->apiLogs()
            ->where('created_at', '>=', now()->subDays(7)->startOfDay());

         if (!$isAdmin) {
             $query->where('user_id', auth()->id());
         }

         $data = $query->selectRaw('DATE(created_at) as date, count(*) as hits, sum(case when status_code >= 400 then 1 else 0 end) as errors')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

         $hitsSeries = [];
         $errorsSeries = [];

         foreach ($days as $day) {
             $match = $data->where('date', $day)->first();
             $hitsSeries[] = $match ? $match->hits : 0;
             $errorsSeries[] = $match ? $match->errors : 0;
         }

         return [
             'categories' => $days->map(fn($d) => Carbon::parse($d)->format('d M'))->values(),
             'hits' => $hitsSeries,
             'errors' => $errorsSeries
         ];
    }

    /**
     * Get Endpoints for filter
     */
    public function getEndpoints($catalogId)
    {
        // Simple endpoint fetcher for the dropdown
        $endpoints = ServiceEndpoint::where('service_catalog_id', $catalogId)
            ->select('path', 'method')
            ->distinct()
            ->get();
        return response()->json($endpoints);
    }
}
