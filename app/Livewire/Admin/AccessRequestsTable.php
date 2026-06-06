<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ServiceAccessRequest;

class AccessRequestsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Bulk Actions
    public $selected = [];
    public $selectAll = false;

    // Filter
    public $statusFilter = 'pending';

    public $approvingRequest = null;
    public $canCustomizeMapping = false;
    public $approvalNote = '';

    // UI Options
    public $showBanner = true;

    public function mount($showBanner = true, $statusFilter = 'pending')
    {
        $this->showBanner = $showBanner;
        $this->statusFilter = $statusFilter;
    }

    // Listeners for SweetAlert events
    protected $listeners = [
        'refreshTable' => '$refresh',
        'reject' => 'reject',
        'bulkReject' => 'bulkReject'
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getRequestsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
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

    public function getRequestsQuery()
    {
        return ServiceAccessRequest::query()
            ->with(['user.agency', 'serviceCatalog'])
            ->when($this->statusFilter, function($q) {
                if ($this->statusFilter === 'history') {
                    return $q->whereIn('status', ['approved', 'rejected']);
                }
                if ($this->statusFilter === 'pending') {
                    return $q->where('status', 'pending_admin');
                }
                return $q->where('status', $this->statusFilter);
            })
            ->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('agency', function ($qa) {
                          $qa->where('name', 'like', '%' . $this->search . '%');
                      });
                })
                ->orWhereHas('serviceCatalog', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $requests = $this->getRequestsQuery()->paginate($this->perPage);
        return view('livewire.admin.access-requests-table', [
            'requests' => $requests
        ]);
    }

    // Actions
    public function openApprovalModal($id)
    {
        $this->approvingRequest = ServiceAccessRequest::with(['user.agency', 'serviceCatalog'])->find($id);
        if ($this->approvingRequest) {
            $this->canCustomizeMapping = false; // Reset default
            $this->approvalNote = 'Disetujui oleh Admin';
            $this->dispatch('open-approval-modal'); // Trigger JS to open modal
        }
    }

    public function submitApprove()
    {
        if ($this->approvingRequest) {
            $this->approvingRequest->update([
                'status' => 'approved',
                'can_customize_mapping' => $this->canCustomizeMapping,
                'admin_note' => $this->approvalNote,
                'admin_approved_at' => now()
            ]);

            // Notify User
            if ($this->approvingRequest->user) {
                $this->approvingRequest->user->notify(new \App\Notifications\AccessRequestUpdated($this->approvingRequest));
            }

            $this->dispatch('close-approval-modal');
            $this->dispatch('swal:toast', type: 'success', message: 'Permohonan akses berhasil disetujui!');
            $this->dispatch('update-notifications');
            $this->dispatch('refreshTable');

            // Reset
            $this->reset(['approvingRequest', 'canCustomizeMapping', 'approvalNote']);
        }
    }

    public function confirmReject($id)
    {
        $this->dispatch('swal:confirm',
            type: 'warning',
            title: 'Tolak Permintaan?',
            text: 'Aksi ini tidak dapat dibatalkan.',
            id: $id,
            method: 'reject'
        );
    }

    public function reject($id)
    {
        $request = ServiceAccessRequest::with('user')->find($id); // Eager load user
        if ($request) {
            $request->status = 'rejected';
            $request->save();

             // Notify User
             if($request->user) {
                $request->user->notify(new \App\Notifications\AccessRequestUpdated($request));
             }

            $this->dispatch('swal:toast', type: 'error', message: 'Permohonan akses ditolak.');
            $this->dispatch('update-notifications');
            $this->dispatch('refreshTable');
        }
    }

    public function bulkApprove()
    {
        $pendingRequests = ServiceAccessRequest::whereIn('id', $this->selected)
            ->where('status', 'pending_admin')
            ->get();

        $count = $pendingRequests->count();

        if ($count > 0) {
            ServiceAccessRequest::whereIn('id', $pendingRequests->pluck('id'))
                ->update([
                    'status' => 'approved',
                    'admin_approved_at' => now()
                ]);

            $this->selected = [];
            $this->selectAll = false;

            $this->dispatch('swal:toast', type: 'success', message: "$count Permintaan berhasil disetujui massal!");
        } else {
             $this->dispatch('swal:toast', type: 'info', message: "Tidak ada permintaan 'Pending Admin' yang dipilih.");
        }
    }

    public function confirmBulkReject()
    {
        $count = ServiceAccessRequest::whereIn('id', $this->selected)
            ->where('status', 'pending_admin')
            ->count();

        if ($count > 0) {
            $this->dispatch('swal:confirm',
                type: 'warning',
                title: 'Tolak ' . $count . ' Permintaan?',
                text: 'Hanya permintaan berstatus Pending Admin yang akan ditolak.',
                confirmText: 'Ya, Tolak Semua!',
                cancelText: 'Batal',
                method: 'bulkReject'
            );
        } else {
            $this->dispatch('swal:toast', type: 'info', message: "Tidak ada permintaan 'Pending Admin' yang dipilih.");
        }
    }

    public function bulkReject()
    {
        $pendingRequests = ServiceAccessRequest::whereIn('id', $this->selected)
            ->where('status', 'pending_admin')
            ->get();

        $count = $pendingRequests->count();

        if ($count > 0) {
            ServiceAccessRequest::whereIn('id', $pendingRequests->pluck('id'))
                ->update(['status' => 'rejected']);

            $this->selected = [];
            $this->selectAll = false;

            $this->dispatch('swal:toast', type: 'success', message: "$count Permintaan berhasil ditolak massal!");
        }
    }
}
