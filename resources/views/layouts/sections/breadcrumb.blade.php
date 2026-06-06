@php
    if (isset($customBreadcrumbs)) {
        $breadcrumbs = $customBreadcrumbs;
    } else {
        $segments = Request::segments();
        $breadcrumbs = [];
        $url = '';

    //A    lways add Dashboard/Home
            $breadcrumbs[] =
        [
            'name' => 'Dashboard',
            'url' => route('dashboard'),
            'active' => false
        ];


        // Translation Map
        $translations = [
            'admin' => 'Admin',
            'dashboard' => 'Beranda',
            'service-catalogs' => 'Katalog Layanan',
            'agency' => 'Perangkat Daerah',
            'users' => 'Data Pengguna',
            'confirm-registrations' => 'Konfirmasi Pendaftaran',
            'rejected-registrations' => 'Riwayat Ditolak',
            'create' => 'Tambah Baru',
            'edit' => 'Ubah Data',
            'access-requests' => 'Permohonan Akses',
            'api-clients' => 'Kelola API Keys',
            'api-logs' => 'Monitoring API',
            'documentation' => 'Panduan Integrasi',
            'tickets' => 'Tiket Bantuan',
            'roles-permissions' => 'Hak Akses & Role',
            'announcements' => 'Pengumuman',
        ];

        foreach ($segments as $key => $segment) {
            // User Request: "Breadcrumb bahasa indonesia tanpa admin"
            // If segment is 'admin', skip it.
            if ($segment === 'admin') {
                $url .= '/' . $segment; // Keep appending to URL for correct links
                continue; // Skip adding to breadcrumb list
            }

            $url .= '/' . $segment;

            // Check translation or default to customized title
            $name = $translations[$segment] ?? ucwords(str_replace('-', ' ', $segment));

            // Resolve Numeric IDs
            if (is_numeric($segment)) {
                $prevSegment = $segments[$key - 1] ?? '';
                if ($prevSegment == 'service-catalogs') {
                    $catalog = \App\Models\ServiceCatalog::find($segment);
                    $name = $catalog ? \Illuminate\Support\Str::limit($catalog->name, 25) : $segment;
                }
            }

            // If it's the last segment, it's active
            $isActive = ($key == count($segments) - 1);

            $breadcrumbs[] = [
                'name' => $name,
                'url' => url($url),
                'active' => $isActive
            ];
        }
    }
@endphp

<nav aria-label="breadcrumb" class="py-2 mb-0">
    <ol class="breadcrumb breadcrumb-style1">
        @foreach($breadcrumbs as $breadcrumb)
            @if(!$loop->last)
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb['url'] }}" class="text-muted">
                        @if($breadcrumb['name'] == 'Dashboard')
                            <i class="ti ti-smart-home me-1"></i>
                        @endif
                        {{ $breadcrumb['name'] }}
                    </a>
                </li>
            @else
                <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">
                    {{ $breadcrumb['name'] }}
                </li>
            @endif
        @endforeach
    </ol>
</nav>
