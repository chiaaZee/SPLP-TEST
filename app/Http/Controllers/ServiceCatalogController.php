<?php

namespace App\Http\Controllers;

use App\Models\ServiceCatalog;
use App\Models\ServiceEndpoint;
use App\Models\Agency;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class ServiceCatalogController extends Controller
{
    /**
     * Display a listing of the resource (Grid View).
     */
    public function index()
    {
        $agencies = Agency::where('status', 'active')->get();
        $categories = \App\Models\ServiceCategory::all();

        $customBreadcrumbs = [
            [
                'name' => 'Dashboard',
                'url' => route('dashboard'),
                'active' => false
            ],
            [
                'name' => 'Katalog Layanan',
                'url' => '',
                'active' => true
            ]
        ];

        return view('content.admin.service-catalogs.index', compact('agencies', 'customBreadcrumbs', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required|exists:agencies,id',
            'category_id' => 'nullable|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_url' => 'nullable|url',
            'target_token' => 'nullable|string',
            'rate_limit' => 'nullable|integer|min:1',
            'requires_mapping' => 'nullable|boolean',
            'mapping_api_url' => 'nullable|url',
            'mapping_field' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048', // Optional
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $input = $request->all();
        $input['slug'] = Str::slug($input['name']);

        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/assets/img/service-catalogs');
            $image->move($destinationPath, $name);
            $input['cover_image'] = $name;
        }

        $catalog = ServiceCatalog::create($input);
        $catalog->load('agency', 'category');

        $html = view('content.admin.service-catalogs._catalog_card', compact('catalog'))->render();

        return response()->json([
            'success' => 'Katalog berhasil dibuat.',
            'html' => $html
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * DEPRECATED: Migrated to Livewire Modal
     */
    /*
    public function edit($id)
    {
        $catalog = ServiceCatalog::where('slug', $id)->first() ?? ServiceCatalog::findOrFail($id);
        $agencies = Agency::where('status', 'active')->get();
        return view('content.admin.service-catalogs.edit', compact('catalog', 'agencies'));
    }
    */

    /**
     * Update the specified resource in storage.
     * DEPRECATED: Migrated to Livewire Modal
     */
    /*
    public function update(Request $request, $id)
    {
        $catalog = ServiceCatalog::where('slug', $id)->first() ?? ServiceCatalog::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'agency_id' => 'required|exists:agencies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_url' => 'nullable|url',
            'target_token' => 'nullable|string',
            'rate_limit' => 'nullable|integer|min:1',
            'requires_mapping' => 'nullable|boolean',
            'mapping_api_url' => 'nullable|url',
            'mapping_field' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $input = $request->only(['agency_id', 'name', 'description', 'status', 'base_url', 'target_token', 'rate_limit', 'requires_mapping', 'mapping_api_url', 'mapping_field']);
        $input['slug'] = Str::slug($input['name']);

        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($catalog->cover_image && file_exists(public_path('/assets/img/service-catalogs/' . $catalog->cover_image))) {
                unlink(public_path('/assets/img/service-catalogs/' . $catalog->cover_image));
            }
            $image = $request->file('cover_image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/assets/img/service-catalogs');
            $image->move($destinationPath, $name);
            $input['cover_image'] = $name;
        }

        $catalog->update($input);

        return redirect()->route('service-catalogs.show', $catalog->slug)->with('success', 'Katalog berhasil diperbarui!');
    }
    */

    /**
     * Toggle catalog status (active/inactive)
     */
    /**
     * Toggle catalog status (active/inactive)
     * DEPRECATED: Migrated to Livewire
     */
    /*
    public function toggleStatus($id)
    {
        $catalog = ServiceCatalog::where('slug', $id)->first() ?? ServiceCatalog::findOrFail($id);
        $catalog->status = $catalog->status == 'active' ? 'inactive' : 'active';
        $catalog->save();

        return response()->json([
            'success' => true,
            'status' => $catalog->status,
            'message' => 'Status katalog berhasil diubah menjadi ' . ($catalog->status == 'active' ? 'Aktif' : 'Nonaktif')
        ]);
    }
    */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $catalog = ServiceCatalog::where('slug', $id)->first() ?? ServiceCatalog::findOrFail($id);

        // Delete cover image if exists
        if ($catalog->cover_image && file_exists(public_path('/assets/img/service-catalogs/' . $catalog->cover_image))) {
            @unlink(public_path('/assets/img/service-catalogs/' . $catalog->cover_image));
        }

        $catalog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Katalog berhasil dihapus.'
        ]);
    }

    /**
     * Display the specified resource (Detail View with Endpoints).
     */
    public function show($id, Request $request)
    {
        // 1. Resolve Catalog (Manual to support Slug/ID)
        $serviceCatalog = ServiceCatalog::with('agency')->where('slug', $id)->first();
        if (!$serviceCatalog) {
            $serviceCatalog = ServiceCatalog::with('agency')->find($id);
        }
        if (!$serviceCatalog) abort(404);

        // [SECURITY] Check Status
        if ($serviceCatalog->status !== 'active' && !auth()->user()->can('manage_catalogs')) {
            abort(404); // Hide inactive catalogs from plain view
        }

        // Check Access
        $hasAccess = true;
        $pendingRequest = null;
        $rejectionNote = null;
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $isOwner = $serviceCatalog->user_id == $user->id;

        if (!$isAdmin && !$isOwner) {
             // Check if user has explicit permission via ServiceAccessRequest
             $access = \App\Models\ServiceAccessRequest::where('user_id', $user->id)
                 ->where('service_catalog_id', $serviceCatalog->id)
                 ->latest()
                 ->first();

             if ($access && $access->status == 'approved') {
                 $hasAccess = true;
             } elseif ($access && $access->status == 'pending') {
                 $pendingRequest = $access;
                 $hasAccess = false;
             } elseif ($access && $access->status == 'rejected') {
                 $rejectionNote = $access;
                 $hasAccess = false;
             } else {
                 $hasAccess = false;
             }
        }

        // Fetch Endpoints
        $query = $serviceCatalog->endpoints();

        if ($request->has('search') && $request->search != '') {
             $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('url', 'like', '%' . $request->search . '%');
             });
        }

        // Filter Visibility
        if (!$isAdmin && !$hasAccess) {
             $query->where('is_public', true);
        }

        $endpoints = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return view('content.admin.service-catalogs._endpoints_list', compact('endpoints'))->render();
        }

        // Stats
        $stats = $this->getCatalogStats($serviceCatalog, $isAdmin);

        // Chart Data
        $chartData = $this->getChartData($serviceCatalog, $isAdmin);

        // Fetch Clients for Impersonation (Admin sees all, User sees own)
        $clients = [];
        if ($hasAccess || $isAdmin) {
            $clientsQuery = \App\Models\ApiClient::where('status', 'active')
                ->where(function($q) use ($serviceCatalog) {
                    $q->where('service_catalog_id', $serviceCatalog->id)
                      ->orWhereNull('service_catalog_id');
                });

            if (!$isAdmin) {
                // Non-admin can only see their own clients
                $clientsQuery->where('user_id', auth()->id());
            }

            $clients = $clientsQuery->get(['id', 'name', 'api_key', 'mapping_config']);
        }

        $catalog = $serviceCatalog;

        // Custom Breadcrumbs (if needed, though layout might handle it)
        $customBreadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('dashboard'), 'active' => false],
            ['name' => 'Katalog Layanan', 'url' => route('service-catalogs.index'), 'active' => false],
            ['name' => $catalog->name, 'url' => '', 'active' => true]
        ];

        return view('content.admin.service-catalogs.show', compact('catalog', 'endpoints', 'hasAccess', 'pendingRequest', 'rejectionNote', 'stats', 'chartData', 'clients', 'customBreadcrumbs'));
    }

    /**
     * Store Endpoint
     */
    /**
     * Store Endpoint
     * DEPRECATED: Migrated to Livewire
     */
    /*
    public function storeEndpoint(Request $request)
    {
        // 1. Get Catalog to check Base URL
        $catalog = ServiceCatalog::findOrFail($request->service_catalog_id);

        // 2. Adjust input URL if Base URL exists and input is not full URL
        // If user typed just path '/users' and base_url exists, we prepend it.
        // We do this BEFORE validation so validation passes 'url' rule.
        if ($catalog->base_url && !filter_var($request->url, FILTER_VALIDATE_URL)) {
            // Ensure base_url ends with / and path doesn't start with / to avoid double slash, or standard join
            $baseUrl = rtrim($catalog->base_url, '/');
            $path = ltrim($request->url, '/');
            $request->merge(['url' => $baseUrl . '/' . $path]);
        }

        $validator = Validator::make($request->all(), [
            'service_catalog_id' => 'required|exists:service_catalogs,id',
            'name' => 'required|string|max:255',
            'method' => 'required|in:GET,POST,PUT,DELETE,PATCH',
            'path' => 'required|string|regex:/^\//|max:255', // Must start with /
            'url' => 'required|url',
            'request_body' => 'nullable|string',
            'is_public' => 'boolean', // Optional, default true if missing
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $input = $request->all();
        $slug = \Illuminate\Support\Str::slug($request->name);
        $count = 1;
        while (\App\Models\ServiceEndpoint::where('slug', $slug)->exists()) {
            $slug = \Illuminate\Support\Str::slug($request->name) . '-' . $count++;
        }
        $input['slug'] = $slug;

        $endpoint = ServiceEndpoint::create($input);

        // Pass catalog to view for Gateway URL generation
        $html = view('content.admin.service-catalogs._endpoint_item', compact('endpoint', 'catalog'))->render();

        return response()->json([
            'success' => 'Endpoint added successfully.',
            'html' => $html
        ]);
    }
    */

    /**
     * Update Endpoint
     */
    /**
     * Update Endpoint
     * DEPRECATED: Migrated to Livewire
     */
    /*
    public function updateEndpoint(Request $request, $id)
    {
        $endpoint = ServiceEndpoint::findOrFail($id);
        $catalog = $endpoint->catalog; // Eager load? Or assuming relationship exists.

        // Adjust input URL if Base URL exists and input is not full URL
        if ($catalog->base_url && !filter_var($request->url, FILTER_VALIDATE_URL)) {
            $baseUrl = rtrim($catalog->base_url, '/');
            $path = ltrim($request->url, '/');
            $request->merge(['url' => $baseUrl . '/' . $path]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'method' => 'required|in:GET,POST,PUT,DELETE,PATCH',
            'path' => 'required|string|regex:/^\//|max:255',
            'url' => 'required|url',
            'request_body' => 'nullable|string',
            'is_public' => 'required|boolean' // Add validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $input = $request->all();
        $slug = \Illuminate\Support\Str::slug($request->name);
        $count = 1;
        while (\App\Models\ServiceEndpoint::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = \Illuminate\Support\Str::slug($request->name) . '-' . $count++;
        }
        $input['slug'] = $slug;

        $endpoint->update($input);

        // Re-render item with catalog context
        $html = view('content.admin.service-catalogs._endpoint_item', compact('endpoint', 'catalog'))->render();

        return response()->json([
            'success' => 'Endpoint updated successfully.',
            'html' => $html,
            'id' => $id
        ]);
    }
    */

    /**
     * Delete Endpoint
     * DEPRECATED: Migrated to Livewire
     */
    /*
    public function destroyEndpoint($id)
    {
        ServiceEndpoint::find($id)->delete();
        return response()->json(['success' => 'Endpoint deleted.']);
    }
    */

    /**
     * Show Endpoint Detail Page
     */
    /**
     * Show Endpoint Detail Page
     */
    public function showEndpointDetail($catalogSlug, $id)
    {
        // Support ID or Slug
        $endpoint = ServiceEndpoint::with('catalog')->where('slug', $id)->orWhere('id', $id)->firstOrFail();

        // Optional: Verify Catalog Match
        if ($endpoint->catalog->slug !== $catalogSlug) {
             // Redirect to correct URL if mismatch (Canonical)
             return redirect()->route('service-catalogs.endpoint.detail', ['catalog' => $endpoint->catalog->slug, 'id' => $endpoint->slug]);
        }

        // [SECURITY] Check Visibility Check (Consistent with Show List)
        if (!auth()->user()->hasRole('admin') && !$endpoint->is_public) {
            abort(404); // Hide it completely as if it doesn't exist
        }

        $customBreadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('dashboard'), 'active' => false],
            ['name' => 'Katalog Layanan', 'url' => route('service-catalogs.index'), 'active' => false],
            ['name' => $endpoint->catalog->name, 'url' => route('service-catalogs.show', $endpoint->catalog->slug), 'active' => false],
            ['name' => 'Detail Endpoint', 'url' => '#', 'active' => true],
        ];

        // [NEW] Fetch Clients for Impersonation (Admin sees all, User sees own)
        $clients = [];
        $user = auth()->user();

        // Define access (simplified check, ideal would be to reuse same check as show)
        $isAdmin = $user->hasRole('admin');
        // Basic check if they can view detail, they probably have access.
        // We tighten the client list purely by ownership.

        $clientsQuery = \App\Models\ApiClient::where('status', 'active')
            ->where(function($q) use ($endpoint) {
                $q->where('service_catalog_id', $endpoint->catalog->id)
                  ->orWhereNull('service_catalog_id');
            });

        if (!$isAdmin) {
             $clientsQuery->where('user_id', $user->id);
        }

        $clients = $clientsQuery->get(['id', 'name', 'api_key', 'mapping_config']);

        return view('content.admin.service-catalogs.endpoint_detail', compact('endpoint', 'customBreadcrumbs', 'clients'));
    }

    /**
     * Test Endpoint (Proxy)
     */
    public function testEndpoint(Request $request, $catalogSlug, $id)
    {
        $endpoint = ServiceEndpoint::where('slug', $id)->orWhere('id', $id)->firstOrFail();

        // [SECURITY] Check Catalog Status
        if ($endpoint->catalog->status !== 'active' && !auth()->user()->can('manage_catalogs')) {
             return response()->json([
                 'success' => false,
                 'status' => 403,
                 'status_text' => 'Forbidden',
                 'body' => 'Layanan ini sedang tidak aktif (Inactive) dan tidak dapat diuji.',
                 'duration' => '0 ms'
             ]);
        }

        try {
            $startTime = microtime(true);

            // Prepare inputs
            $method = $request->input('method', $endpoint->method);
            $url = $endpoint->url; // Target URL
            $body = $request->input('body');
            $headers = $request->input('headers', []);
            $manualQueryParams = $request->input('query_params');
            $pathSuffix = $request->input('path_suffix');

            // [LOGIC] Append Path Suffix (e.g. /123)
            if (!empty($pathSuffix)) {
                // Ensure starts with / if not present, but handle careful concatenation
                $url = rtrim($url, '/') . '/' . ltrim($pathSuffix, '/');
            }

            // [LOGIC] Append Manual Query Params
            if (!empty($manualQueryParams)) {
                $url .= (strpos($url, '?') !== false ? '&' : '?') . $manualQueryParams;
            }

            // [LOGIC] Auto-Inject Filter (Simulation of Gateway)
            $impersonateClientId = $request->input('impersonate_client_id');
            $appliedImpersonation = false;

            if ($impersonateClientId) {
                 $client = \App\Models\ApiClient::find($impersonateClientId);
                 // Security Check: Allow if Admin OR if Client belongs to User
                 $isOwner = $client && $client->user_id == auth()->id();
                 $isAdmin = auth()->user()->can('manage_catalogs');

                 if ($client && ($isAdmin || $isOwner)) {
                     // 1. Simulator Mode: Use Client's Mapping
                     if (!empty($client->mapping_config)) {
                          $mappingConfig = $client->mapping_config;
                          // Inject all params from mapping_config
                          foreach ($mappingConfig as $key => $value) {
                               if (empty($value)) continue;
                               $url .= (strpos($url, '?') !== false ? '&' : '?') . "$key=$value";
                          }
                     }
                     $appliedImpersonation = true;
                 }
            }

            if (!$appliedImpersonation) {
                 // 2. Default Mode: Use User's Agency Legacy Mapping (if any and not using Impersonation)
                 $user = auth()->user();
                 if ($user && $user->agency && !empty($user->agency->external_ids)) {
                    $slug = $endpoint->catalog->slug;
                    // Check if mapping exists for this service
                    if (isset($user->agency->external_ids[$slug])) {
                        $autoParam = 'skpd_id=' . $user->agency->external_ids[$slug];
                        $url .= (strpos($url, '?') !== false ? '&' : '?') . $autoParam;
                    }
                 }
            }

            // Normalize headers
            $requestHeaders = collect($headers)->pluck('value', 'key')->toArray();

            // Add Authorization from User Input OR Configured Target Token
            if ($request->input('auth_token')) {
                $requestHeaders['Authorization'] = 'Bearer ' . $request->input('auth_token');
            } elseif ($endpoint->catalog->target_token) {
                // Auto-inject configured token if user didn't provide one
                $requestHeaders['Authorization'] = 'Bearer ' . $endpoint->catalog->target_token;
            }

            // Client Configuration
            // [Fix] Add Custom Header for Cloudflare Bypass
             if (isset($requestHeaders['Authorization'])) {
                  $parts = explode(' ', $requestHeaders['Authorization']);
                  if (count($parts) > 1) {
                      $requestHeaders['X-Splpd-Token'] = $parts[1];
                  }
             }

            $http = Http::withHeaders($requestHeaders)
                ->withOptions(['verify' => false, 'timeout' => 30]);

            // Execute Request
            if (!empty($body) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
                // Parse body if it's a JSON string
                $jsonBody = is_string($body) ? json_decode($body, true) : $body;
                $response = $http->send($method, $url, ['json' => $jsonBody]);
            } else {
                $response = $http->send($method, $url);
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => $response->successful(),
                'status' => $response->status(),
                'status_text' => $response->reason(),
                'headers' => $response->headers(),
                'body' => $response->json() ?? $response->body(), // Try JSON, else raw
                'duration' => $duration . ' ms'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'status_text' => 'Internal Proxy Error',
                'body' => $e->getMessage(),
                'duration' => '0 ms'
            ]);
        }
    }
    /**
     * Handle Access Request
     */
    public function requestAccess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_catalog_id' => 'required|exists:service_catalogs,id',
            'attachment' => 'required|file|mimes:pdf,doc,docx,jpg,png,zip|max:5120', // Max 5MB
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal. Mohon lampirkan dokumen surat permohonan.'], 422);
        }

        // Check if pending request exists
        $exists = \App\Models\ServiceAccessRequest::where('user_id', auth()->id())
            ->where('service_catalog_id', $request->service_catalog_id)
            ->whereIn('status', ['pending_owner', 'pending_admin', 'approved'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah memiliki permintaan pending atau sudah disetujui untuk layanan ini.'], 422);
        }

        $filename = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . Str::slug(auth()->user()->name) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/access_requests');
            if (!file_exists($destinationPath))
                mkdir($destinationPath, 0777, true);
            $file->move($destinationPath, $filename);
        }

        $catalog = \App\Models\ServiceCatalog::find($request->service_catalog_id);
        $owner = $catalog->user;
        $hasDinasOwner = $owner && $owner->role !== 'admin';
        $status = $hasDinasOwner ? 'pending_owner' : 'pending_admin';

        $accessRequest = \App\Models\ServiceAccessRequest::create([
            'user_id' => auth()->id(),
            'service_catalog_id' => $request->service_catalog_id,
            'reason' => $request->reason ?? 'Lampiran Dokumen',
            'attachment' => $filename,
            'status' => $status
        ]);

        // Send notifications
        if ($status === 'pending_owner') {
            if ($catalog->user) {
                $catalog->user->notify(new \App\Notifications\AccessRequestNotification($accessRequest, 'new_request'));
            }
        } else {
            // Notify Admin
            $admins = \App\Models\User::role('admin')->get();
            if (!method_exists($admins, 'notify') && $admins->isEmpty()) {
                $admins = \App\Models\User::where('role', 'admin')->get();
            }
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AccessRequestNotification($accessRequest, 'new_request_admin'));
            }
        }

        return response()->json(['message' => 'Dokumen permohonan berhasil dikirim. Admin akan meninjau permintaan Anda.']);
    }
    private function getCatalogStats($catalog, $isAdmin)
    {
        // 1. General Stats (Last 7 Days)
        $logsQuery = \App\Models\ApiLog::where('service_catalog_id', $catalog->id)
            ->where('created_at', '>=', now()->subDays(7));

        $totalHits = (clone $logsQuery)->count();
        $errorCount = (clone $logsQuery)->where('status_code', '>=', 400)->count();
        $errorRate = $totalHits > 0 ? round(($errorCount / $totalHits) * 100, 1) : 0;

        // 2. Health Status (Last 24 Hours) - Matches Monitoring API
        $logsQuery24h = \App\Models\ApiLog::where('service_catalog_id', $catalog->id)
            ->where('created_at', '>=', now()->subDay());

        $hits24h = (clone $logsQuery24h)->count();
        $errors24h = (clone $logsQuery24h)->where('status_code', '>=', 400)->count();
        $errorRate24h = $hits24h > 0 ? ($errors24h / $hits24h) * 100 : 0;

        $usersCount = 0;
        if ($isAdmin) {
            $usersCount = \App\Models\ServiceAccessRequest::where('service_catalog_id', $catalog->id)->where('status', 'approved')->count();
        }

        $lastUsed = null;
        if (!$isAdmin) {
             $lastLog = \App\Models\ApiLog::where('service_catalog_id', $catalog->id)
                ->where('user_id', auth()->id())
                ->latest()
                ->first();
             $lastUsed = $lastLog ? $lastLog->created_at : null;
        }

        // Health Status Logic (Based on 24h)
        if ($hits24h === 0) {
            $healthStatus = 'No Data';
            $healthColor = 'secondary';
            $healthIcon = 'help';
        } elseif ($errorRate24h >= 10) {
            $healthStatus = 'Down'; // >10% errors
            $healthColor = 'danger';
            $healthIcon = 'alert-triangle';
        } elseif ($errorRate24h >= 1) {
            $healthStatus = 'Issues'; // 1-10% errors
            $healthColor = 'warning';
            $healthIcon = 'alert-circle';
        } else {
            $healthStatus = 'Healthy'; // <1% errors
            $healthColor = 'success';
            $healthIcon = 'heart-rate-monitor';
        }

        return [
            'total_hits' => $totalHits,
            'error_rate' => $errorRate,
            'users_count' => $usersCount,
            'last_used' => $lastUsed,
            'health_status' => $healthStatus,
            'health_color' => $healthColor,
            'health_icon' => $healthIcon
        ];
    }

    private function getChartData($catalog, $isAdmin)
    {
        $logsQuery = \App\Models\ApiLog::where('service_catalog_id', $catalog->id)
            ->where('created_at', '>=', now()->subDays(7));

        $rawLogs = (clone $logsQuery)
            ->with('user:id,name,email')
            ->get()
            ->map(function ($log) use ($isAdmin) {
                return [
                    'x' => $log->created_at->format('Y-m-d'),
                    'y' => floatval($log->created_at->format('H')) + (floatval($log->created_at->format('i')) / 60),
                    'endpoint' => $log->method . ' ' . $log->endpoint,
                    'user' => $isAdmin ? ($log->user->name ?? 'Unknown') : null
                ];
            });

        $chartSeries = $rawLogs->groupBy('endpoint')->map(function ($items, $endpointName) {
            return [
                'name' => $endpointName,
                'data' => $items->values()->all()
            ];
        })->values()->all();

        return [
            'series' => $chartSeries
        ];
    }
    public function getReferences($id)
    {
        $catalog = ServiceCatalog::where('slug', $id)->first() ?? ServiceCatalog::findOrFail($id);

        // Use Configured Mapping URL if available, otherwise default fallback (legacy)
        $url = $catalog->mapping_api_url ?? (rtrim($catalog->base_url, '/') . '/api/splpd/v1/skpd');

        try {
            $headers = ['Accept' => 'application/json'];
            if ($catalog->target_token) {
                 $headers['Authorization'] = 'Bearer ' . $catalog->target_token;
            }

            // We use timeout 5s for UI responsiveness
            $response = Http::withHeaders($headers)->timeout(5)->get($url);

            if ($response->successful()) {
                 $json = $response->json();

                 // Extract list from 'data' key if present (Standard API Wrapper)
                 $items = $json['data'] ?? $json;

                 if (is_array($items)) {
                     $results = [];
                     foreach ($items as $item) {
                         // Skip if not array/object
                         if (!is_array($item) && !is_object($item)) continue;
                         $item = (array)$item;

                         $results[] = [
                             'id' => $item['id'] ?? null,
                             'text' => $item['nama_pd'] ?? $item['name'] ?? $item['text'] ?? 'Unknown'
                         ];
                     }
                     return response()->json($results);
                 }

                 return response()->json($items); // Fallback
            }

            return response()->json([], 200); // Empty on fail/404

        } catch (\Exception $e) {
            return response()->json([], 200); // Fail silently for UI
        }
    }
}
