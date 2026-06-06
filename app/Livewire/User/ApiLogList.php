<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ServiceCatalog;
use App\Models\ServiceAccessRequest;

class ApiLogList extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $catalogs = $this->getCatalogs();

        return view('livewire.user.api-log-list', [
            'catalogs' => $catalogs
        ]);
    }

    private function getCatalogs()
    {
        $isAdmin = auth()->user()->hasRole('admin');

        $query = ServiceCatalog::query();

        // 1. Role Filtering
        if (!$isAdmin) {
             $approvedIds = ServiceAccessRequest::where('user_id', auth()->id())
                ->where('status', 'approved')
                ->pluck('service_catalog_id');

            $query->whereIn('id', $approvedIds);
        }

        // 2. Search
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        // 3. Eager Loading Stats removed in favor of standardized health_stats in loop
        // If performance becomes an issue, we can eager load aggregated stats later.

        $catalogs = $query->orderBy('name')->get();

        // 4. Calculate Stats & Health (Post-processing)
        foreach ($catalogs as $catalog) {
            // Use standardized health stats (Global 24h)
            $stats = $catalog->health_stats;

            $catalog->hits_24h = $stats['total_hits'];
            $catalog->avg_latency = $stats['avg_latency'];
            $catalog->error_count = $stats['error_count'];

            $catalog->health_status = $stats['status'];
            $catalog->health_color = $stats['color'];
        }

        return $catalogs;
    }
}
