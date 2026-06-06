<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ApiClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
             // ... existing datatables logic ...
             if (auth()->user()->hasRole('admin')) {
                $clients = ApiClient::with(['user.agency', 'serviceCatalog'])->latest();
            } else {
                $clients = ApiClient::with('serviceCatalog')->where('user_id', auth()->id())->latest();
            }

            return DataTables::of($clients)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    return $row->user?->name ?? '-';
                })
                ->addColumn('user_agency', function ($row) {
                    return $row->user?->agency?->name ?? '-';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y H:i');
                })
                ->editColumn('status', function ($row) {
                    return '<span class="badge bg-label-success">Active</span>';
                })
                ->addColumn('action', function ($row) {
                    if (auth()->user()->hasRole('admin') || $row->user_id == auth()->id()) {
                        return '<button type="button" class="btn btn-sm btn-icon btn-label-danger delete-client-btn" title="Revoke Key" data-id="' . $row->id . '"><i class="ti ti-trash"></i></button>';
                    }
                    return '-';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        // [FILTER] Fetch Service Catalogs
        $catalogQuery = \App\Models\ServiceCatalog::where('status', 'active');

        // If not admin, restrict & attach permission info
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            // Fetch Approved Requests with Permission Info
            $accessRequests = \App\Models\ServiceAccessRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->get(['service_catalog_id', 'can_customize_mapping']);

            $approvedIds = $accessRequests->pluck('service_catalog_id');

            // Key-Value Pair: CatalogID => CanCustomize (boolean)
            $permissions = $accessRequests->pluck('can_customize_mapping', 'service_catalog_id')->toArray();

            $catalogQuery->whereIn('id', $approvedIds);

            $serviceCatalogs = $catalogQuery->get(['id', 'name', 'slug', 'requires_mapping', 'mapping_field'])->map(function($cat) use ($permissions) {
                $cat->can_customize_mapping = $permissions[$cat->id] ?? false;
                return $cat;
            });
        } else {
            // Admin can access all, assumes false for can_customize unless specified (or ignored for admin)
            $serviceCatalogs = $catalogQuery->get(['id', 'name', 'slug', 'requires_mapping', 'mapping_field']);
        }

        // Fetch List of Agencies for Dropdown (If needed for Super User)
        $agencies = \App\Models\Agency::where('status', 'active')->orderBy('name')->get(['code', 'name']);

        return view('content.user.api-clients.index', compact('serviceCatalogs', 'agencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'service_catalog_id' => 'required|exists:service_catalogs,id',
            'skpd_code' => 'nullable|string' // Validated as string, if allowed
        ]);

        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        // 1. Check Permission Check
        $canCustomize = false;
        if ($isAdmin) {
             $canCustomize = true;
        } else {
             $access = \App\Models\ServiceAccessRequest::where('user_id', $user->id)
                ->where('service_catalog_id', $request->service_catalog_id)
                ->where('status', 'approved')
                ->first();

             if (!$access) {
                 return response()->json(['message' => 'Anda tidak memiliki akses yang disetujui untuk layanan ini.'], 403);
             }
             $canCustomize = $access->can_customize_mapping;
        }

        // 2. Resolve Mapping Config
        $mappingConfig = [];

        if ($canCustomize) {
            // [Scenario B: Diskominfo]
            // If user provided a code, use it. If empty, it means "All Access" (Allowed).
            if ($request->filled('skpd_code')) {
                $mappingConfig = ['skpd_code' => $request->skpd_code];
            } else {
                // Explicitly set null/empty to indicate "No Mapping" (Free Pass)
                $mappingConfig = [];
            }
        } else {
            // [Scenario A: User Dinas]
            // FORCE Binding to User's Agency Code
            $agencyCode = $user->agency->code ?? null;
            if (!$agencyCode) {
                 return response()->json(['message' => 'Akun instansi Anda belum memiliki Kode SKPD. Hubungi Admin.'], 400);
            }
            $mappingConfig = ['skpd_code' => $agencyCode];
        }

        $creds = ApiClient::generateCredentials();

        $client = ApiClient::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'api_key' => $creds['api_key'],
            'secret_key' => $creds['secret_key'],
            'status' => 'active',
            'service_catalog_id' => $request->service_catalog_id,
            'mapping_config' => $mappingConfig
        ]);

        return response()->json([
            'message' => 'API Key Created Successfully.',
            'api_key' => $client->api_key,
            'secret_key' => $client->secret_key
        ]);
    }

    public function destroy($id)
    {
        ApiClient::where('user_id', auth()->id())->where('id', $id)->delete();
        return response()->json(['success' => 'API Key Revoked.']);
    }
}
