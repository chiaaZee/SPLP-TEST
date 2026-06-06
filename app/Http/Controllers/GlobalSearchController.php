<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceCatalog;
use App\Models\Agency;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->get('search'); // Ignored for now as we load all data for client-side filtering (typeahead expectation)
        // If we want real server side search, typeahead config needs to change to remote.
        // For now, let's replicate the structure expected by main.js: { pages: [], files: [], members: [] }

        // Mapped Structure:
        // Pages -> Menu items / Features
        // Files -> API Services (Service Catalogs)
        // Members -> Agencies (Instansi)

        $data = [
            'pages' => $this->getPages(),
            'files' => $this->getServices(),
            'members' => $this->getAgencies()
        ];

        return response()->json($data);
    }

    private function getPages()
    {
        // Define key pages manually or dynamically
        $pages = [
            ['name' => 'Dashboard', 'url' => 'dashboard', 'icon' => 'ti-smart-home'],
            ['name' => 'Katalog Layanan API', 'url' => 'admin/service-catalogs', 'icon' => 'ti-layout-grid'],
            ['name' => 'Monitoring API', 'url' => 'api-logs', 'icon' => 'ti-chart-bar'],
            ['name' => 'Kelola API Keys', 'url' => 'api-clients', 'icon' => 'ti-shield-lock'],
            ['name' => 'Tiket Bantuan', 'url' => 'tickets', 'icon' => 'ti-ticket'],
            ['name' => 'Profil Saya', 'url' => 'profile', 'icon' => 'ti-user'],
            ['name' => 'Dokumentasi', 'url' => 'documentation', 'icon' => 'ti-book'],
        ];

        if (auth()->user()->hasRole('admin')) {
            $pages[] = ['name' => 'Data User', 'url' => 'admin/users', 'icon' => 'ti-users'];
            $pages[] = ['name' => 'Data Instansi', 'url' => 'admin/agency', 'icon' => 'ti-building'];
            $pages[] = ['name' => 'Role & Permission', 'url' => 'admin/roles-permissions', 'icon' => 'ti-lock'];
        }

        return $pages;
    }

    private function getServices()
    {
        // Fetch active service catalogs
        $services = ServiceCatalog::select('id', 'name', 'description')
            ->where('status', 'active') // Changed from is_active to status
            ->limit(20)
            ->get();

        return $services->map(function ($service) {
            return [
                'name' => $service->name,
                'subtitle' => Str::limit($service->description, 50),
                'src' => 'img/icons/misc/doc.png', // Generic icon or use specific logo if available
                'meta' => 'API',
                'url' => 'admin/service-catalogs/' . $service->id // Assuming this is the detail URL
            ];
        })->toArray(); // map returns collection, we need array? response()->json handles collection fine but main.js expects array structure.
    }

    private function getAgencies()
    {
        if (!auth()->user()->hasRole('admin')) {
            return []; // Only admin sees agencies? Or public sees them? Let's show to all for now or restrict.
            // If restrict: return [];
        }

        $agencies = Agency::select('id', 'name', 'code')
            ->limit(10)
            ->get();

        return $agencies->map(function ($agency) {
            return [
                 'name' => $agency->name,
                 'subtitle' => $agency->code,
                 'src' => 'img/avatars/1.png', // Placeholder
                 'url' => 'admin/agency/' . $agency->id . '/edit' // Edit url or detail
            ];
        })->toArray();
    }
}
