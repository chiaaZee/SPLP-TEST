<?php

namespace App\Livewire\Dashboard;


use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\ApiLog;

class RecentActivity extends Component
{
    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        $recentActivities = DB::table('api_logs')
            ->join('users', 'api_logs.user_id', '=', 'users.id')
            ->leftJoin('agencies', 'users.agency_id', '=', 'agencies.id')
            ->join('service_catalogs', 'api_logs.service_catalog_id', '=', 'service_catalogs.id')
            ->leftJoin('api_clients', 'api_logs.api_client_id', '=', 'api_clients.id')
            ->select(
                'api_logs.*',
                'users.name as user_name',
                'users.email as user_email',
                'agencies.name as agency_name',
                'service_catalogs.name as service_name',
                'api_clients.mapping_config'
            )
            ->when(!$isAdmin, function($query) use ($user) {
                return $query->where('api_logs.user_id', $user->id);
            })
            ->orderByDesc('api_logs.created_at')
            ->limit(10)
            ->get();

        // Post-processing to resolve Agency Name from API Client Mappings if user agency is null
        if ($isAdmin && $recentActivities->isNotEmpty()) {
            $skpdCodes = [];
            foreach ($recentActivities as $log) {
                if (empty($log->agency_name) && !empty($log->mapping_config)) {
                    $config = json_decode($log->mapping_config, true);
                    if (!empty($config['skpd_code'])) {
                        $skpdCodes[] = $config['skpd_code'];
                    }
                }
            }

            $mappedAgencyNames = [];
            if (!empty($skpdCodes)) {
                $mappedAgencyNames = \App\Models\Agency::whereIn('code', array_unique($skpdCodes))
                    ->pluck('name', 'code')
                    ->toArray();
            }

            // Apply names
            $recentActivities->transform(function ($log) use ($mappedAgencyNames) {
                if (empty($log->agency_name) && !empty($log->mapping_config)) {
                    $config = json_decode($log->mapping_config, true);
                    if (!empty($config['skpd_code']) && isset($mappedAgencyNames[$config['skpd_code']])) {
                        $log->agency_name = $mappedAgencyNames[$config['skpd_code']];
                    }
                }
                return $log;
            });
        }

        return view('livewire.dashboard.recent-activity', [
            'recentActivities' => $recentActivities,
            'isAdmin' => $isAdmin
        ]);
    }
}
