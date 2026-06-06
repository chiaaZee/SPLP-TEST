<?php

namespace Database\Seeders;

use App\Models\Agency;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencies = [
            ['code' => 'DISKOMINFO', 'name' => 'Dinas Komunikasi dan Informatika'],
            ['code' => 'DINKES', 'name' => 'Dinas Kesehatan'],
            ['code' => 'DISPENDIK', 'name' => 'Dinas Pendidikan dan Kebudayaan'],
            ['code' => 'DISHUB', 'name' => 'Dinas Perhubungan'],
            ['code' => 'DPUTR', 'name' => 'Dinas Pekerjaan Umum dan Tata Ruang'],
            ['code' => 'DINSOS', 'name' => 'Dinas Sosial'],
            ['code' => 'DISDUKCAPIL', 'name' => 'Dinas Kependudukan dan Pencatatan Sipil'],
            ['code' => 'BKPSDM', 'name' => 'Badan Kepegawaian dan Pengembangan SDM'],
            ['code' => 'BAPPEDA', 'name' => 'Badan Perencanaan Pembangunan Daerah'],
            ['code' => 'INSPEKTORAT', 'name' => 'Inspektorat Daerah'],
            ['code' => 'SATPOLPP', 'name' => 'Satuan Polisi Pamong Praja'],
        ];

        foreach ($agencies as $agency) {
            Agency::firstOrCreate(
                ['code' => $agency['code']],
                [
                    'name' => $agency['name'],
                    'status' => 'active' // Pre-approved for master data
                ]
            );
        }
    }
}
