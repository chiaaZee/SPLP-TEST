<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
use App\Models\ServiceCatalog;
use App\Models\ServiceEndpoint;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Diskominfo exists
        $agency = Agency::firstOrCreate(
            ['code' => 'DISKOMINFO'],
            [
                'name' => 'Dinas Komunikasi dan Informatika',
                'email' => 'diskominfo@lumajangkab.go.id',
                'status' => 'active'
            ]
        );

        // Create Demo Catalog
        $catalog = ServiceCatalog::updateOrCreate(
            ['slug' => 'layanan-data-kependudukan', 'agency_id' => $agency->id],
            [
                'name' => 'Layanan Data Kependudukan',
                'description' => 'Kumpulan API untuk mengakses data kependudukan, KK, dan KTP secara terintegrasi.',
                'status' => 'active'
            ]
        );

        // Endpoints
        ServiceEndpoint::create([
            'service_catalog_id' => $catalog->id,
            'name' => 'Cek Data Penduduk (List All)',
            'method' => 'GET',
            'url' => 'https://jsonplaceholder.typicode.com/users',
            'description' => 'Mengambil semua data penduduk.'
        ]);

        ServiceEndpoint::create([
            'service_catalog_id' => $catalog->id,
            'name' => 'Cek Detail Penduduk by ID',
            'method' => 'GET',
            'url' => 'https://jsonplaceholder.typicode.com/users/1',
            'description' => 'Mengambil detail satu penduduk berdasarkan ID.'
        ]);
    }
}
