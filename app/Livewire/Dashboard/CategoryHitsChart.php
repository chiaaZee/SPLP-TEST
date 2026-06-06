<?php

namespace App\Livewire\Dashboard;


use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class CategoryHitsChart extends Component
{
    public $categoryHitsStats = [];

    public function mount()
    {
        $this->loadData();
    }

    // #[On('echo:api-logs,ApiLogCreated')] // Disabled: Echo not installed
    public function loadData()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        $this->categoryHitsStats = [];

        if ($isAdmin) {
             // Admin: Hits per Category
             $this->categoryHitsStats = DB::table('api_logs')
                ->join('service_catalogs', 'api_logs.service_catalog_id', '=', 'service_catalogs.id')
                ->join('service_categories', 'service_catalogs.category_id', '=', 'service_categories.id')
                ->select('service_categories.name', DB::raw('count(*) as total'))
                ->groupBy('service_categories.id', 'service_categories.name')
                ->get()
                ->map(function ($item) {
                     return ['name' => $item->name, 'total' => $item->total];
                })->toArray();
        } else {
             // User: Hits per Category
             $this->categoryHitsStats = DB::table('api_logs')
                ->join('service_catalogs', 'api_logs.service_catalog_id', '=', 'service_catalogs.id')
                ->join('service_categories', 'service_catalogs.category_id', '=', 'service_categories.id')
                ->where('api_logs.user_id', $user->id)
                ->select('service_categories.name', DB::raw('count(*) as total'))
                ->groupBy('service_categories.id', 'service_categories.name')
                ->get()
                ->map(function ($item) {
                    return ['name' => $item->name, 'total' => $item->total];
                })->toArray();
        }

        $this->dispatch('update-category-hits-chart', data: $this->categoryHitsStats);
    }

    public function render()
    {
        return view('livewire.dashboard.category-hits-chart');
    }
}
