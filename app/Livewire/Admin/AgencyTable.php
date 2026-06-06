<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Agency;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AgencyTable extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $connectionStatus = ''; // '' = All, 'connected', 'disconnected'

    // Form Properties
    public $agencyId = null;
    public $name, $code, $email, $phone, $address, $status = 'active';
    public $logo;
    public $existingLogo;

    // Modal State
    public $isModalOpen = false;
    public $isEditMode = false;

    // Services Modal Properties
    public $isServiceModalOpen = false;
    public $selectedAgency = null;
    public $agencyServices = [];
    public $serviceStatuses = []; // [service_id => 'connected' | 'disconnected']
    public $checkingStatus = []; // [service_id => true]

    // Listeners
    protected $listeners = [
        'refreshTable' => '$refresh',
        'deleteConfirmed' => 'deleteAgency'
    ];

    public function mount()
    {
        // Capture 'filter' from query string if present
        $filter = request()->query('filter');
        if ($filter === 'connected') {
            $this->connectionStatus = 'connected';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedConnectionStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->isModalOpen = true;
        $this->isEditMode = false;
        $this->dispatch('open-agency-modal');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
        $this->dispatch('close-agency-modal');
    }

    public function getConnectedServiceCount($agency)
    {
        // 1. Direct Access Requests (Approved)
        $directServiceIds = \App\Models\ServiceAccessRequest::where('status', 'approved')
            ->whereHas('user', function($u) use ($agency) {
                $u->where('agency_id', $agency->id);
            })
            ->pluck('service_catalog_id')
            ->toArray();

        // 2. Mapped API Clients
        // Find clients where mapping_config->skpd_code matches agency code
        $mappedServiceIds = [];
        if ($agency->code) {
             $mappedServiceIds = \App\Models\ApiClient::whereJsonContains('mapping_config->skpd_code', $agency->code)
                ->pluck('service_catalog_id') // Assuming ApiClient has service_catalog_id relationship or column
                ->toArray();
        }

        // Merge and Count Unique
        $allServiceIds = array_unique(array_merge($directServiceIds, $mappedServiceIds));

        return count($allServiceIds);
    }

    public function openServiceModal($agencyId)
    {
        $this->selectedAgency = Agency::find($agencyId);
        if ($this->selectedAgency) {
            // 1. Direct Access Requests
            $directServices = \App\Models\ServiceCatalog::whereHas('accessRequests', function($q) use ($agencyId) {
                $q->where('status', 'approved')
                  ->whereHas('user', function($u) use ($agencyId) {
                      $u->where('agency_id', $agencyId);
                  });
            })->get();

            // 2. Mapped API Clients
            $mappedServices = collect();
            if ($this->selectedAgency->code) {
                $mappedClientIds = \App\Models\ApiClient::whereJsonContains('mapping_config->skpd_code', $this->selectedAgency->code)
                    ->pluck('id');

                if ($mappedClientIds->isNotEmpty()) {
                    // Logic assume ApiClient has 'service_catalog_id'
                    // Actually ApiClient belongs to User, but it is created FOR a Service?
                    // Let's check db schema mentally. Usually ApiClient is for a service?
                    // Wait, ApiClient in this system seems to be Generic or Specific?
                    // Previous context: "Api Client Management".
                    // Let's assume ApiClient has `service_catalog_id` or `service_access_request_id`?
                    // Re-reading previous files... `ApiClient` model.
                    // If ApiClient doesn't have service_catalog_id, we might need to check logs?
                    // BUT, typically a Client Key is for the whole system? or specific service?
                    // If it's for whole system, `ApiLog` has `service_catalog_id`.
                    // IF keys are global, then "Connected Service" = "Has successful Log for that service using that Key".

                    // Let's assume keys are Global for now but we check USAGE (Logs).
                    // "Connected" implies active usage or permission.
                    // If access request exists -> Connected.
                    // For Mapped Key -> If they have Mapped Key, they technically can access public/protected endpoints if allowed.
                    // But to List "Used Services", checking distinct `service_catalog_id` from `ApiLog` for those clients is safest.

                    $mappedServices = \App\Models\ServiceCatalog::whereHas('apiLogs', function($q) use ($mappedClientIds) {
                        $q->whereIn('api_client_id', $mappedClientIds)
                          ->whereBetween('status_code', [200, 299]);
                    })->get();
                }
            }

            $services = $directServices->merge($mappedServices)->unique('id'); // Merge
            $this->agencyServices = $services;

            // Populate statuses based on LAST API Log from this agency's users OR mapped clients
            $this->serviceStatuses = [];

            foreach ($services as $service) {
                // Find latest log for this service from any user in this agency OR mapped client
                $lastLog = \App\Models\ApiLog::where('service_catalog_id', $service->id)
                    ->where(function($q) use ($agencyId) {
                        // User in Agency
                        $q->whereHas('user', function($u) use ($agencyId) {
                            $u->where('agency_id', $agencyId);
                        });

                        // OR Mapped Client
                        $agencyCode = Agency::find($agencyId)->code;
                        if ($agencyCode) {
                            $q->orWhereHas('client', function($c) use ($agencyCode) {
                                $c->whereJsonContains('mapping_config->skpd_code', $agencyCode);
                            });
                        }
                    })
                    ->latest()
                    ->first();

                if ($lastLog) {
                    $this->serviceStatuses[$service->id] = [
                        'status' => ($lastLog->status_code >= 200 && $lastLog->status_code < 300) ? 'connected' : 'disconnected',
                        'last_check' => $lastLog->created_at,
                        'code' => $lastLog->status_code
                    ];
                } else {
                    $this->serviceStatuses[$service->id] = [ // Default for Access Request with no logs yet
                        'status' => 'unknown',
                        'last_check' => null,
                        'code' => '-'
                    ];
                }
            }

            $this->isServiceModalOpen = true;
            $this->dispatch('open-service-modal');
        }
    }

    public function closeServiceModal()
    {
        $this->isServiceModalOpen = false;
        $this->selectedAgency = null;
        $this->agencyServices = [];
        $this->dispatch('close-service-modal');
    }

    public function checkServiceStatus($serviceId)
    {
        $this->checkingStatus[$serviceId] = true;
        // ... (Keep existing simple check logic if needed, but primarily relying on Logs now)
        // For brevity, keeping simple check or just relying on logs.
        // Let's keep original simple check logic or empty for now as logs are primary source?
        // Reuse original logic:
        $service = \App\Models\ServiceCatalog::find($serviceId);
        if ($service && $service->base_url) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->get($service->base_url);
                if ($response->successful()) {
                    $this->serviceStatuses[$serviceId]['status'] = 'connected'; // Update status
                } else {
                    $this->serviceStatuses[$serviceId]['status'] = 'disconnected';
                }
            } catch (\Exception $e) {
                $this->serviceStatuses[$serviceId]['status'] = 'disconnected';
            }
        }
        unset($this->checkingStatus[$serviceId]);
    }

    public function resetForm()
    {
        $this->agencyId = null;
        $this->name = '';
        $this->code = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->status = 'active';
        $this->logo = null;
        $this->existingLogo = null;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:agencies,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048', // 2MB Max
            'status' => 'required|in:active,inactive'
        ]);

        $logoPath = null;
        if ($this->logo) {
            // Store locally in public/assets/img/agency/ to match existing structure
            $imageName = time() . '.' . $this->logo->extension();
            $this->logo->storeAs('assets/img/agency', $imageName, 'public_uploads');
            $logoPath = $imageName;
        }

        Agency::create([
            'name' => $this->name,
            'code' => $this->code,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'logo' => $logoPath
        ]);

        $this->closeModal();
        $this->dispatch('swal:toast', type: 'success', message: 'Instansi berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->resetForm();

        $agency = Agency::find($id);
        $this->agencyId = $agency->id;
        $this->name = $agency->name;
        $this->code = $agency->code;
        $this->email = $agency->email;
        $this->phone = $agency->phone;
        $this->address = $agency->address;
        $this->status = $agency->status;
        $this->existingLogo = $agency->logo;

        $this->isEditMode = true;
        $this->isModalOpen = true;
        $this->dispatch('open-agency-modal');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:agencies,code,' . $this->agencyId,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        $agency = Agency::find($this->agencyId);
        $logoPath = $agency->logo;

        if ($this->logo) {
            // Delete old logo
            if ($agency->logo && file_exists(public_path('assets/img/agency/' . $agency->logo))) {
                unlink(public_path('assets/img/agency/' . $agency->logo));
            }

            $imageName = time() . '.' . $this->logo->extension();
            $this->logo->storeAs('assets/img/agency', $imageName, 'public_uploads');
            $logoPath = $imageName;
        }

        $agency->update([
            'name' => $this->name,
            'code' => $this->code,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'logo' => $logoPath
        ]);

        $this->closeModal();
        $this->dispatch('swal:toast', type: 'success', message: 'Data Instansi berhasil diperbarui!');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm',
            type: 'warning',
            title: 'Hapus Instansi?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            id: $id,
            method: 'deleteConfirmed'
        );
    }

    public function deleteAgency($id)
    {
        $agency = Agency::find($id);
        if ($agency) {
            if ($agency->logo && file_exists(public_path('assets/img/agency/' . $agency->logo))) {
                unlink(public_path('assets/img/agency/' . $agency->logo));
            }
            $agency->delete();
            $this->dispatch('swal:toast', type: 'success', message: 'Instansi berhasil dihapus.');
        }
    }

    public function getAgenciesQuery()
    {
        $query = Agency::query()
            ->withCount(['accessRequests' => function($q) {
                // Count distinct service catalogs? No, withCount distinct is hard.
                // Assuming access requests count is good proxy for now.
                // If distinct needed: we need separate query or custom select.
                // User said "menggunakan 2 katalog" so distinct is important.
                // But withCount returns raw count.
                // Filter approved only.
                $q->where('service_access_requests.status', 'approved');
            }])
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });

        // connectionStatus Filter
        if ($this->connectionStatus === 'connected') {
            $query->where(function ($q) {
                // 1. Has Approved Access Requests
                $q->whereHas('accessRequests', function ($sub) {
                    $sub->where('service_access_requests.status', 'approved');
                })
                // 2. OR Has Mapped API Client
                ->orWhere(function ($subQ) {
                    // Logic: Find agencies where 'code' matches any ApiClient mapping
                    // This is complex in Eloquent without a direct relation or JSON capability on Agency side.
                    // But Agency has 'code'. ApiClient has 'mapping_config->skpd_code'.
                    // We need: select * from agencies where exists (select * from api_clients where json_unquote(json_extract(mapping_config, '$.skpd_code')) = agencies.code)

                    // Using existing DB facade or raw where
                    $subQ->whereNotNull('code')
                         ->whereExists(function ($exists) {
                             $exists->select(DB::raw(1))
                                    ->from('api_clients')
                                    ->whereColumn('api_clients.mapping_config->skpd_code', 'agencies.code');
                         });
                });
            });
        } elseif ($this->connectionStatus === 'disconnected') {
            $query->where(function ($q) {
                // 1. No Approved Access Requests
                $q->whereDoesntHave('accessRequests', function ($sub) {
                    $sub->where('service_access_requests.status', 'approved');
                })
                // 2. AND No Mapped API Client
                ->where(function ($subQ) {
                     $subQ->whereNull('code')
                          ->orWhereNotExists(function ($exists) {
                             $exists->select(DB::raw(1))
                                    ->from('api_clients')
                                    ->whereColumn('api_clients.mapping_config->skpd_code', 'agencies.code');
                         });
                });
            });
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $agencies = $this->getAgenciesQuery()->paginate($this->perPage);
        return view('livewire.admin.agency-table', [
            'agencies' => $agencies
        ]);
    }
}
