<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
use App\Models\User;
use App\Models\ServiceCategory;
use App\Models\ServiceCatalog;
use App\Models\ServiceEndpoint;
use App\Models\ServiceAccessRequest;
use App\Models\ApiClient;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get Admin User for Ownership
        $adminUser = User::where('role', 'admin')->first();
        $adminId = $adminUser ? $adminUser->id : null;

        // 2. Create Service Categories
        $categoriesData = [
            [
                'name' => 'Kesehatan',
                'slug' => 'kesehatan',
                'description' => 'Layanan data kesehatan masyarakat, fasilitas kesehatan, dan jaminan kesehatan daerah.'
            ],
            [
                'name' => 'Pendidikan & Kebudayaan',
                'slug' => 'pendidikan-kebudayaan',
                'description' => 'Statistik pendidikan dasar, sarana perpustakaan sekolah, kearsipan, dan indeks literasi.'
            ],
            [
                'name' => 'Perhubungan & Infrastruktur',
                'slug' => 'perhubungan-infrastruktur',
                'description' => 'Data tata ruang, sanitasi perumahan, pengelolaan air bersih, dan infrastruktur wilayah.'
            ],
            [
                'name' => 'Sosial & Kependudukan',
                'slug' => 'sosial-kependudukan',
                'description' => 'Layanan data demografi, indeks gender, data ketenagakerjaan, dan perlindungan sosial.'
            ],
            [
                'name' => 'Teknologi & Informasi',
                'slug' => 'teknologi-informasi',
                'description' => 'Integrasi sistem berbasis elektronik (SPBE), aduan publik, ketahanan pangan, dan kualitas lingkungan.'
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = ServiceCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description']
                ]
            );
        }

        // 3. Define the Real Dataset Catalogs and Endpoints for Kabupaten Lumajang
        $catalogsData = [
            [
                'agency_code' => 'DINKES',
                'agency_name' => 'Dinas Kesehatan, Pengendalian Penduduk dan Keluarga Berencana',
                'category_slug' => 'kesehatan',
                'catalog' => [
                    'name' => 'Layanan Data Kesehatan dan Keluarga Berencana',
                    'slug' => 'layanan-data-kesehatan-dan-keluarga-berencana',
                    'description' => 'API Statistik pelayanan dasar kesehatan keluarga di tingkat Puskesmas Kabupaten Lumajang.',
                    'status' => 'active',
                ],
                'endpoints' => [
                    [
                        'name' => 'Cek Pelayanan Dasar Kesehatan Keluarga',
                        'slug' => 'cakupan-pelayanan-dasar-kesehatan',
                        'method' => 'GET',
                        'path' => '/dinkes/pelayanan-dasar-keluarga',
                        'url' => 'https://satudata.lumajangkab.go.id/api/v1/dssd-indicator?id=350820274',
                        'description' => 'Mengambil jumlah keluarga yang mendapatkan pelayanan dasar kesehatan melalui pendekatan keluarga.',
                        'is_public' => true,
                        'auth_mode' => 'none',
                    ],
                ]
            ],
            [
                'agency_code' => 'DPKP',
                'agency_name' => 'Dinas Perumahan dan Kawasan Permukiman',
                'category_slug' => 'perhubungan-infrastruktur',
                'catalog' => [
                    'name' => 'Layanan Data Sanitasi dan Sumber Air Rumah Tangga',
                    'slug' => 'layanan-data-sanitasi-dan-sumber-air-rumah-tangga',
                    'description' => 'Kumpulan API penyediaan data air minum, sanitasi aman, dan prasarana permukiman.',
                    'status' => 'active',
                ],
                'endpoints' => [
                    [
                        'name' => 'Presentase Rumah Tangga Berdasar Sumber Air Mandi/Cuci',
                        'slug' => 'sumber-air-mandi-cuci',
                        'method' => 'GET',
                        'path' => '/dpkp/sumber-air-mandi-cuci',
                        'url' => 'https://satudata.lumajangkab.go.id/api/v1/indicator?id=350815747',
                        'description' => 'Data persentase rumah tangga berdasarkan penggunaan bahan bakar utama dan pemanfaatan mata air terlindung.',
                        'is_public' => true,
                        'auth_mode' => 'none',
                    ],
                ]
            ],
            [
                'agency_code' => 'DINSOS',
                'agency_name' => 'Dinas Sosial, Pemberdayaan Perempuan dan Perlindungan Anak',
                'category_slug' => 'sosial-kependudukan',
                'catalog' => [
                    'name' => 'Layanan Data Pembangunan dan Kesejahteraan Gender',
                    'slug' => 'layanan-data-pembangunan-dan-kesejahteraan-gender',
                    'description' => 'API Statistik Indeks Pembangunan Gender (IPG) dan Indeks Pemberdayaan Gender.',
                    'status' => 'active',
                ],
                'endpoints' => [
                    [
                        'name' => 'Indeks Pembangunan Gender (IPG) Lumajang',
                        'slug' => 'indeks-pembangunan-gender',
                        'method' => 'GET',
                        'path' => '/dinsos/indeks-pembangunan-gender',
                        'url' => 'https://satudata.lumajangkab.go.id/api/v1/indicator?id=3508100002',
                        'description' => 'Data tren Indeks Pembangunan Gender (IPG) sektoral di Kabupaten Lumajang.',
                        'is_public' => true,
                        'auth_mode' => 'none',
                    ],
                    [
                        'name' => 'Indeks Pemberdayaan Gender Lumajang',
                        'slug' => 'indeks-pemberdayaan-gender',
                        'method' => 'GET',
                        'path' => '/dinsos/indeks-pemberdayaan-gender',
                        'url' => 'https://satudata.lumajangkab.go.id/api/v1/indicator?id=3508100003',
                        'description' => 'Statistik Indeks Pemberdayaan Gender dalam keterwakilan politik dan jabatan publik.',
                        'is_public' => true,
                        'auth_mode' => 'none',
                    ],
                ]
            ],
            [
                'agency_code' => 'DISNAKER',
                'agency_name' => 'Dinas Tenaga Kerja',
                'category_slug' => 'sosial-kependudukan',
                'catalog' => [
                    'name' => 'Layanan Data Ketenagakerjaan Daerah',
                    'slug' => 'layanan-data-ketenagakerjaan-daerah',
                    'description' => 'API Tingkat Pengangguran Terbuka (TPT) dan serapan tenaga kerja sektoral.',
                    'status' => 'active',
                ],
                'endpoints' => [
                    [
                        'name' => 'Tingkat Pengangguran Terbuka (TPT) Lumajang',
                        'slug' => 'tingkat-pengangguran-terbuka',
                        'method' => 'GET',
                        'path' => '/disnaker/tingkat-pengangguran-terbuka',
                        'url' => 'https://satudata.lumajangkab.go.id/api/v1/indicator?id=3508100009',
                        'description' => 'Mengambil data persentase angkatan kerja produktif yang belum terserap lapangan kerja.',
                        'is_public' => true,
                        'auth_mode' => 'none',
                    ],
                ]
            ],
            [
                'agency_code' => 'DISPUSIP',
                'agency_name' => 'Dinas Perpustakaan dan Kearsipan',
                'category_slug' => 'pendidikan-kebudayaan',
                'catalog' => [
                    'name' => 'Layanan Data Literasi dan Perpustakaan Daerah',
                    'slug' => 'layanan-data-literasi-dan-perpustakaan-daerah',
                    'description' => 'API Statistik Indeks Pembangunan Literasi Masyarakat (IPLM) dan sarana kearsipan perpustakaan.',
                    'status' => 'active',
                ],
                'endpoints' => [
                    [
                        'name' => 'Indeks Pembangunan Literasi Masyarakat (IPLM)',
                        'slug' => 'indeks-pembangunan-literasi-masyarakat',
                        'method' => 'GET',
                        'path' => '/dispusip/iplm',
                        'url' => 'https://satudata.lumajangkab.go.id/api/v1/indicator?id=3508100042',
                        'description' => 'Nilai pencapaian indeks literasi masyarakat Kabupaten Lumajang pertahun.',
                        'is_public' => true,
                        'auth_mode' => 'none',
                    ],
                    [
                        'name' => 'Jumlah Perpustakaan Sekolah dan Umum',
                        'slug' => 'jumlah-perpustakaan-daerah',
                        'method' => 'GET',
                        'path' => '/dispusip/jumlah-perpustakaan',
                        'url' => 'https://satudata.lumajangkab.go.id/api/v1/indicator?id=3508100043',
                        'description' => 'Data jumlah sarana gedung perpustakaan sekolah (SD/SMP) dan perpustakaan umum desa.',
                        'is_public' => true,
                        'auth_mode' => 'none',
                    ],
                ]
            ],
            [
                'agency_code' => 'DKPP',
                'agency_name' => 'Dinas Ketahanan Pangan dan Pertanian',
                'category_slug' => 'teknologi-informasi',
                'catalog' => [
                    'name' => 'Layanan Data Pangan dan Konsumsi Daerah',
                    'slug' => 'layanan-data-pangan-dan-konsumsi-daerah',
                    'description' => 'API Skor Pola Pangan Harapan (PPH) dan ketersediaan bahan pangan pokok.',
                    'status' => 'active',
                ],
                'endpoints' => [
                    [
                        'name' => 'Skor Pola Pangan Harapan Kabupaten',
                        'slug' => 'skor-pola-pangan-harapan',
                        'method' => 'GET',
                        'path' => '/dkpp/skor-pph',
                        'url' => 'https://satudata.lumajangkab.go.id/api/v1/indicator?id=3508100045',
                        'description' => 'Skor konsumsi pangan daerah berdasarkan keragaman gizi dan energi harian.',
                        'is_public' => true,
                        'auth_mode' => 'none',
                    ],
                ]
            ],
            [
                'agency_code' => 'DLH',
                'agency_name' => 'Dinas Lingkungan Hidup',
                'category_slug' => 'teknologi-informasi',
                'catalog' => [
                    'name' => 'Layanan Data Kualitas Lingkungan dan Kebersihan',
                    'slug' => 'layanan-data-kualitas-lingkungan-dan-kebersihan',
                    'description' => 'API Indeks Kualitas Udara (IKU) dan statistik daur ulang timbulan sampah perkotaan.',
                    'status' => 'active',
                ],
                'endpoints' => [
                    [
                        'name' => 'Indeks Kualitas Udara Sektoral',
                        'slug' => 'indeks-kualitas-udara',
                        'method' => 'GET',
                        'path' => '/dlh/indeks-kualitas-udara',
                        'url' => 'https://satudata.lumajangkab.go.id/api/v1/indicator?id=3508100047',
                        'description' => 'Mengambil nilai parameter kebersihan udara sektoral Kabupaten Lumajang.',
                        'is_public' => true,
                        'auth_mode' => 'none',
                    ],
                    [
                        'name' => 'Jumlah Timbulan Sampah yang Didaur Ulang',
                        'slug' => 'sampah-didaur-ulang',
                        'method' => 'GET',
                        'path' => '/dlh/sampah-daur-ulang',
                        'url' => 'https://satudata.lumajangkab.go.id/api/v1/indicator?id=3508100048',
                        'description' => 'Volume timbulan sampah yang diolah kembali/didaur ulang (ton per tahun).',
                        'is_public' => true,
                        'auth_mode' => 'none',
                    ],
                ]
            ],
        ];

        // 4. Seed Data and get catalog mapping
        $catalogs = [];
        foreach ($catalogsData as $data) {
            // Find or create agency
            $agency = Agency::firstOrCreate(
                ['code' => $data['agency_code']],
                [
                    'name' => $data['agency_name'],
                    'email' => strtolower($data['agency_code']) . '@lumajangkab.go.id',
                    'logo' => 'logo-lumajang.png',
                    'status' => 'active'
                ]
            );

            // Get Category ID
            $categoryId = isset($categories[$data['category_slug']]) ? $categories[$data['category_slug']]->id : null;

            // Create Catalog
            $catalog = ServiceCatalog::updateOrCreate(
                ['slug' => $data['catalog']['slug'], 'agency_id' => $agency->id],
                [
                    'user_id' => $adminId,
                    'category_id' => $categoryId,
                    'name' => $data['catalog']['name'],
                    'description' => $data['catalog']['description'],
                    'status' => $data['catalog']['status'],
                    'requires_mapping' => false,
                    'is_public' => true,
                    'base_url' => 'https://satudata.lumajangkab.go.id',
                    'auth_mode' => 'none',
                    'target_token' => 'sata_lmj', // Save target API key for custom gateway usage
                ]
            );

            $catalogs[$data['catalog']['slug']] = $catalog;

            // Create Endpoints
            foreach ($data['endpoints'] as $ep) {
                ServiceEndpoint::updateOrCreate(
                    ['slug' => $ep['slug']],
                    [
                        'service_catalog_id' => $catalog->id,
                        'name' => $ep['name'],
                        'method' => $ep['method'],
                        'path' => $ep['path'],
                        'url' => $ep['url'],
                        'description' => $ep['description'],
                        'is_public' => $ep['is_public'],
                        'auth_mode' => $ep['auth_mode'],
                    ]
                );
            }
        }

        // 5. Create Agency PIC Users
        $dbAgencies = Agency::whereIn('code', ['DINKES', 'DPKP', 'DINSOS', 'DISNAKER', 'DISPUSIP', 'DKPP', 'DLH'])->get();
        $dinasUsers = [];
        foreach ($dbAgencies as $agency) {
            $email = strtolower($agency->code) . '_pic@lumajangkab.go.id';
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'PIC ' . $agency->name,
                    'password' => 'password', // Will be hashed automatically by model cast
                    'role' => 'dinas',
                    'agency_id' => $agency->id,
                    'status' => 'active',
                ]
            );
            $dinasUsers[$agency->code] = $user;
        }

        // 6. Create Service Access Requests
        $requestsData = [
            [
                'requester_code' => 'DINKES',
                'target_slug' => 'layanan-data-sanitasi-dan-sumber-air-rumah-tangga', // Owned by DPKP
                'reason' => 'Untuk mengintegrasikan data sanitasi rumah tangga guna memetakan sebaran sanitasi buruk dan kaitannya dengan pencegahan stunting balita di Lumajang.',
                'status' => 'approved',
            ],
            [
                'requester_code' => 'DINSOS',
                'target_slug' => 'layanan-data-sanitasi-dan-sumber-air-rumah-tangga', // Owned by DPKP
                'reason' => 'Verifikasi kriteria kepemilikan sanitasi layak bagi keluarga penerima manfaat program bantuan sosial Kabupaten Lumajang.',
                'status' => 'approved',
            ],
            [
                'requester_code' => 'DISNAKER',
                'target_slug' => 'layanan-data-pembangunan-dan-kesejahteraan-gender', // Owned by DINSOS
                'reason' => 'Sinkronisasi data ketenagakerjaan wanita dengan Indeks Pemberdayaan Gender Lumajang.',
                'status' => 'approved',
            ],
            [
                'requester_code' => 'DLH',
                'target_slug' => 'layanan-data-literasi-dan-perpustakaan-daerah', // Owned by DISPUSIP
                'reason' => 'Kerjasama pembentukan pojok baca peduli lingkungan (eco-library) di taman kota.',
                'status' => 'pending',
            ]
        ];

        foreach ($requestsData as $req) {
            $user = $dinasUsers[$req['requester_code']] ?? null;
            $catalog = $catalogs[$req['target_slug']] ?? null;

            if ($user && $catalog) {
                ServiceAccessRequest::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'service_catalog_id' => $catalog->id,
                    ],
                    [
                        'reason' => $req['reason'],
                        'status' => $req['status'],
                        'admin_note' => $req['status'] === 'approved' ? 'Disetujui oleh Administrator untuk kebutuhan interoperabilitas statistik sektoral.' : ($req['status'] === 'rejected' ? 'Ditolak: Hubungan kerja antar-dinas kurang relevan.' : null),
                    ]
                );
            }
        }

        // 7. Create API Clients for Approved Connections
        $clientsData = [
            [
                'requester_code' => 'DINKES',
                'target_slug' => 'layanan-data-sanitasi-dan-sumber-air-rumah-tangga',
            ],
            [
                'requester_code' => 'DINSOS',
                'target_slug' => 'layanan-data-sanitasi-dan-sumber-air-rumah-tangga',
            ]
        ];

        $apiClients = [];
        foreach ($clientsData as $clientInfo) {
            $user = $dinasUsers[$clientInfo['requester_code']] ?? null;
            $catalog = $catalogs[$clientInfo['target_slug']] ?? null;

            if ($user && $catalog) {
                $credentials = ApiClient::generateCredentials();
                $apiClients[] = ApiClient::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'service_catalog_id' => $catalog->id,
                    ],
                    [
                        'name' => 'Kunci API ' . $clientInfo['requester_code'] . ' -> ' . $catalog->name,
                        'api_key' => $credentials['api_key'],
                        'secret_key' => $credentials['secret_key'],
                        'status' => 'active',
                        'mapping_config' => ['skpd_code' => [$clientInfo['requester_code']]],
                        'last_used_at' => now()->subMinutes(rand(5, 120)),
                    ]
                );
            }
        }

        // 8. Seed API Logs over the last 7 days to simulate real usage traffic
        $statusCodes = [200, 200, 200, 200, 200, 200, 200, 200, 200, 201, 304, 400, 404, 500];
        $endpoints = ServiceEndpoint::all();

        if ($endpoints->isNotEmpty() && !empty($apiClients)) {
            for ($i = 0; $i < 200; $i++) {
                $endpoint = $endpoints->random();
                $catalog = $endpoint->catalog;
                
                // Select a client (some logs are authenticated, some are public)
                $client = (rand(0, 10) > 3) ? collect($apiClients)->random() : null;
                $user = $client ? $client->user : ($adminUser ?: null);

                $statusCode = $statusCodes[array_rand($statusCodes)];
                $duration = $statusCode >= 500 ? rand(1000, 3000) : rand(30, 250); 
                
                // Distribute timestamps over the last 7 days
                $createdAt = now()->subHours(rand(1, 168));

                \DB::table('api_logs')->insert([
                    'user_id' => $user ? $user->id : null,
                    'api_client_id' => $client ? $client->id : null,
                    'service_catalog_id' => $catalog->id,
                    'method' => $endpoint->method,
                    'endpoint' => $endpoint->path ?? '/' . $endpoint->slug,
                    'status_code' => $statusCode,
                    'ip_address' => '192.168.' . rand(1, 254) . '.' . rand(1, 254),
                    'duration_ms' => $duration,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'request_header' => json_encode(['Content-Type' => 'application/json', 'Authorization' => 'Bearer ...']),
                    'request_body' => null,
                    'response_body' => json_encode(['status' => $statusCode == 200 ? 'success' : 'error', 'data' => []]),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}
