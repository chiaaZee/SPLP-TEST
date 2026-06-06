<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Agency;
use App\Models\ServiceCatalog;
use App\Models\ApiLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $totalInstansi;
    public $totalConnectedInstansi;
    public $totalLayanan;
    public $totalTransaksi;
    public $errorRate;
    public $avgLatency; // Added for V2 parity

    public $dailyTraffic = [];
    public $topAgencies = [];
    public $categoryStats = [];
    public $popularCategories = [];
    public $recentLogs = [];

    public function mount()
    {
        // 1. Big Numbers
        $this->totalInstansi = Agency::where('status', 'active')->count();

        // 1b. Connected Agencies (Have at least 1 successful API request 200-299)
        $this->totalConnectedInstansi = Agency::whereHas('users.apiLogs', function ($query) {
            $query->whereBetween('status_code', [200, 299]);
        })->count();

        $this->totalLayanan = ServiceCatalog::count();
        $this->totalTransaksi = ApiLog::count();

        // Error Rate Calculation
        $errorCount = ApiLog::where('status_code', '>=', 400)->count();
        $this->errorRate = $this->totalTransaksi > 0
            ? round(($errorCount / $this->totalTransaksi) * 100, 1)
            : 0;

        // 2. Charts: Daily Traffic (Last 7 Days)
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        // Fetch hits and errors grouped by date
        $trafficQuery = ApiLog::selectRaw('DATE(created_at) as date, count(*) as hits, sum(case when status_code >= 400 then 1 else 0 end) as errors')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        $this->dailyTraffic = [
            'categories' => [],
            'hits' => [],
            'errors' => []
        ];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $record = $trafficQuery->where('date', $dateString)->first();

            $this->dailyTraffic['categories'][] = $date->format('d M');
            $this->dailyTraffic['hits'][] = $record ? $record->hits : 0;
            $this->dailyTraffic['errors'][] = $record ? $record->errors : 0;
        }

        // Calculate Average Latency (All Time for now, or 7 days if preferred - matching Satu Data logic which is All Time usually)
        $this->avgLatency = round(ApiLog::avg('duration_ms') ?? 0);


        // 3. Charts: Active Agencies (All)
        $this->topAgencies = Agency::leftJoin('users', 'agencies.id', '=', 'users.agency_id')
            ->leftJoin('api_logs', 'users.id', '=', 'api_logs.user_id')
            ->select('agencies.name', DB::raw('count(api_logs.id) as total'))
            ->groupBy('agencies.id', 'agencies.name')
            ->orderByDesc('total')
            ->get()
            ->toArray();

        // 4. Charts: Category Distribution (Donut)
        $this->categoryStats = \App\Models\ServiceCategory::withCount('services')
            ->having('services_count', '>', 0)
            ->get()
            ->map(function ($cat) {
                return ['name' => $cat->name, 'total' => $cat->services_count];
            })->toArray();

        // 5. List: Popular Categories (Based on API Hits)
        $this->popularCategories = DB::table('api_logs')
            ->join('service_catalogs', 'api_logs.service_catalog_id', '=', 'service_catalogs.id')
            ->join('service_categories', 'service_catalogs.category_id', '=', 'service_categories.id')
            ->select('service_categories.name', DB::raw('count(api_logs.id) as total'))
            ->groupBy('service_categories.id', 'service_categories.name')
            ->orderByDesc('total')
            ->take(5) // Keep top 5 for popularity
            ->get()
            ->map(function ($cat) {
                return ['name' => $cat->name, 'total' => $cat->total];
            })->toArray();

        // 6. Recent Logs
        $this->recentLogs = ApiLog::with(['user.agency', 'catalog'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        $this->dispatch('init-charts', [
            'traffic' => $this->dailyTraffic,
            'agencies' => $this->topAgencies,
            'categories' => $this->categoryStats,
            'popular' => $this->popularCategories
        ]);

        return view('livewire.admin.dashboard');
    }
}
