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
            ['code' => 'DINKES', 'name' => 'Dinas Kesehatan, Pengendalian Penduduk dan Keluarga Berencana'],
            ['code' => 'DISPENDIK', 'name' => 'Dinas Pendidikan dan Kebudayaan'],
            ['code' => 'DISHUB', 'name' => 'Dinas Perhubungan'],
            ['code' => 'DPUTR', 'name' => 'Dinas Pekerjaan Umum dan Tata Ruang'],
            ['code' => 'DINSOS', 'name' => 'Dinas Sosial, Pemberdayaan Perempuan dan Perlindungan Anak'],
            ['code' => 'DISDUKCAPIL', 'name' => 'Dinas Kependudukan dan Pencatatan Sipil'],
            ['code' => 'BKPSDM', 'name' => 'Badan Kepegawaian dan Pengembangan SDM'],
            ['code' => 'BAPPEDA', 'name' => 'Badan Perencanaan Pembangunan Daerah'],
            ['code' => 'INSPEKTORAT', 'name' => 'Inspektorat Daerah'],
            ['code' => 'SATPOLPP', 'name' => 'Satuan Polisi Pamong Praja'],
            ['code' => 'DPKP', 'name' => 'Dinas Perumahan dan Kawasan Permukiman'],
            ['code' => 'DISNAKER', 'name' => 'Dinas Tenaga Kerja'],
            ['code' => 'DISPUSIP', 'name' => 'Dinas Perpustakaan dan Kearsipan'],
            ['code' => 'DKPP', 'name' => 'Dinas Ketahanan Pangan dan Pertanian'],
            ['code' => 'DLH', 'name' => 'Dinas Lingkungan Hidup'],
        ];

        foreach ($agencies as $agency) {
            Agency::firstOrCreate(
                ['code' => $agency['code']],
                [
                    'name' => $agency['name'],
                    'logo' => 'logo-lumajang.png',
                    'status' => 'active' // Pre-approved for master data
                ]
            );
        }
    }
}
