<?php

namespace App\Livewire\Dashboard;


use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class PopularCategoryChart extends Component
{
    public $categoryStats = [];

    public function mount()
    {
        $this->loadData();
    }

    // #[On('echo:api-logs,ApiLogCreated')] // Disabled: Echo not installed
    public function loadData()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        $this->categoryStats = [];

        if ($isAdmin) {
             // Admin: Count services per category
             $this->categoryStats = \App\Models\ServiceCategory::withCount('services')
                ->having('services_count', '>', 0)
                ->get()
                ->map(function ($cat) {
                    return ['name' => $cat->name, 'total' => $cat->services_count];
                })->toArray();
        } else {
             // User: Count approved services per category
             $this->categoryStats = DB::table('service_access_requests')
                ->join('service_catalogs', 'service_access_requests.service_catalog_id', '=', 'service_catalogs.id')
                ->join('service_categories', 'service_catalogs.category_id', '=', 'service_categories.id')
                ->where('service_access_requests.user_id', $user->id)
                ->where('service_access_requests.status', 'approved')
                ->select('service_categories.name', DB::raw('count(*) as total'))
                ->groupBy('service_categories.id', 'service_categories.name')
                ->get()
                ->map(function ($item) {
                    return ['name' => $item->name, 'total' => $item->total];
                })->toArray();
        }

        $this->dispatch('update-popular-category-chart', data: $this->categoryStats);
    }

    public function render()
    {
        return view('livewire.dashboard.popular-category-chart', [
            'isAdmin' => auth()->user()->hasRole('admin')
        ]);
    }
}
