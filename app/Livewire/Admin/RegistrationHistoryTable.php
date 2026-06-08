<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class RegistrationHistoryTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $sortField = 'updated_at'; // Sort by update time (decision time)
    public $sortDirection = 'desc';
    public $showBanner = true;

    public function mount()
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized.');
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
            ->whereIn('status', ['active', 'rejected']) // Show History (Approved/Rejected)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('agency', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $users = $this->getUsersQuery()->paginate($this->perPage);
        return view('livewire.admin.registration-history-table', [
            'users' => $users
        ]);
    }
}
