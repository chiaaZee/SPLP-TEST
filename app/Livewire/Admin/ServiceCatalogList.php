<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ServiceCatalog;
use App\Models\ServiceAccessRequest;
use Illuminate\Support\Facades\Auth;

class ServiceCatalogList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterCategory = '';

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshCatalogList' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ServiceCatalog::with('agency', 'category')
            ->withCount('endpoints')
            ->latest();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('agency', function($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        $catalogs = $query->paginate(12); // Grid friendly number (2, 3, 4 columns)
        $categories = \App\Models\ServiceCategory::orderBy('name')->get();

        // User Requests logic
        $userRequests = collect();
        if (Auth::check()) {
            $userRequests = ServiceAccessRequest::where('user_id', Auth::id())
                ->get()
                ->sortByDesc('created_at')
                ->groupBy('service_catalog_id')
                ->map->first();
        }

        return view('livewire.admin.service-catalog-list', [
            'catalogs' => $catalogs,
            'userRequests' => $userRequests,
            'categories' => $categories,
        ]);
    }
}
