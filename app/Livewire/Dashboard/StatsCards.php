<?php

namespace App\Livewire\Dashboard;


use Livewire\Component;
use App\Models\ApiLog;
use App\Models\Agency;
use App\Models\ServiceCatalog;
use App\Models\ServiceAccessRequest;

class StatsCards extends Component
{
    public $totalTransactions;
    public $successRate = 0;
    public $totalLayanan;
    public $totalConnectedInstansi;
    public $errorRate;

    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        // Query Base
        $query = ApiLog::query();
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $this->totalTransactions = $query->count();
        $errorCount = (clone $query)->where('status_code', '>=', 400)->count();

        // Success Rate
        $this->successRate = $this->totalTransactions > 0
            ? round((($this->totalTransactions - $errorCount) / $this->totalTransactions) * 100, 1)
            : 100;

        $this->errorRate = 100 - $this->successRate;

        if ($isAdmin) {
             $this->totalLayanan = ServiceCatalog::count(); // Total Katalog

             // Instansi Terdaftar
             // Note: Reuse variable naming from Controller but logic specifically for "Active Agencies"
             // In controller: totalInstansi = Agency count.
             // We'll pass both if needed, but for the cards we need to match the View logic.
             // Card 1: Admin (Instansi) vs User (Katalog Disetujui)
             $totalInstansi = Agency::where('status', 'active')->count();

             // Card 2: Admin (Total Katalog) vs User (Katalog Terhubung)
             // Connected: Agencies having users who have successful logs OR Mapped via API Keys

             // 1. Base: Agencies of Users who have logs (Original Logic)
             $userIds = ApiLog::whereBetween('status_code', [200, 299])->distinct()->pluck('user_id');
             $connectedAgencyIds = \App\Models\User::whereIn('id', $userIds)
                ->whereNotNull('agency_id')
                ->distinct()
                ->pluck('agency_id')
                ->toArray();

             // 2. Extension: Agencies mapped via API Clients involved in logs
             $clientIds = ApiLog::whereBetween('status_code', [200, 299])
                ->whereNotNull('api_client_id')
                ->distinct()
                ->pluck('api_client_id');

             if ($clientIds->isNotEmpty()) {
                $clients = \App\Models\ApiClient::whereIn('id', $clientIds)->get();
                // Map Code -> ID for lookup
                $agencyCodeMap = Agency::where('status', 'active')->pluck('id', 'code')->toArray();

                foreach ($clients as $client) {
                    if (!empty($client->mapping_config['skpd_code'])) {
                        $code = $client->mapping_config['skpd_code'];
                        if (isset($agencyCodeMap[$code])) {
                            $connectedAgencyIds[] = $agencyCodeMap[$code];
                        }
                    }
                }
             }

             $this->totalConnectedInstansi = count(array_unique($connectedAgencyIds));

             // Adjust variables to match view expectation if generic
             // Just pass exact values to view

             return view('livewire.dashboard.stats-cards', [
                'isAdmin' => $isAdmin,
                'card1_value' => $totalInstansi,
                'card1_label' => 'Perangkat Daerah Terdaftar',
                'card1_sub'   => $this->totalConnectedInstansi . ' Terhubung',

                'card2_value' => $this->totalLayanan,
                'card2_label' => 'Total Katalog',
                'card2_sub'   => 'Active',
             ]);

        } else {
             // User specific stats
             // 1. Approved Catalogs
             $this->totalLayanan = ServiceAccessRequest::where('user_id', $user->id)->where('status', 'approved')->count();

             // 2. Connected Catalogs (Has at least one 200 OK log)
             $this->totalConnectedInstansi = ApiLog::where('user_id', $user->id)
                ->whereBetween('status_code', [200, 299])
                ->distinct('service_catalog_id')
                ->count('service_catalog_id');

             return view('livewire.dashboard.stats-cards', [
                'isAdmin' => $isAdmin,
                'card1_value' => $this->totalLayanan,
                'card1_label' => 'Layanan Digunakan',
                'card1_sub'   => '',

                'card2_value' => $this->totalConnectedInstansi,
                'card2_label' => 'Layanan Terhubung',
                'card2_sub'   => 'Active',
             ]);
        }
    }
}
