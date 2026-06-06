<?php

namespace App\Livewire\Dashboard;


use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class TopStatsChart extends Component
{
    public $topStats = ['categories' => [], 'data' => []];

    public function mount()
    {
        $this->loadData();
    }

    // #[On('echo:api-logs,ApiLogCreated')] // Disabled: Echo not installed
    public function loadData()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        $this->topStats = ['categories' => [], 'data' => []];

        if ($isAdmin) {
             // Admin: Hits per Agency (ALL Agencies)
             $agencyHits = DB::table('agencies')
                ->leftJoin('users', 'agencies.id', '=', 'users.agency_id')
                ->leftJoin('api_logs', 'users.id', '=', 'api_logs.user_id')
                ->where('agencies.status', 'active')
                ->select('agencies.code', 'agencies.name', DB::raw('count(api_logs.id) as total'))
                ->groupBy('agencies.id', 'agencies.code', 'agencies.name')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

             $this->topStats['categories'] = $agencyHits->pluck('code')->toArray();
             $this->topStats['data'] = $agencyHits->pluck('total')->toArray();
             $this->topStats['names'] = $agencyHits->mapWithKeys(function ($item) {
                 return [$item->code => $item->name];
             })->toArray();

        } else {
             // User: Hits per Service Catalog (ALL Approved Services)
             $serviceHits = DB::table('service_access_requests')
                ->join('service_catalogs', 'service_access_requests.service_catalog_id', '=', 'service_catalogs.id')
                ->leftJoin('api_logs', function($join) use ($user) {
                    $join->on('service_catalogs.id', '=', 'api_logs.service_catalog_id')
                         ->where('api_logs.user_id', '=', $user->id);
                })
                ->where('service_access_requests.user_id', $user->id)
                ->where('service_access_requests.status', 'approved')
                ->select('service_catalogs.slug', 'service_catalogs.name', DB::raw('count(api_logs.id) as total'))
                ->groupBy('service_catalogs.id', 'service_catalogs.slug', 'service_catalogs.name')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

             // Use Slug as "Code" for simplicity, or generate shorter name
             $this->topStats['categories'] = $serviceHits->pluck('slug')->toArray();
             $this->topStats['data'] = $serviceHits->pluck('total')->toArray();
             $this->topStats['names'] = $serviceHits->mapWithKeys(function ($item) {
                 return [$item->slug => $item->name];
             })->toArray();
        }

        $this->dispatch('update-top-stats-chart', data: $this->topStats);
    }

    public function render()
    {
        return view('livewire.dashboard.top-stats-chart', [
            'isAdmin' => auth()->user()->hasRole('admin')
        ]);
    }
}
