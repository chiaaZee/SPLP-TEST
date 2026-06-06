<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ApiClient;
use App\Models\ServiceCatalog;
use App\Models\Agency;
use App\Models\ServiceAccessRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class ApiClientManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search & Filter
    public $search = '';
    public $perPage = 10;

    // Form Data
    public $name;
    public $service_catalog_id;
    public $skpd_code;

    // UI State
    public $formServiceCatalogs = [];
    public $formAgencies = [];
    public $canCustomizeMapping = false;
    public $requiresMapping = false;

    // Admin Specific
    public $selected_agency_id = ''; // New property for Agency Dropdown

    // Generated Credentials (Ephemeral)
    public $newCredential = null;

    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public function mount()
    {
        // Explicitly check permission to ensure consistency with Menu
        // If this fails, it means the user's Role does NOT have 'manage_api_keys' permission
        if (!auth()->user()->can('manage_api_keys')) {
            abort(403, 'Unauthorized. Permission "manage_api_keys" is required.');
        }

        $this->loadFormData();
    }

    public function loadFormData()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        // Load Service Catalogs
        $catalogQuery = ServiceCatalog::where('status', 'active');

        if (!$isAdmin) {
            // User can only see approved services
            $approvedAccess = ServiceAccessRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->get(['service_catalog_id', 'can_customize_mapping']);

            $approvedIds = $approvedAccess->pluck('service_catalog_id');
            // Store permissions for referencing in updatedServiceCatalogId
            $this->catalogPermissions = $approvedAccess->pluck('can_customize_mapping', 'service_catalog_id')->toArray();

            $catalogQuery->whereIn('id', $approvedIds);
        }

        $this->formServiceCatalogs = $catalogQuery->get(['id', 'name', 'slug', 'requires_mapping', 'mapping_field']);

        // Load Agencies for Super User (Admin)
        if ($isAdmin) {
             $this->formAgencies = Agency::where('status', 'active')->orderBy('name')->get(['id', 'code', 'name']);
             $this->canCustomizeMapping = true; // Admin can always customize
        }
    }

    // Hook: When Agency is selected (Admin only)
    public function updatedSelectedAgencyId($value)
    {
        if (empty($value)) {
            $this->skpd_code = '';
            return;
        }

        $agency = Agency::find($value);
        if ($agency) {
            $this->skpd_code = $agency->code;
        }
    }

    public function updatedServiceCatalogId($value)
    {
        // $this->skpd_code = ''; // Don't reset SKPD code if Admin selected an agency already?
        // Better to reset if logic demands, but here we want to keep agency selection if possible OR reset both.
        // Let's reset for safety unless we want persistence. User usually selects Service -> Agency.

        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        if (empty($value)) {
            if (!$isAdmin) {
                $this->canCustomizeMapping = false;
                $this->requiresMapping = false;
            }
            return;
        }

        $catalog = $this->formServiceCatalogs->firstWhere('id', $value);
        if (!$catalog) return;

        $this->requiresMapping = $catalog->requires_mapping;

        // Determine Can Customize
        if ($isAdmin) {
            $this->canCustomizeMapping = true;
        } else {
            // Check stored permission from mount
            $this->canCustomizeMapping = $this->catalogPermissions[$value] ?? false;
        }

        // If can customize and we don't have agencies loaded yet (e.g. non-admin user with special permission), load them
        if ($this->canCustomizeMapping && empty($this->formAgencies)) {
             $this->formAgencies = Agency::where('status', 'active')->orderBy('name')->get(['id', 'code', 'name']);
        }
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'service_catalog_id' => 'required|exists:service_catalogs,id',
            'skpd_code' => 'nullable|string'
        ]);

        $user = auth()->user();

        // Final Permission Verification (Server Side)
        $isAdmin = $user->hasRole('admin');
        $canCustomize = false;

        if ($isAdmin) {
            $canCustomize = true;
        } else {
             $access = ServiceAccessRequest::where('user_id', $user->id)
                ->where('service_catalog_id', $this->service_catalog_id)
                ->where('status', 'approved')
                ->first();

             if (!$access) {
                 $this->addError('service_catalog_id', 'Anda tidak memiliki akses yang disetujui untuk layanan ini.');
                 return;
             }
             $canCustomize = $access->can_customize_mapping;
        }

        // Resolve Mapping
        $mappingConfig = [];
        if ($canCustomize) {
            if (!empty($this->skpd_code)) {
                $mappingConfig = ['skpd_code' => $this->skpd_code];
            }
        } else {
            // Force User's Agency Code
            $agencyCode = $user->agency->code ?? null;
            if (!$agencyCode) {
                 $this->dispatch('swal:alert', type: 'error', title: 'Error', text: 'Akun instansi Anda belum memiliki Kode SKPD. Hubungi Admin.');
                 return;
            }
            $mappingConfig = ['skpd_code' => $agencyCode];
        }

        $creds = ApiClient::generateCredentials();

        try {
            DB::beginTransaction();

            $client = ApiClient::create([
                'user_id' => $user->id,
                'name' => $this->name,
                'api_key' => $creds['api_key'],
                'secret_key' => $creds['secret_key'],
                'status' => 'active',
                'service_catalog_id' => $this->service_catalog_id,
                'mapping_config' => $mappingConfig
            ]);

            DB::commit();

            // Set New Creds for Modal Display
            $this->newCredential = [
                'api_key' => $client->api_key,
                'secret_key' => $client->secret_key
            ];

            $this->reset(['name', 'service_catalog_id', 'skpd_code', 'selected_agency_id']);
            $this->dispatch('close-create-modal');
            $this->dispatch('credential-created', credential: $this->newCredential); // Handle Swal in JS

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:toast', type: 'error', message: 'Gagal membuat API Key: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'type' => 'warning',
            'title' => 'Revoke API Key?',
            'text' => 'Aplikasi akan kehilangan akses selamanya.',
            'confirmText' => 'Ya, Revoke!',
            'method' => 'delete',
            'id' => $id
        ]);
    }

    #[On('delete')]
    public function delete($id)
    {
        $query = ApiClient::where('id', $id);

        if (!auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->id());
        }

        $query->delete();
        $this->dispatch('swal:toast', type: 'success', message: 'API Key berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $query = ApiClient::where('id', $id);

        if (!auth()->user()->hasRole('admin')) {
             $query->where('user_id', auth()->id());
        }

        $client = $query->firstOrFail();
        $newStatus = $client->status === 'active' ? 'inactive' : 'active';
        $client->update(['status' => $newStatus]);

        $msg = $newStatus === 'active' ? 'API Key diaktifkan.' : 'API Key disuspend (Nonaktif).';
        $this->dispatch('swal:toast', type: 'success', message: $msg);
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        $clients = ApiClient::with(['user.agency', 'serviceCatalog'])
            ->when(!$isAdmin, function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->when($this->search, function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('api_key', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.user.api-client-manager', [
            'clients' => $clients,
            'isAdmin' => $isAdmin
        ])->extends('layouts.layoutMaster');
    }

     // Temp property to store perms logic in PHP state
     protected $catalogPermissions = [];
}
