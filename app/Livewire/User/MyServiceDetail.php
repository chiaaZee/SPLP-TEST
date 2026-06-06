<?php

namespace App\Livewire\User;

use App\Models\ServiceCatalog;
use App\Models\ServiceCategory;
use App\Models\ServiceEndpoint;
use App\Models\ServiceAccessRequest;
use App\Models\ApiLog;
use App\Models\User;
use App\Notifications\AccessRequestNotification;
use Livewire\Component;

class MyServiceDetail extends Component
{
    public ServiceCatalog $service;
    public $endpoints;

    // Modal State
    public $isModalOpen = false;
    public $editingEndpointId = null;

    // Form Fields
    public $name = '';
    public $method = 'GET';
    public $path = '';
    public $url = ''; // Target URL
    public $description = '';
    public $request_body = '';
    public $is_public = false;
    public $auth_mode = 'required'; // Default to secured

    // Config Fields
    public $base_url;
    public $service_category_id;
    public $categories = [];
    public $rate_limit;
    public $target_token;
    public $mapping_field;

    // Stats & Lists
    public $active_users_count = 0;
    public $total_requests = 0;
    public $accessRequests = []; // For User List Tab (Active)
    public $pendingRequests = []; // For Pending Approval

    // UI State
    public $activeTab = 'info';

    public function mount(ServiceCatalog $service)
    {
        if ($service->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        $this->service = $service;

        // Load Config
        $this->base_url = $service->base_url;
        $this->service_category_id = $service->service_category_id;
        $this->categories = ServiceCategory::all();
        $this->rate_limit = $service->rate_limit;
        $this->target_token = $service->target_token;
        $this->mapping_field = $service->mapping_field;

        // Load Stats & Users
        $this->refreshRequests();

        $this->active_users_count = $service->accessRequests()->where('status', 'approved')->count(); // Estimasi unique user
        $this->total_requests = $service->apiLogs()->count();

        $this->refreshEndpoints();
    }

    public function refreshRequests()
    {
        // Active Users (Fully Approved)
        $this->accessRequests = $this->service->accessRequests()
            ->with('user')
            ->where('status', 'approved')
            ->latest()
            ->get();

        // Pending Requests (Waiting for Owner)
        $this->pendingRequests = $this->service->accessRequests()
            ->with('user')
            ->where(function($q) {
                // Logic: Status pending OR (status pending_admin but owner approved date is null? - fallback)
                // Assuming status 'pending' is the initial state where Owner needs to approve
                $q->where('status', 'pending')
                  ->whereNull('owner_approved_at');
            })
            ->latest()
            ->get();
    }

    public function refreshEndpoints()
    {
        $this->endpoints = $this->service->endpoints()->orderBy('created_at')->get();
    }

    public function render()
    {
        return view('livewire.user.my-service-detail')->extends('layouts.layoutMaster')->section('content');
    }

    public function updateConfig()
    {
        $this->validate([
            // 'base_url' => 'required|url', // Disabled edit per user request
            'service_category_id' => 'required|exists:service_categories,id',
            'rate_limit' => 'required|integer|min:1',
            'target_token' => 'nullable|string',
            'mapping_field' => 'nullable|string'
        ]);

        $this->service->update([
            // 'base_url' => $this->base_url,
            'service_category_id' => $this->service_category_id,
            'rate_limit' => $this->rate_limit,
            'target_token' => $this->target_token,
            'mapping_field' => $this->mapping_field
        ]);

        $this->dispatch('swal:toast', type: 'success', message: 'Konfigurasi layanan diperbarui.');
    }

    public function togglePublish()
    {
        $this->service->is_public = !$this->service->is_public;
        $this->service->save();

        $status = $this->service->is_public ? 'Dipublikasikan' : 'Disembunyikan';
        $this->dispatch('swal:toast', type: 'success', message: "Layanan berhasil $status.");
    }

    public function openEndpointModal()
    {
        $this->reset(['name', 'method', 'path', 'url', 'description', 'request_body', 'is_public', 'auth_mode', 'editingEndpointId']);
        $this->resetValidation();
        $this->dispatch('show-endpoint-modal');
    }

    public function closeModal()
    {
        $this->isModalOpen = false; // Keep specifically if used for other logic, but primarily we use JS now
        $this->dispatch('hide-endpoint-modal');
    }

    public function storeEndpoint()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'method' => 'required|in:GET,POST,PUT,DELETE,PATCH',
            'path' => 'required|string|max:255',
            'url' => 'nullable|string|max:255', // Target URL override
            'description' => 'required|string',
            'request_body' => 'nullable|string',
            'is_public' => 'boolean',
            'auth_mode' => 'required|in:required,none'
        ]);

        $data = [
            'name' => $this->name,
            'method' => $this->method,
            'path' => $this->path,
            'url' => $this->url,
            'description' => $this->description,
            'request_body' => $this->request_body,
            'is_public' => $this->is_public,
            'auth_mode' => $this->auth_mode,
        ];

        if ($this->editingEndpointId) {
            $endpoint = ServiceEndpoint::find($this->editingEndpointId);
            if ($endpoint && $endpoint->service_catalog_id === $this->service->id) {
                $endpoint->update($data);
                $this->dispatch('swal:toast', type: 'success', message: 'Endpoint diperbarui.');
            }
        } else {
            $data['slug'] = \Illuminate\Support\Str::slug($this->name . '-' . uniqid());
            $this->service->endpoints()->create($data);
            $this->dispatch('swal:toast', type: 'success', message: 'Endpoint ditambahkan.');
        }

        $this->activeTab = 'endpoints'; // Persist tab
        $this->dispatch('hide-endpoint-modal');
        $this->refreshEndpoints();
    }

    public function editEndpoint($id)
    {
        $this->editingEndpointId = $id;
        $endpoint = ServiceEndpoint::findOrFail($id);

        if ($endpoint->service_catalog_id !== $this->service->id) {
            abort(403);
        }

        $this->name = $endpoint->name;
        $this->method = $endpoint->method;
        $this->path = $endpoint->path;
        $this->url = $endpoint->url;
        $this->description = $endpoint->description;
        $this->request_body = $endpoint->request_body;
        $this->is_public = (bool)$endpoint->is_public;
        $this->auth_mode = $endpoint->auth_mode;

        $this->dispatch('show-endpoint-modal');
    }

    public function deleteEndpoint($id)
    {
        $endpoint = ServiceEndpoint::find($id);
        if ($endpoint && $endpoint->service_catalog_id === $this->service->id) {
            $endpoint->delete();
            $this->activeTab = 'endpoints'; // Persist tab
            $this->refreshEndpoints();
            $this->dispatch('swal:toast', type: 'success', message: 'Endpoint dihapus.');
        }
    }

    public function approveRequest($id)
    {
        $request = ServiceAccessRequest::find($id);

        if (!$request || $request->service_catalog_id !== $this->service->id) {
            $this->dispatch('swal:toast', type: 'error', message: 'Permintaan tidak valid.');
            return;
        }

        // Owner Appeals
        $request->update([
            'owner_approved_at' => now(),
            'owner_note' => 'Disetujui oleh pemilik layanan.',
            'status' => 'pending_admin' // Move to next stage
        ]);

        // Notify Admins
        $admins = User::role('admin')->get(); // Assuming Spatie Permission or similar method exists. If not, fallback to where('role', 'admin')
        // Safe check for role method, otherwise use simple where if role is a column
        if (!method_exists($admins, 'notify') && $admins->isEmpty()) {
             $admins = User::where('role', 'admin')->get();
        }

        foreach ($admins as $admin) {
            $admin->notify(new AccessRequestNotification($request, 'owner_approved'));
        }

        $this->refreshRequests();
        $this->dispatch('swal:toast', type: 'success', message: 'Permintaan disetujui. Menunggu persetujuan Admin.');
    }

    public function rejectRequest($id)
    {
        $request = ServiceAccessRequest::find($id);

        if (!$request || $request->service_catalog_id !== $this->service->id) {
            return;
        }

        $request->update([
            'status' => 'rejected',
            'owner_note' => 'Ditolak oleh pemilik layanan.'
        ]);

         // Notify User
        $request->user->notify(new AccessRequestNotification($request, 'rejected'));

        $this->refreshRequests();
        $this->dispatch('swal:toast', type: 'info', message: 'Permintaan ditolak.');
    }

}
