<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\RegistrationLog;

class ConfirmRegistrationsTable extends Component
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

    // UI Options
    public $showBanner = true;

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
            $this->selected = $this->getUsersQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
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

    public function getUsersQuery()
    {
        return User::query()
            ->with('agency')
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('agency', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->latest() // Use latest() instead of sorting by sortField initially to verify content
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $users = $this->getUsersQuery()->paginate($this->perPage);
        return view('livewire.admin.confirm-registrations-table', [
            'users' => $users
        ]);
    }

    // Actions
    public function approve($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->status = 'active';
            $user->save();

            if ($user->agency && $user->agency->status === 'pending') {
                $user->agency->status = 'active';
                $user->agency->save();
            }

            $this->dispatch('swal:toast', type: 'success', message: 'User ' . $user->name . ' berhasil disetujui!');
            $this->dispatch('update-notifications');
            $this->dispatch('refreshTable');
        }
    }

    public function confirmReject($id)
    {
        $this->dispatch('swal:confirm',
            type: 'warning',
            title: 'Tolak Pendaftaran?',
            text: 'Aksi ini tidak dapat dibatalkan. User akan ditandai sebagai ditolak.',
            id: $id,
            method: 'reject'
        );
    }

    public function reject($id)
    {
        $user = User::find($id);
        if ($user) {
             // Log rejection
             RegistrationLog::create([
                'email' => $user->email,
                'name' => $user->name,
                'action' => 'rejected',
                'user_data' => $user->load('agency')->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $user->status = 'rejected';
            $user->save();

            $this->dispatch('swal:toast', type: 'error', message: 'User ' . $user->name . ' telah ditolak.');
            $this->dispatch('update-notifications');
            $this->dispatch('refreshTable');
        }
    }

    public function bulkApprove()
    {
        $count = count($this->selected);
        if ($count > 0) {
            User::whereIn('id', $this->selected)->update(['status' => 'active']);
            // Also approve agencies if pending
            $users = User::whereIn('id', $this->selected)->with('agency')->get();
            foreach ($users as $user) {
                if ($user->agency && $user->agency->status === 'pending') {
                    $user->agency->status = 'active';
                    $user->agency->save();
                }
            }

            $this->selected = [];
            $this->selectAll = false;

            $this->dispatch('swal:toast', type: 'success', message: "$count User berhasil disetujui massal!");
            $this->dispatch('update-notifications');
            $this->dispatch('refreshTable');
        }
    }

    public function confirmBulkReject()
    {
        $count = count($this->selected);
        if ($count > 0) {
            $this->dispatch('swal:confirm',
                type: 'warning',
                title: 'Tolak ' . $count . ' Pendaftaran?',
                text: 'Semua user yang dipilih akan ditolak permanen.',
                confirmText: 'Ya, Tolak Semua!',
                cancelText: 'Batal',
                method: 'bulkReject'
            );
        }
    }

    public function bulkReject()
    {
        $count = count($this->selected);
        if ($count > 0) {
            $users = User::whereIn('id', $this->selected)->get();
            foreach ($users as $user) {
                // Log rejection
                RegistrationLog::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'action' => 'rejected',
                    'user_data' => $user->load('agency')->toArray(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
                $user->status = 'rejected';
                $user->save();
            }

            $this->selected = [];
            $this->selectAll = false;

            $this->dispatch('swal:toast', type: 'success', message: "$count User berhasil ditolak massal!");
            $this->dispatch('update-notifications');
            $this->dispatch('refreshTable');
        }
    }


}
