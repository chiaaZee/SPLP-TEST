<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ServiceCatalog;
use App\Models\ServiceEndpoint;
use App\Models\Agency;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class ServiceCatalogDetail extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Catalog Data
    public $slug;
    public $catalog;

    // Search & Filter
    public $search = '';
    public $perPage = 10;

    // Endpoint Form Data
    public $endpointId = null;
    public $name = '';
    public $method = 'GET';
    public $path = '';
    public $url = '';
    public $request_body = '';
    public $is_public = true; // Default true (Visibility)
    public $auth_mode = 'required'; // Security
    public $description = '';

    // Modal State
    public $isEditMode = false;

    // View Data
    public $stats = [];
    public $chartData = [];
    public $catalogData = [];
    public $hasAccessOrAdmin = false;
    public $agencies = [];
    public $categories = [];

    // Catalog Edit Form Data
    public $edit_agency_id;
    public $edit_category_id;
    public $edit_name;
    public $edit_description;
    public $edit_base_url;
    public $edit_target_token;
    public $edit_rate_limit;
    public $edit_requires_mapping = false;
    public $edit_mapping_api_url;
    public $edit_mapping_field;
    public $edit_status;
    public $edit_rejection_reason;
    public $edit_auth_mode;
    public $new_cover_image;
    // UAT Document
    public $uat_document;
    public $existing_uat_document;
    public $isCatalogEditMode = false;

    // Connected Agencies Modal Data
    public $connectedAgencies = [];
    public $agencyStatuses = [];

    public function openConnectedAgenciesModal()
    {
        // 1. Get approved access requests (User Based)
        $accessRequests = \App\Models\ServiceAccessRequest::where('service_catalog_id', $this->catalog->id)
            ->where('status', 'approved')
            ->with(['user.agency'])
            ->get();

        $agencyMap = [];

        // A. From Users
        foreach ($accessRequests as $req) {
            if ($req->user && $req->user->agency) {
                $agencyId = $req->user->agency->id;
                if (!isset($agencyMap[$agencyId])) {
                    $agencyMap[$agencyId] = $req->user->agency;
                }
            }
        }

        // B. From API Clients (Mapped SKPD) linked to this service OR Global
        // Logic: Find clients that have used this service (via Logs) OR are bound to it.
        // For "Connected" definition, strict "Usage" is better, but for "Access List", Binding is better.
        // Dashboard uses "Logs" for "Connected". Here "Instansi Pengguna Layanan" implies Access.
        // Let's use Binding (Access) + Usage status.

        $boundClients = \App\Models\ApiClient::where(function($q) {
                $q->where('service_catalog_id', $this->catalog->id)
                  ->orWhereNull('service_catalog_id'); // Global Keys can access too, count them?
            })
            ->where('status', 'active')
            ->get();

        // Filter Global Clients? Usually purely global keys might not be "Users" of this specific service unless logged.
        // But if they are bound to a specific Dinas via mapping, they are potential users.
        // Let's check Logs to be sure they are relevant "Connected" agencies, or include if explicit binding.
        // If "Global" key with Mapping, they effectively have access.

        $agencyCodeMap = Agency::where('status', 'active')->pluck('id', 'code')->toArray();
        $allAgencies = Agency::where('status', 'active')->get()->keyBy('id');

        foreach ($boundClients as $client) {
             if (!empty($client->mapping_config['skpd_code'])) {
                 $codes = $client->mapping_config['skpd_code'];
                 if (!is_array($codes)) {
                     $codes = [$codes];
                 }
                 foreach ($codes as $code) {
                     if (is_scalar($code) && isset($agencyCodeMap[$code])) {
                          $agencyId = $agencyCodeMap[$code];
                          if (!isset($agencyMap[$agencyId]) && isset($allAgencies[$agencyId])) {
                              $agencyMap[$agencyId] = $allAgencies[$agencyId];
                          }
                     }
                 }
             }
        }

        $this->connectedAgencies = array_values($agencyMap);

        // 3. Check Status (Latest Log for each agency)
        $this->agencyStatuses = [];
        foreach ($this->connectedAgencies as $agency) {
            // Find latest log for this service from ANY user/client of this agency
            // 1. Logs by Users of Agency
            $latestLogUser = \App\Models\ApiLog::where('service_catalog_id', $this->catalog->id)
                ->whereHas('user', function ($q) use ($agency) {
                    $q->where('agency_id', $agency->id);
                })
                ->latest()
                ->first();

            // 2. Logs by Mapped Clients of Agency
            // Find Client IDs mapped to this agency
            $mappedClientIds = \App\Models\ApiClient::whereJsonContains('mapping_config->skpd_code', $agency->code)->pluck('id');

            $latestLogClient = null;
            if ($mappedClientIds->isNotEmpty()) {
                $latestLogClient = \App\Models\ApiLog::where('service_catalog_id', $this->catalog->id)
                    ->whereIn('api_client_id', $mappedClientIds)
                    ->latest()
                    ->first();
            }

            // Compare
            $latestLog = $latestLogUser;
            if ($latestLogClient) {
                if (!$latestLog || $latestLogClient->created_at > $latestLog->created_at) {
                    $latestLog = $latestLogClient;
                }
            }

            if ($latestLog) {
                // Consider 2xx as connected, others as disconnected (or error)
                $isConnected = $latestLog->status_code >= 200 && $latestLog->status_code < 300;
                $this->agencyStatuses[$agency->id] = [
                    'status' => $isConnected ? 'connected' : 'disconnected',
                    'last_check' => $latestLog->created_at->toIso8601String(),
                    'code' => $latestLog->status_code
                ];
            } else {
                $this->agencyStatuses[$agency->id] = [
                    'status' => 'unknown',
                    'last_check' => null,
                    'code' => null
                ];
            }
        }

        $this->dispatch('open-connected-agencies-modal');
    }

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->loadCatalog();
        $this->agencies = Agency::all();
        // Assuming ServiceCategory exists, otherwise we'll check
        if (class_exists(\App\Models\ServiceCategory::class)) {
            $this->categories = \App\Models\ServiceCategory::all();
        }

        // Check permissions
        $user = auth()->user();
        $this->hasAccessOrAdmin = $user->hasRole('admin') || ($this->catalog->agency_id == $user->agency_id);
    }

    public function loadCatalog()
    {
        $this->catalog = ServiceCatalog::where('slug', $this->slug)->firstOrFail();
        $this->catalogData = $this->catalog->load(['agency', 'category'])->toArray();

        // Load stats
        // 1. Total Hits
        $totalHits = \App\Models\ApiLog::where('service_catalog_id', $this->catalog->id)->count();

        // 2. Connected Agencies
        // Reuse logic from openConnectedAgenciesModal but simpler/optimized for count
        // Or just lazy load?
        // Let's do a quick count approximation or full logic if needed.
        // A. From Logs (Actual usage) is usually better for "Connected" metric on dashboard card.
        // Dashboard Stats uses: Logs 200-299.

        $userIds = \App\Models\ApiLog::where('service_catalog_id', $this->catalog->id)
            ->whereBetween('status_code', [200, 299])
            ->distinct()
            ->pluck('user_id');

        $connectedAgencyIds = \App\Models\User::whereIn('id', $userIds)
            ->whereNotNull('agency_id')
            ->distinct()
            ->pluck('agency_id')
            ->toArray();

        $clientIds = \App\Models\ApiLog::where('service_catalog_id', $this->catalog->id)
            ->whereBetween('status_code', [200, 299])
            ->whereNotNull('api_client_id')
            ->distinct()
            ->pluck('api_client_id');

        if ($clientIds->isNotEmpty()) {
            $clients = \App\Models\ApiClient::whereIn('id', $clientIds)->get();
            $agencyCodeMap = Agency::where('status', 'active')->pluck('id', 'code')->toArray();
            foreach ($clients as $client) {
                if (!empty($client->mapping_config['skpd_code'])) {
                    $codes = $client->mapping_config['skpd_code'];
                    if (!is_array($codes)) {
                        $codes = [$codes];
                    }
                    foreach ($codes as $code) {
                        if (is_scalar($code) && isset($agencyCodeMap[$code])) {
                             $connectedAgencyIds[] = $agencyCodeMap[$code];
                        }
                    }
                }
            }
        }
        $usersCount = count(array_unique($connectedAgencyIds)); // Instansi Terhubung

        $usersCount = $this->catalog->connected_agencies_count; // Refactored to use attribute too in previous step implicitly, or we can use it here explicitly. Use model attribute we made in step 440? Actually step 440 made connectedAgenciesCount. Let's use it if we want, but the inline code was:
        // $usersCount logic...
        // Actually for this step I am just replacing health logic.
        // Let's replace the whole stats block to be clean.

        $healthStats = $this->catalog->health_stats; // Uses 24h window

        $this->stats = [
            'total_hits' => $totalHits, // Keep total hits all time
            'users_count' => $usersCount,
            'health_status' => $healthStats['status'],
            'health_color' => $healthStats['color'],
            'health_icon' => $healthStats['icon'],
            'last_used' => \App\Models\ApiLog::where('service_catalog_id', $this->catalog->id)->latest()->value('created_at')
        ];
    }

    public function confirmToggleStatus()
    {
        $action = $this->catalog->status === 'active' ? 'nonaktifkan' : 'aktifkan';
        $color = $this->catalog->status === 'active' ? 'warning' : 'success';

        $this->dispatch('swal:confirm', [
            'type' => $color,
            'title' => 'Konfirmasi Status',
            'text' => "Apakah Anda yakin ingin $action layanan ini?",
            'confirmText' => "Ya, $action!",
            'method' => 'toggleStatus',
        ]);
    }

    #[On('toggleStatus')]
    public function toggleStatus()
    {
        $newStatus = $this->catalog->status === 'active' ? 'inactive' : 'active';
        $this->catalog->update(['status' => $newStatus]);

        $this->loadCatalog(); // Refresh data

        $this->dispatch('swal:toast', type: 'success', message: 'Status layanan berhasil diperbarui.');
    }

    // Catalog Management
    public function editCatalog()
    {
        $this->edit_agency_id = $this->catalog->agency_id;
        $this->edit_category_id = $this->catalog->category_id;
        $this->edit_name = $this->catalog->name;
        $this->edit_description = $this->catalog->description;
        $this->edit_base_url = $this->catalog->base_url;
        $this->edit_target_token = $this->catalog->target_token;
        $this->edit_rate_limit = $this->catalog->rate_limit;
        $this->edit_requires_mapping = (bool) $this->catalog->requires_mapping;
        $this->edit_mapping_api_url = $this->catalog->mapping_api_url;
        $this->edit_mapping_field = $this->catalog->mapping_field;
        $this->edit_status = $this->catalog->status;
        $this->edit_rejection_reason = $this->catalog->rejection_reason;
        $this->edit_auth_mode = $this->catalog->auth_mode ?? 'required';
        $this->new_cover_image = null;
        $this->uat_document = null;
        $this->existing_uat_document = $this->catalog->uat_document_path;

        $this->dispatch('open-catalog-modal');
    }

    public function updateCatalog()
    {
        $validated = $this->validate([
            'edit_agency_id' => 'required|exists:agencies,id',
            'edit_category_id' => 'nullable|exists:service_categories,id',
            'edit_name' => 'required|string|max:255',
            'edit_description' => 'nullable|string',
            'edit_base_url' => 'nullable|url',
            'edit_target_token' => 'nullable|string',
            'edit_rate_limit' => 'nullable|integer|min:1',
            'edit_requires_mapping' => 'boolean',
            'edit_mapping_api_url' => 'nullable|url',
            'edit_mapping_field' => 'nullable|string',
            'edit_status' => 'required|in:active,inactive,pending,rejected',
            'edit_rejection_reason' => 'required_if:edit_status,rejected|nullable|string',
            'edit_auth_mode' => 'required|in:required,none',
            'new_cover_image' => 'nullable|image|max:2048',
            'uat_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB Max
        ]);

        $updateData = [
            'agency_id' => $validated['edit_agency_id'],
            'category_id' => $validated['edit_category_id'],
            'name' => $validated['edit_name'],
            'description' => $validated['edit_description'],
            'base_url' => $validated['edit_base_url'],
            'target_token' => $validated['edit_target_token'],
            'rate_limit' => $validated['edit_rate_limit'],
            'requires_mapping' => $validated['edit_requires_mapping'],
            'mapping_api_url' => $validated['edit_mapping_api_url'],
            'mapping_field' => $validated['edit_mapping_field'],
            'status' => $validated['edit_status'],
            'rejection_reason' => $validated['edit_rejection_reason'],
            'auth_mode' => $validated['edit_auth_mode'],
            'slug' => Str::slug($validated['edit_name']),
        ];

        // Handle UAT Document Upload
        if ($this->uat_document) {
             if ($this->catalog->uat_document_path && \Storage::disk('public')->exists($this->catalog->uat_document_path)) {
                \Storage::disk('public')->delete($this->catalog->uat_document_path);
            }
            $updateData['uat_document_path'] = $this->uat_document->store('uat-docs', 'public');
        }

        if ($this->new_cover_image) {
            // Delete old image
            if ($this->catalog->cover_image && file_exists(public_path('/assets/img/service-catalogs/' . $this->catalog->cover_image))) {
                @unlink(public_path('/assets/img/service-catalogs/' . $this->catalog->cover_image));
            }

            $name = time() . '.' . $this->new_cover_image->getClientOriginalExtension();
            // Store locally temporarily then move manually as per existing logic, or use storeAs if disk configured.
            // Keeping consistent with previous block I rote in Step 4483
             $destinationPath = public_path('/assets/img/service-catalogs');
            $this->new_cover_image->storeAs('.', $name, 'service_catalogs_upload');
            $path = $this->new_cover_image->getRealPath();
             if (!file_exists($destinationPath))
                mkdir($destinationPath, 0777, true);
            copy($path, $destinationPath . '/' . $name);

            $updateData['cover_image'] = $name;
        }

        $this->catalog->update($updateData);

        // Refresh Data
        $this->loadCatalog();
        $this->catalogData = $this->catalog->load('agency')->toArray(); // Refresh View Array
        $this->slug = $this->catalog->slug; // Reset slug in case valid name changed

        $this->dispatch('close-catalog-modal'); // You'll need to listen to this in view
        $this->dispatch('swal:toast', type: 'success', message: 'Katalog berhasil diperbarui!');

        // If slug changed, we might need to redirect, but for now we update in place.
        // Update URL history if desired? Livewire supports query string updates but not full path usually.
        // If critical, we redirect.
        if ($this->catalog->wasChanged('slug')) {
             return redirect()->route('service-catalogs.show', $this->catalog->slug);
        }
    }

    // Modal Management
    public function createEndpoint()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->dispatch('open-endpoint-modal');
    }

    public function editEndpoint($id)
    {
        $endpoint = ServiceEndpoint::findOrFail($id);
        $this->endpointId = $endpoint->id;
        $this->name = $endpoint->name;
        $this->method = $endpoint->method;
        $this->path = $endpoint->path;
        $this->url = $endpoint->url;
        $this->request_body = $endpoint->request_body;
        $this->description = $endpoint->description;
        $this->is_public = (bool) $endpoint->is_public;
        $this->auth_mode = $endpoint->auth_mode ?? 'required';

        // Strip Base URL for display if matches
        // Strip Base URL for display if matches
        if ($this->catalog->base_url) {
            $baseUrlPrefix = rtrim(trim($this->catalog->base_url), '/');
            $currentUrl = trim($this->url);

            if (str_starts_with($currentUrl, $baseUrlPrefix)) {
                $this->url = substr($currentUrl, strlen($baseUrlPrefix));
            }
        }

        $this->isEditMode = true;
        $this->dispatch('open-endpoint-modal');
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->dispatch('close-endpoint-modal');
    }

    public function resetForm()
    {
        $this->endpointId = null;
        $this->name = '';
        $this->method = 'GET';
        $this->path = '';
        $this->url = '';
        $this->request_body = '';
        $this->description = '';
        $this->is_public = true;
        $this->auth_mode = 'required';
        $this->resetErrorBag();
    }

    // CRUD Operations
    public function saveEndpoint()
    {
        // Pre-process URL
        $processingUrl = $this->url;
        if ($this->catalog->base_url && !filter_var($processingUrl, FILTER_VALIDATE_URL)) {
             $baseUrl = rtrim($this->catalog->base_url, '/');
             $pathSegment = ltrim($processingUrl, '/');
             $processingUrl = $baseUrl . '/' . $pathSegment;
        }

        // Merge for validation
        // We use a custom validator to include the processed URL
        $data = [
            'service_catalog_id' => $this->catalog->id,
            'name' => $this->name,
            'method' => $this->method,
            'path' => $this->path,
            'url' => $processingUrl,
            'request_body' => $this->request_body,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'auth_mode' => $this->auth_mode,
        ];


        $rules = [
            'name' => 'required|string|max:255',
            'method' => 'required|in:GET,POST,PUT,DELETE,PATCH',
            'path' => ['required', 'string', 'max:255', 'regex:/^\//'],
            'url' => 'required|url',
            'request_body' => 'nullable|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'auth_mode' => 'required|in:required,none',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $this->dispatch('swal:toast', type: 'error', message: $validator->errors()->first());
            return;
        }

        $validated = $validator->validated();

        // Slug Generation
        $slug = Str::slug($this->name);

        if (!$this->isEditMode) {
             $count = 1;
             $originalSlug = $slug;
             while (ServiceEndpoint::where('slug', $slug)->exists()) {
                 $slug = $originalSlug . '-' . $count++;
             }
        } else {
             $count = 1;
             $originalSlug = $slug;
             while (ServiceEndpoint::where('slug', $slug)->where('id', '!=', $this->endpointId)->exists()) {
                 $slug = $originalSlug . '-' . $count++;
             }
        }

        $data['slug'] = $slug;

        if ($this->isEditMode) {
            $endpoint = ServiceEndpoint::findOrFail($this->endpointId);
            $endpoint->update($data);
        } else {
            ServiceEndpoint::create($data);
        }

        // Reset form first
        $this->resetForm();

        // Dispatch events for modal close and toast notification
        $this->dispatch('endpoint-saved');
        $this->dispatch('swal:toast', type: 'success', message: $this->isEditMode ? 'Endpoint berhasil diperbarui!' : 'Endpoint berhasil ditambahkan!');
    }

    public function confirmDeleteEndpoint($id)
    {
        $this->dispatch('swal:confirm', [
            'type' => 'warning',
            'title' => 'Konfirmasi Penghapusan',
            'text' => 'Apakah Anda yakin ingin menghapus endpoint ini? Data tidak dapat dikembalikan.',
            'confirmText' => 'Ya, Hapus',
            'method' => 'deleteEndpoint',
            'id' => $id
        ]);
    }

    #[On('deleteEndpoint')]
    public function deleteEndpoint($id)
    {
        ServiceEndpoint::find($id)->delete();
        $this->dispatch('swal:toast', type: 'success', message: 'Endpoint berhasil dihapus.');
    }

    public function render()
    {
        $user = auth()->user();
        $endpoints = ServiceEndpoint::where('service_catalog_id', $this->catalog->id)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('path', 'like', '%' . $this->search . '%')
                      ->orWhere('method', 'like', '%' . $this->search . '%');
            })
            // Filter: Jika bukan admin, hanya tampilkan endpoint publik
            ->when(!$user->hasRole('admin'), function ($query) {
                $query->where('is_public', 1);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.service-catalog-detail', [
            'endpoints' => $endpoints
        ]);
    }
}
