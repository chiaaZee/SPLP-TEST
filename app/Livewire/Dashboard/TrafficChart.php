<?php

namespace App\Livewire\Dashboard;


use Livewire\Component;
use App\Models\ApiLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\On;

class TrafficChart extends Component
{
    public $dailyTraffic = [];

    public function mount()
    {
        $this->loadData();
    }

    // #[On('echo:api-logs,ApiLogCreated')] // Disabled: Echo not installed
    public function loadData()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $period = \Carbon\CarbonPeriod::create($startDate, '1 day', $endDate);

        $chartQuery = ApiLog::selectRaw('DATE(created_at) as date, count(*) as hits, sum(case when status_code >= 400 then 1 else 0 end) as errors')
             ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        if (!$isAdmin) {
            $chartQuery->where('user_id', $user->id);
        }

        $trafficData = $chartQuery->groupBy(DB::raw('DATE(created_at)'))->get();

        $this->dailyTraffic = [
            'categories' => [],
            'hits' => [],
            'errors' => []
        ];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $record = $trafficData->where('date', $dateString)->first();

            $this->dailyTraffic['categories'][] = $date->format('d M');
            $this->dailyTraffic['hits'][] = $record ? $record->hits : 0;
            $this->dailyTraffic['errors'][] = $record ? $record->errors : 0;
        }

        // Dispatch browser event to update chart
        $this->dispatch('update-traffic-chart', data: $this->dailyTraffic);
    }

    public function render()
    {
        return view('livewire.dashboard.traffic-chart');
    }
}
