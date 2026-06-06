<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ApiLog;
use App\Models\Agency;
use App\Models\ServiceCatalog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        // Fetch Active Announcements
        $announcements = \App\Models\Announcement::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // 1. Stats
        $totalTransactions = 0;
        $activeServices = 0;
        $totalInstansi = 0; // Admin only
        $totalConnectedInstansi = 0;
        $totalLayanan = 0;

        // Query Base
        $query = ApiLog::query();
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $totalTransactions = $query->count();
        $errorCount = (clone $query)->where('status_code', '>=', 400)->count(); // Clone to avoid modifying base query if re-used

        // Success Rate
        $successRate = $totalTransactions > 0
            ? round((($totalTransactions - $errorCount) / $totalTransactions) * 100, 1)
            : 100;

        // Avg Latency (All Time)
        // Note: Re-instantiating query for different aggregation
        $latencyQuery = ApiLog::query();
        if (!$isAdmin) {
            $latencyQuery->where('user_id', $user->id);
        }
        $avgLatency = round($latencyQuery->avg('duration_ms') ?? 0);


        // Other Stats
        // Other Stats
        if ($isAdmin) {
             $totalInstansi = Agency::where('status', 'active')->count();

             // Connected: Agencies having users who have successful logs (200-299)
             $totalConnectedInstansi = Agency::whereHas('users.apiLogs', function($q) {
                 $q->whereBetween('status_code', [200, 299]);
             })->count();

             $totalLayanan = ServiceCatalog::count();
        } else {
             // User specific stats
             // 1. Approved Catalogs
             $totalLayanan = \App\Models\ServiceAccessRequest::where('user_id', $user->id)->where('status', 'approved')->count();

             // 2. Connected Catalogs (Has at least one 200 OK log)
             $totalConnectedInstansi = ApiLog::where('user_id', $user->id)
                ->whereBetween('status_code', [200, 299])
                ->distinct('service_catalog_id')
                ->count('service_catalog_id');
        }

        // 2. Chart Data (Hits + Errors) - Last 7 Days
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $period = \Carbon\CarbonPeriod::create($startDate, '1 day', $endDate);

        $chartQuery = ApiLog::selectRaw('DATE(created_at) as date, count(*) as hits, sum(case when status_code >= 400 then 1 else 0 end) as errors')
             ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        if (!$isAdmin) {
            $chartQuery->where('user_id', $user->id);
        }

        $trafficData = $chartQuery->groupBy(DB::raw('DATE(created_at)'))->get();

        $dailyTraffic = [
            'categories' => [],
            'hits' => [],
            'errors' => []
        ];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $record = $trafficData->where('date', $dateString)->first();

            $dailyTraffic['categories'][] = $date->format('d M');
            $dailyTraffic['hits'][] = $record ? $record->hits : 0;
            $dailyTraffic['errors'][] = $record ? $record->errors : 0;
        }

        // 3. Category Distribution (Donut Chart 1) - By Catalog Count
        $categoryStats = [];
        // 4. Category Hits Distribution (Donut Chart 2) - By API Hits
        $categoryHitsStats = [];
        // 5. Top Stats (Bar Chart)
        $topStats = ['categories' => [], 'data' => []];

        if ($isAdmin) {
             // Admin: Count services per category
             $categoryStats = \App\Models\ServiceCategory::withCount('services')
                ->having('services_count', '>', 0)
                ->get()
                ->map(function ($cat) {
                    return ['name' => $cat->name, 'total' => $cat->services_count];
                })->toArray();

             // Admin: Hits per Category
             $categoryHitsStats = DB::table('api_logs')
                ->join('service_catalogs', 'api_logs.service_catalog_id', '=', 'service_catalogs.id')
                ->join('service_categories', 'service_catalogs.category_id', '=', 'service_categories.id')
                ->select('service_categories.name', DB::raw('count(*) as total'))
                ->groupBy('service_categories.id', 'service_categories.name')
                ->get()
                ->map(function ($item) {
                     return ['name' => $item->name, 'total' => $item->total];
                })->toArray();

             // Admin: Hits per Agency (ALL Agencies)
             $agencyHits = DB::table('agencies')
                ->leftJoin('users', 'agencies.id', '=', 'users.agency_id')
                ->leftJoin('api_logs', 'users.id', '=', 'api_logs.user_id')
                ->where('agencies.status', 'active')
                ->select('agencies.code', 'agencies.name', DB::raw('count(api_logs.id) as total'))
                ->groupBy('agencies.id', 'agencies.code', 'agencies.name')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

             $topStats['categories'] = $agencyHits->pluck('code')->toArray();
             $topStats['data'] = $agencyHits->pluck('total')->toArray();
             $topStats['names'] = $agencyHits->mapWithKeys(function ($item) {
                 return [$item->code => $item->name];
             })->toArray();

        } else {
             // User: Count approved services per category
             $categoryStats = DB::table('service_access_requests')
                ->join('service_catalogs', 'service_access_requests.service_catalog_id', '=', 'service_catalogs.id')
                ->join('service_categories', 'service_catalogs.category_id', '=', 'service_categories.id')
                ->where('service_access_requests.user_id', $user->id)
                ->where('service_access_requests.status', 'approved')
                ->select('service_categories.name', DB::raw('count(*) as total'))
                ->groupBy('service_categories.id', 'service_categories.name')
                ->get()
                ->map(function ($item) {
                    return ['name' => $item->name, 'total' => $item->total];
                })->toArray();

             // User: Hits per Category
             $categoryHitsStats = DB::table('api_logs')
                ->join('service_catalogs', 'api_logs.service_catalog_id', '=', 'service_catalogs.id')
                ->join('service_categories', 'service_catalogs.category_id', '=', 'service_categories.id')
                ->where('api_logs.user_id', $user->id)
                ->select('service_categories.name', DB::raw('count(*) as total'))
                ->groupBy('service_categories.id', 'service_categories.name')
                ->get()
                ->map(function ($item) {
                    return ['name' => $item->name, 'total' => $item->total];
                })->toArray();

             // User: Hits per Service Catalog (ALL Approved Services, including 0 hits)
             // Driver table must be service_access_requests (approved)
             $serviceHits = DB::table('service_access_requests')
                ->join('service_catalogs', 'service_access_requests.service_catalog_id', '=', 'service_catalogs.id')
                ->leftJoin('api_logs', function($join) use ($user) {
                    $join->on('service_catalogs.id', '=', 'api_logs.service_catalog_id')
                         ->where('api_logs.user_id', '=', $user->id);
                })
                ->where('service_access_requests.user_id', $user->id)
                ->where('service_access_requests.status', 'approved')
                ->select('service_catalogs.slug', 'service_catalogs.name', DB::raw('count(api_logs.id) as total'))
                ->groupBy('service_catalogs.id', 'service_catalogs.slug', 'service_catalogs.name')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

             $topStats['categories'] = $serviceHits->pluck('slug')->toArray();
             $topStats['data'] = $serviceHits->pluck('total')->toArray();
             $topStats['names'] = $serviceHits->mapWithKeys(function ($item) {
                 return [$item->slug => $item->name];
             })->toArray();
        }

        // 6. Recent Activities (Table Data)
        $recentActivities = DB::table('api_logs')
            ->join('users', 'api_logs.user_id', '=', 'users.id')
            ->leftJoin('agencies', 'users.agency_id', '=', 'agencies.id')
            ->join('service_catalogs', 'api_logs.service_catalog_id', '=', 'service_catalogs.id')
            ->select(
                'api_logs.*',
                'users.name as user_name',
                'users.email as user_email',
                'agencies.name as agency_name',
                'service_catalogs.name as service_name'
            )
            ->when(!$isAdmin, function($query) use ($user) {
                return $query->where('api_logs.user_id', $user->id);
            })
            ->orderByDesc('api_logs.created_at')
            ->limit(10)
            ->get();

        return view('content.dashboard.index', [
            'totalTransactions' => $totalTransactions,
            'successRate' => $successRate,
            'dailyTraffic' => $dailyTraffic,
            'totalInstansi' => $totalInstansi,
            'totalConnectedInstansi' => $totalConnectedInstansi,
            'totalLayanan' => $totalLayanan,
            'isAdmin' => $isAdmin,
            'categoryStats' => $categoryStats,
            'categoryHitsStats' => $categoryHitsStats,
            'topStats' => $topStats,
            'recentActivities' => $recentActivities,
            'announcements' => $announcements
        ]);
    }
}
