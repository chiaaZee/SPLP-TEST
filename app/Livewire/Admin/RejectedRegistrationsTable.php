<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RegistrationLog;

class RejectedRegistrationsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
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

    public function getLogsQuery()
    {
        return RegistrationLog::query()
            ->where('action', 'rejected')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('user_data', 'like', '%' . $this->search . '%'); // JSON search might be slow but functional for basic needs
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $logs = $this->getLogsQuery()->paginate($this->perPage);
        return view('livewire.admin.rejected-registrations-table', [
            'logs' => $logs
        ]);
    }
}
