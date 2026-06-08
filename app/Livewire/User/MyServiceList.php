<?php

namespace App\Livewire\User;

use App\Models\ServiceCatalog;
use App\Models\ServiceCategory;
use Livewire\Component;
use Livewire\WithPagination;

class MyServiceList extends Component
{
    use WithPagination;
    use \Livewire\WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingId = null; // Track editing state

    // Proposal Form Fields
    public $name;
    public $category_id;
    public $description;
    public $base_url;
    public $target_token;
    public $cover_image;
    public $uat_document;

    // For keeping old files when updating
    public $existingCover;
    public $existingUat;

    public $isModalOpen = false;

    // Validation Rules
    protected function rules()
    {
        $rules = [
            'name' => 'required|min:3|max:255',
            'category_id' => 'required|exists:service_categories,id',
            'description' => 'required|max:1000',
            'base_url' => 'required|url',
            'target_token' => 'required|string',
        ];

        // Conditional validation for files
        if ($this->editingId) {
            $rules['cover_image'] = 'nullable|image|max:2048';
            $rules['uat_document'] = 'nullable|file|mimes:pdf,doc,docx|max:5120';
        } else {
            $rules['cover_image'] = 'required|image|max:2048';
            $rules['uat_document'] = 'required|file|mimes:pdf,doc,docx|max:5120';
        }

        return $rules;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
        $this->dispatch('close-modal');
    }

    private function resetInputFields()
    {
        $this->reset(['name', 'category_id', 'description', 'base_url', 'target_token', 'cover_image', 'uat_document', 'editingId', 'existingCover', 'existingUat']);
        $this->resetValidation();
    }

    public function edit($id)
    {
        $service = ServiceCatalog::find($id);

        if ($service && ($service->user_id === auth()->id()) && $service->status === 'rejected') {
            $this->editingId = $id;
            $this->name = $service->name;
            $this->category_id = $service->category_id;
            $this->description = $service->description;
            $this->base_url = $service->base_url;
            $this->target_token = $service->target_token;

            // Keep track of existing files
            $this->existingCover = $service->cover_image;
            $this->existingUat = $service->uat_document_path;

            $this->isModalOpen = true;
            $this->dispatch('open-modal');
        }
    }

    public function store()
    {
        $this->validate();

        $data = [
            'agency_id' => auth()->user()->agency_id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'base_url' => $this->base_url,
            'target_token' => $this->target_token,
            'status' => 'pending', // Reset to pending on save
        ];

        // Handle Cover Image
        if ($this->cover_image) {
            $data['cover_image'] = $this->cover_image->store('service_covers', 'public');
        } elseif ($this->editingId) {
             // Keep existing
             $data['cover_image'] = $this->existingCover;
        }

        // Handle UAT Doc
        if ($this->uat_document) {
            $data['uat_document_path'] = $this->uat_document->store('uat_docs', 'public');
        } elseif ($this->editingId) {
             $data['uat_document_path'] = $this->existingUat;
        }

        if ($this->editingId) {
            // Update Logic
            $service = ServiceCatalog::find($this->editingId);
            if (!$service || $service->user_id !== auth()->id() || $service->status !== 'rejected') {
                abort(403, 'Unauthorized action.');
            }
            $service->update(array_merge($data, [
                'rejection_reason' => null // Clear reason
            ]));

            // Notify Admin
            $this->notifyAdmins($service);

            $message = 'Layanan berhasil diperbarui dan diajukan ulang!';
        } else {
            // Create Logic
            $data['user_id'] = auth()->id();
            $data['slug'] = \Str::slug($this->name) . '-' . \Str::random(5);

            $service = ServiceCatalog::create($data);

            // Notify Admin
            $this->notifyAdmins($service);

            $message = 'Layanan berhasil diajukan! Menunggu verifikasi admin.';
        }

        $this->closeModal();
        $this->dispatch('swal:toast', type: 'success', message: $message);
        $this->dispatch('refresh-ui');
    }

    protected function notifyAdmins($service)
    {
        $admins = \App\Models\User::role('admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ServiceSubmittedNotification($service));
    }

    public function render()
    {
        $services = ServiceCatalog::query()
            ->owned()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->with(['category', 'accessRequests' => function($query) {
                $query->where('status', 'approved')->with('user.agency');
            }])
            ->withCount('apiLogs')
            ->latest()
            ->paginate(10);

        return view('livewire.user.my-service-list', [
            'services' => $services,
            'categories' => ServiceCategory::orderBy('name')->get()
        ])->extends('layouts.layoutMaster')->section('content');
    }
}
