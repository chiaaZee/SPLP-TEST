<?php

namespace App\Livewire\Admin;

use App\Models\ServiceCatalog;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceVerificationTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $statusFilter = 'pending';

    public $rejectingId = null;
    public $rejectionReason = '';

    protected $listeners = ['refreshTable' => '$refresh'];

    public function mount()
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $services = ServiceCatalog::query()
            ->with(['user.agency', 'category'])
            ->when($this->statusFilter, function ($q) {
                if ($this->statusFilter === 'history') {
                    $q->whereIn('status', ['active', 'rejected']);
                } else {
                    $q->where('status', $this->statusFilter);
                }
            })
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($u) {
                      $u->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('agency', function ($a) {
                      $a->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.service-verification-table', [
            'services' => $services
        ]);
    }

    public function approve($id)
    {
        $service = ServiceCatalog::find($id);
        if ($service && $service->status === 'pending') {
            $service->update([
                'status' => 'active',
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            // Notify User
            $service->user->notify(new \App\Notifications\ServiceStatusUpdatedNotification($service, 'active'));

            $this->dispatch('swal:toast', type: 'success', message: 'Layanan berhasil diverifikasi dan diterbitkan!');
        }
    }

    public function confirmReject($id)
    {
        $this->rejectingId = $id;
        $this->rejectionReason = '';
        $this->dispatch('open-reject-modal');
    }

    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|min:5'
        ]);

        $service = ServiceCatalog::find($this->rejectingId);
        if ($service) {
            $service->update([
                'status' => 'rejected',
                'rejection_reason' => $this->rejectionReason,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            // Notify User
            $service->user->notify(new \App\Notifications\ServiceStatusUpdatedNotification($service, 'rejected', $this->rejectionReason));

            $this->dispatch('close-reject-modal');
            $this->dispatch('swal:toast', type: 'success', message: 'Layanan ditolak.');
            $this->reset(['rejectingId', 'rejectionReason']);
        }
    }

    public function downloadUAT($id)
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

        $service = ServiceCatalog::findOrFail($id);
        if (!$service->uat_document_path) {
            abort(404, 'UAT Document not found.');
        }

        $path = storage_path('app/public/' . $service->uat_document_path);
        if (!file_exists($path)) {
            abort(404, 'File not found on server.');
        }

        return response()->download($path);
    }
}
