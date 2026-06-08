<div>
    <!-- Render specific styles from original _endpoints_list -->
    <style>
        .endpoint-card {
            transition: all 0.3s ease-in-out;
            border: 1px solid transparent;
        }

        .endpoint-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        html.dark .endpoint-card:hover {
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.1) !important;
        }
        /* Method Badge Styles */
        .method-badge {
            font-weight: 800;
            font-size: 0.8rem;
            padding: 0.5rem 0.8rem;
            border-radius: 0.375rem;
            min-width: 70px;
            text-align: center;
            display: inline-block;
        }
        .method-GET { background-color: rgba(40, 199, 111, 0.15); color: #28c76f; }
        .method-POST { background-color: rgba(255, 159, 67, 0.15); color: #ff9f43; }
        .method-PUT { background-color: rgba(0, 207, 232, 0.15); color: #00cfe8; }
        .method-DELETE { background-color: rgba(234, 84, 85, 0.15); color: #ea5455; }
        .method-PATCH { background-color: rgba(115, 103, 240, 0.15); color: #7367f0; }
    </style>

    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4 position-relative">
                    <div class="row align-items-center">
                        <div class="col-md-8 z-1">
                            <h3 class="text-white fw-bold mb-1">Detail Layanan API</h3>
                            <p class="text-white opacity-75 mb-0">Kelola endpoint, pantau statistik, dan atur akses layanan ini.</p>
                        </div>
                    </div>
                    <i class="ti ti-server-2 position-absolute text-white opacity-25"
                        style="font-size: 8rem; right: 1rem; bottom: -2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex flex-column">
                    <h4 class="mb-1">{{ $catalogData['name'] }}</h4>
                    <p class="mb-0 text-muted">{{ $catalogData['agency']['name'] ?? 'Unknown Agency' }}</p>
                    <p class="mt-2">{{ $catalogData['description'] ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                        <span class="badge bg-label-{{ $catalogData['status'] == 'active' ? 'success' : 'secondary' }}">
                            <i class="ti ti-{{ $catalogData['status'] == 'active' ? 'check' : 'ban' }} me-1"></i>
                            {{ $catalogData['status'] == 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        <span class="badge bg-label-primary cursor-pointer" data-bs-toggle="tooltip" title="Gateway Base URL">
                            <i class="ti ti-link me-1"></i> {{ url('/') }}/api/{{ $catalogData['slug'] }}
                        </span>
                        @if($catalogData['base_url'] && auth()->user()->can('manage_catalogs'))
                            <span class="badge bg-label-secondary cursor-pointer" data-bs-toggle="tooltip"
                                title="Target URL: {{ $catalogData['base_url'] }}">
                                <i class="ti ti-server me-1"></i> Target
                            </span>
                        @endif
                        <span class="badge bg-label-danger"><i class="ti ti-gauge me-1"></i> Limit: {{ $catalogData['rate_limit'] ?? 60 }} req/min</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @can('manage_catalogs')
                    <button class="btn btn-primary" wire:click="editCatalog" wire:loading.attr="disabled">
                        <i class="ti ti-edit me-1"></i> Edit
                    </button>
                    <button class="btn btn-outline-{{ $catalogData['status'] == 'active' ? 'warning' : 'success' }}"
                        wire:click="confirmToggleStatus" wire:loading.attr="disabled">
                        <i class="ti ti-{{ $catalogData['status'] == 'active' ? 'player-pause' : 'player-play' }} me-1"></i>
                        {{ $catalogData['status'] == 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                    <button class="btn btn-danger delete-catalog-btn" data-id="{{ $catalogData['slug'] }}" data-name="{{ $catalogData['name'] }}" data-redirect="true" wire:loading.attr="disabled">
                        <i class="ti ti-trash me-1"></i> Hapus
                    </button>
                    @endcan
                    <button class="btn btn-label-secondary" onclick="history.back()"><i class="ti ti-arrow-left me-1"></i> Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats & Quick Integration Row -->
    <div class="row mb-4">
        <!-- Stats Column -->
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="row g-4">
                <!-- Total Hits -->
                <div class="col-sm-6 col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-label-primary rounded p-2 me-2"><i class="ti ti-chart-bar ti-sm"></i></span>
                                <small class="text-muted text-uppercase fw-bold">Total Hits</small>
                            </div>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['total_hits'] ?? 0) }}</h4>
                            <small class="text-success fw-bold"><i class="ti ti-arrow-up"></i> Last 7 Days</small>
                        </div>
                    </div>
                </div>
                <!-- Users / Connection Status -->
                @if(auth()->user()->hasRole('admin'))
                    <div class="col-sm-6 col-md-4">
                        <div class="card h-100 border-0 shadow-sm cursor-pointer hover-card" wire:click="openConnectedAgenciesModal">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-label-info rounded p-2 me-2"><i class="ti ti-users ti-sm"></i></span>
                                    <small class="text-muted text-uppercase fw-bold">Pengguna</small>
                                </div>
                                <h4 class="mb-0 fw-bold">{{ number_format($stats['users_count'] ?? 0) }}</h4>
                                <small class="text-muted">Perangkat Daerah Terhubung</small>
                            </div>
                        </div>
                    </div>
                @else
                    @php
                        $isConnected = false;
                        $statusText = 'Belum Ada Aktivitas';
                        $statusColor = 'secondary';
                        $icon = 'plug-connected-x';
                        $textColor = 'text-heading';

                        if(isset($stats['last_used']) && $stats['last_used']) {
                            $lastMs = \Carbon\Carbon::parse($stats['last_used']);
                            if($lastMs->diffInHours(now()) < 24) {
                                $isConnected = true;
                                $statusText = 'Terhubung';
                                $statusColor = 'success';
                                $icon = 'plug-connected';
                                $textColor = 'text-success';
                            } else {
                                $statusText = 'Idle (' . $lastMs->diffForHumans() . ')';
                                $statusColor = 'warning';
                                $icon = 'plug';
                                $textColor = 'text-warning';
                            }
                        }
                    @endphp
                    <div class="col-sm-6 col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-label-{{ $statusColor }} rounded p-2 me-2"><i class="ti ti-{{ $icon }} ti-sm"></i></span>
                                    <small class="text-muted text-uppercase fw-bold">Status Koneksi</small>
                                </div>
                                <h4 class="mb-0 fw-bold {{ $textColor }}">{{ $isConnected ? 'Active' : ($stats['last_used'] ? 'Inactive' : 'Idle') }}</h4>
                                <small class="text-muted">{{ $statusText }}</small>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Error Rate / Health -->
                <div class="col-sm-6 col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-label-{{ $stats['health_color'] ?? 'secondary' }} rounded p-2 me-2">
                                    <i class="ti ti-{{ $stats['health_icon'] ?? 'activity' }} ti-sm"></i>
                                </span>
                                <small class="text-muted text-uppercase fw-bold">Health</small>
                            </div>
                            <h4 class="mb-0 fw-bold {{ $stats['health_color'] == 'success' ? 'text-success' : ($stats['health_color'] == 'danger' ? 'text-danger' : ($stats['health_color'] == 'warning' ? 'text-warning' : '')) }}">
                                {{ $stats['health_status'] ?? 'No Data' }}
                            </h4>
                            <small class="text-muted">
                                Based on last 24h
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Description / Meta -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3"><i class="ti ti-file-description me-1"></i> Deskripsi Layanan</h6>
                                <p class="text-muted mb-0">{{ $catalogData['description'] ?? 'Belum ada deskripsi untuk layanan ini.' }}</p>

                                <div class="mt-4 pt-3 border-top d-flex gap-4">
                                    <div>
                                        <small class="text-muted d-block mb-1">Dikelola Oleh</small>
                                        <span class="fw-bold text-heading">{{ $catalogData['agency']['name'] ?? '-' }}</span>
                                    </div>
                                    <div class="vr"></div>
                                    <div>
                                        <small class="text-muted d-block mb-1">Terakhir Diupdate</small>
                                        <span class="fw-bold text-heading">{{ \Carbon\Carbon::parse($catalogData['updated_at'])->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Integration Guide -->
        <div class="col-lg-4">
            <div class="card h-100 border-primary shadow-sm" style="border: 2px solid var(--bs-primary) !important;">
                <div class="card-header bg-label-primary d-flex justify-content-between align-items-center py-3">
                    <h6 class="m-0 text-primary fw-bold"><i class="ti ti-code me-1"></i> Quick Integration</h6>
                    <span class="badge bg-white text-primary">REST API</span>
                </div>
                <div class="card-body pt-3">
                    <!-- Base URL -->
                    <div class="mb-3">
                        <label class="small text-uppercase fw-bold text-muted mb-1">Base URL</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" value="{{ url('/') }}/api/{{ $catalogData['slug'] }}" readonly id="quickBaseUrl">
                            <button class="btn btn-primary" onclick="copyToClipboard('#quickBaseUrl')"><i class="ti ti-copy"></i></button>
                        </div>
                    </div>

                    <!-- Headers -->
                    <div class="mb-3">
                        <label class="small text-uppercase fw-bold text-muted mb-1">Required Headers</label>
                        <ul class="list-group list-group-flush border rounded small">
                            <li class="list-group-item d-flex justify-content-between p-2">
                                <span class="font-monospace text-heading">X-SPLP-Client-ID</span>
                                <span class="text-muted">Your Client ID</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between p-2">
                                <span class="font-monospace text-heading">X-SPLP-Signature</span>
                                <span class="text-muted">HMAC-SHA256</span>
                            </li>
                        </ul>
                    </div>

                    <div class="alert alert-primary d-flex p-2 mb-0 mt-3" role="alert">
                        <i class="ti ti-book me-2 mt-1"></i>
                        <div class="small">Lihat <a href="{{ route('documentation.index') }}" class="fw-bold">Dokumentasi</a> lengkap untuk cara generate signature.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Connected Agencies Modal -->
    <div class="modal fade" id="modalConnectedAgencies" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        <i class="ti ti-users me-2"></i>Perangkat Daerah Pengguna Layanan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Nama Perangkat Daerah</th>
                                    <th>Status Koneksi</th>
                                    <th>Terakhir Diakses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($connectedAgencies as $agency)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-medium text-heading">{{ $agency->name }}</div>
                                    </td>
                                    <td>
                                        @if(isset($agencyStatuses[$agency->id]))
                                            @php $status = $agencyStatuses[$agency->id]; @endphp
                                            @if($status['status'] == 'connected')
                                                <span class="badge bg-success">Connected</span>
                                            @elseif($status['status'] == 'disconnected')
                                                <span class="badge bg-danger">Disconnected ({{ $status['code'] }})</span>
                                            @else
                                                <span class="badge bg-label-secondary">Belum ada akses</span>
                                            @endif
                                        @else
                                            <span class="badge bg-label-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($agencyStatuses[$agency->id]) && $agencyStatuses[$agency->id]['last_check'])
                                            <div class="small text-muted">
                                                {{ \Carbon\Carbon::parse($agencyStatuses[$agency->id]['last_check'])->diffForHumans() }}
                                            </div>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ \Carbon\Carbon::parse($agencyStatuses[$agency->id]['last_check'])->format('d M Y H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ti ti-users text-muted mb-2 fs-1 opacity-50"></i>
                                            <p class="text-muted mb-0">Belum ada perangkat daerah yang terhubung.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-connected-agencies-modal', () => {
                $('#modalConnectedAgencies').modal('show');
            });
        });
    </script>


    <!-- Action Bar & Search -->
    <div class="card mb-4" id="endpoint-list">
        <div class="card-header d-flex flex-wrap justify-content-between gap-3">
            <div class="card-title mb-0 me-1">
                <h5 class="mb-1">Daftar Endpoint API</h5>
                <p class="text-muted mb-0">Daftar endpoint yang tersedia dalam katalog layanan ini.</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-3 ms-auto">
                <div class="input-group input-group-merge" style="max-width: 200px;">
                     <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                     <input type="text" class="form-control" placeholder="Cari endpoint..." aria-label="Cari endpoint..." aria-describedby="basic-addon-search31" wire:model.live.debounce.300ms="search">
                </div>
                <select wire:model.live="perPage" class="form-select" style="width: 80px;">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                @if(auth()->user()->can('manage_catalogs'))
                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modalEndpoint" wire:click="resetForm">
                    <i class="ti ti-plus me-1"></i> Tambah Endpoint
                </button>
                @endif
            </div>
        </div>
        <div class="card-body">
    <!-- Endpoints List -->
    @if($hasAccessOrAdmin)
        <div id="endpoints-list-container">
            @forelse($endpoints as $endpoint)
                <!-- Individual Endpoint Card (Replicated from _endpoint_item.blade.php) -->
                <div class="col-12 animate__animated animate__fadeIn mb-3"
                    style="animation-delay: {{ $loop->iteration * 0.1 }}s"
                    wire:key="endpoint-{{ $endpoint->id }}">
                    <div class="card endpoint-card mb-0 shadow-sm border-0" style="overflow: visible;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <!-- Left: Method & Info -->
                                <div class="d-flex align-items-start flex-grow-1">
                                    <div class="me-3">
                                        <span class="method-badge method-{{ $endpoint->method }}">{{ $endpoint->method }}</span>
                                        @if($endpoint->auth_mode === 'none')
                                            <span class="badge bg-label-success ms-1" data-bs-toggle="tooltip" title="Public / No Auth">
                                                <i class="ti ti-world"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger ms-1" data-bs-toggle="tooltip" title="Secured / Key Required">
                                                <i class="ti ti-lock"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column" style="min-width: 0;">
                                        <h6 class="mb-1 text-heading fw-bold text-truncate" title="{{ $endpoint->name }}">
                                            {{ $endpoint->name }}
                                            @if(!$endpoint->is_public)
                                                <span class="badge bg-label-secondary ms-2 text-xs">Admin Only</span>
                                            @endif
                                        </h6>
                                        <div class="d-flex align-items-center">
                                            <small class="text-muted font-monospace me-2 text-truncate">
                                                <i class="ti ti-link me-1 text-primary"></i>
                                                @if($endpoint->path && $catalogData)
                                                    <span class="fw-bold text-primary">{{ url('/') }}/api/{{ $catalogData['slug'] }}{{ Str::start($endpoint->path, '/') }}</span>
                                                    @if(auth()->user()->can('manage_catalogs'))
                                                        <div class="text-xs text-muted">Target: {{ $endpoint->url }}</div>
                                                    @endif
                                                @else
                                                    <span class="d-none d-sm-inline text-muted">{{ $endpoint->url }}</span>
                                                    <!-- Legacy Logic for setting gateway path if missing -->
                                                    @if(auth()->user()->can('manage_catalogs'))
                                                        <span class="badge bg-label-danger ms-2 cursor-pointer"
                                                            wire:click="editEndpoint({{ $endpoint->id }})" data-bs-toggle="modal" data-bs-target="#modalEndpoint">
                                                            <i class="ti ti-alert-circle me-1"></i> Set Gateway Path
                                                        </span>
                                                    @endif
                                                @endif
                                            </small>
                                        </div>
                                        @if($endpoint->description)
                                            <small class="text-muted text-truncate mt-1"
                                                style="max-width: 500px;">{{ $endpoint->description }}</small>
                                        @endif
                                        @if($endpoint->request_body)
                                            <div class="mt-2">
                                                <button class="btn btn-xs btn-outline-secondary" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#body-{{ $endpoint->id }}">
                                                    <i class="ti ti-code me-1"></i> Lihat Request Body
                                                </button>
                                                <div class="collapse mt-2" id="body-{{ $endpoint->id }}">
                                                    <div class="card card-body bg-light p-2 small font-monospace">
                                                        {{ $endpoint->request_body }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Right: Actions -->
                                <div class="ms-3">
                                    <!-- 3-dot dropdown menu for all actions -->
                                    <div class="dropdown">
                                        <button class="btn p-0 dropdown-toggle hide-arrow" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('service-catalogs.endpoint.detail', ['catalog' => $catalog->slug, 'id' => $endpoint->slug ?? $endpoint->id]) }}">
                                                    <i class="ti ti-eye me-2"></i> Detail
                                                </a>
                                            </li>
                                            @can('manage_catalogs')
                                                <li>
                                                    <button class="dropdown-item" wire:click="editEndpoint({{ $endpoint->id }})"
                                                        data-bs-toggle="modal" data-bs-target="#modalEndpoint">
                                                        <i class="ti ti-pencil me-2"></i> Edit
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item text-danger" wire:click="confirmDeleteEndpoint({{ $endpoint->id }})">
                                                        <i class="ti ti-trash me-2"></i> Hapus
                                                    </button>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <div class="bg-label-primary p-3 rounded-circle mb-3">
                            <i class="ti ti-database-off ti-xl"></i>
                        </div>
                        <h4 class="text-muted mb-1">Belum ada Endpoint</h4>
                        <p class="text-muted">Tambahkan endpoint baru untuk memulai.</p>
                    </div>
                </div>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {{ $endpoints->links() }}
            </div>
        </div>
    @else
        <!-- Restricted Access State -->
        <div class="text-center py-5">
             <!-- (Preserved from previous implementation, assuming standard restricted view is okay) -->
             <div class="d-flex flex-column align-items-center justify-content-center">
                 <div class="bg-label-warning p-4 rounded-circle mb-3 animate__animated animate__pulse animate__infinite">
                     <i class="ti ti-lock-access ti-xl text-warning"></i>
                 </div>
                 <h4 class="text-heading mb-2 fw-bold">Akses Terbatas</h4>
                 <p class="text-muted mb-4" style="max-width: 500px;">
                     Anda tidak memiliki izin untuk melihat endpoint layanan ini.
                 </p>
             </div>
        </div>
    @endif

    <!-- Endpoint Modal (Managed by Livewire) -->
    <div class="modal fade" id="modalEndpoint" tabindex="-1" aria-labelledby="modalEndpointLabel" wire:ignore.self>
      <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
          <div class="modal-body">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="resetForm"></button>
            <div class="text-center mb-4">
              <h3 class="mb-2">{{ $isEditMode ? 'Edit Endpoint' : 'Tambah Endpoint' }}</h3>
              <p class="text-muted">{{ $isEditMode ? 'Perbarui informasi endpoint.' : 'Tambahkan URL API ke dalam katalog ini.' }}</p>
            </div>
            <form wire:submit.prevent="saveEndpoint" class="row g-3">
              <div class="col-12 col-md-4">
                <label class="form-label">Method</label>
                <select wire:model="method" class="form-select">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
                </select>
                @error('method') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
              <div class="col-12 col-md-8">
                <label class="form-label">Nama Endpoint</label>
                <input type="text" wire:model="name" class="form-control" placeholder="Cek NIK" />
                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Gateway Path <span class="text-danger">*</span></label>
                <div class="d-flex flex-column border rounded-3 overflow-hidden focus-within-shadow transition-all">
                    <!-- Base URL Context -->
                    <div class="bg-light px-3 py-2 d-flex align-items-center border-bottom">
                         <i class="ti ti-server ti-xs me-2 text-muted"></i>
                         <div class="text-muted small font-monospace text-truncate" title="{{ url('/') }}/api/{{ $catalogData['slug'] }}">
                             {{ url('/') }}/api/{{ $catalogData['slug'] }}
                         </div>
                    </div>
                    <!-- Path Input -->
                    <div class="input-group input-group-merge border-0 rounded-0">
                        <input type="text" wire:model="path" class="form-control border-0 shadow-none ps-3" placeholder="/news" style="padding-left: 0;">
                    </div>
                </div>
                <div class="form-text mt-1">Wajib diisi agar request tercatat di log SPLP.</div>
                @error('path') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
              <div class="col-12">
                @if(!empty($catalogData['base_url']))
                    <label class="form-label">Target Path <span class="text-danger">*</span></label>
                    <div class="d-flex flex-column border rounded-3 overflow-hidden focus-within-shadow transition-all">
                        <div class="bg-light px-3 py-2 d-flex align-items-center border-bottom">
                             <i class="ti ti-world-www ti-xs me-2 text-muted"></i>
                             <div class="text-muted small font-monospace text-truncate" title="{{ $catalogData['base_url'] }}">
                                 {{ rtrim($catalogData['base_url'], '/') }}
                             </div>
                        </div>
                        <div class="input-group input-group-merge border-0 rounded-0">
                            <input type="text" wire:model="url" class="form-control border-0 shadow-none ps-3" placeholder="/v1/resource" style="padding-left: 0;">
                        </div>
                    </div>
                    <div class="form-text mt-1">Path endpoint relative dari Base URL.</div>
                @else
                    <label class="form-label">URL Lengkap (Target) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-link"></i></span>
                        <input type="text" wire:model="url" class="form-control" placeholder="https://api.example.com/v1/resource" />
                    </div>
                    <div class="form-text">URL asli dari penyedia layanan (Full URL).</div>
                @endif
                @error('url') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
              <div class="col-12">
                <label class="form-label">Format Request Body (JSON/Text)</label>
                <textarea wire:model="request_body" class="form-control font-monospace" rows="4" placeholder='{"key": "value"}'></textarea>
                @error('request_body') <span class="text-danger small">{{ $message }}</span> @enderror
              </div>
              <div class="col-12">
                  <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="is_public" wire:model="is_public">
                      <label class="form-check-label" for="is_public">Public Access</label>
                  </div>
                  </div>
              </div>

               <!-- Auth Mode (Modern Radio Cards) -->
               <div class="col-12">
                   <label class="form-label d-block mb-2">Mode Keamanan (Gateway Auth)</label>
                   <div class="row g-3">
                       <!-- Option 1: Required (Locked) -->
                       <div class="col-md-6">
                           <input type="radio" class="btn-check" name="auth_mode" id="auth_mode_required" value="required" wire:model.live="auth_mode" autocomplete="off">
                           <label class="btn btn-outline-danger w-100 p-3 text-start d-flex align-items-center opacity-75-hover" for="auth_mode_required"
                               style="border-width: 2px;">
                               <i class="ti ti-lock me-3 fs-3"></i>
                               <div>
                                   <div class="fw-bold mb-1">Terkunci (Locked)</div>
                                   <div class="small opacity-75" style="font-size: 0.75rem; line-height: 1.2;">Wajib API Key & Signature.</div>
                               </div>
                           </label>
                       </div>
                       <!-- Option 2: None (Public) -->
                       <div class="col-md-6">
                           <input type="radio" class="btn-check" name="auth_mode" id="auth_mode_none" value="none" wire:model.live="auth_mode" autocomplete="off">
                           <label class="btn btn-outline-success w-100 p-3 text-start d-flex align-items-center opacity-75-hover" for="auth_mode_none"
                               style="border-width: 2px;">
                               <i class="ti ti-world me-3 fs-3"></i>
                               <div>
                                   <div class="fw-bold mb-1">Terbuka (Public)</div>
                                   <div class="small opacity-75" style="font-size: 0.75rem; line-height: 1.2;">Bypass Otentikasi.</div>
                               </div>
                           </label>
                       </div>
                   </div>
                   <!-- Visual Feedback Area -->
                   <div class="mt-2 p-2 rounded bg-label-{{ $auth_mode === 'required' ? 'danger' : 'success' }} text-{{ $auth_mode === 'required' ? 'danger' : 'success' }} small animate__animated animate__fadeIn">
                       <i class="ti ti-info-circle me-1"></i>
                       @if($auth_mode === 'required')
                           Endpoint ini <strong>hanya dapat diakses</strong> dengan Header X-SPLP-Client-ID yang valid.
                       @else
                           Endpoint ini <strong>dapat diakses publik</strong> tanpa otentikasi apapun.
                       @endif
                   </div>
                   @error('auth_mode') <span class="text-danger small">{{ $message }}</span> @enderror
               </div>
              <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea wire:model="description" class="form-control" rows="2"></textarea>
              </div>
              <div class="col-12 text-center mt-4">
                <button type="submit" class="btn btn-primary me-sm-3 me-1">Simpan</button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" wire:click="resetForm">Batal</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Catalog Modal -->
    <div class="modal fade" id="modalEditCatalog" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-simple modal-edit-user">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Edit Informasi Katalog</h3>
                        <p class="text-muted">Perbarui detail layanan API ini.</p>
                    </div>
                    <form wire:submit.prevent="updateCatalog" class="row g-3">
                        <!-- Agency -->
                        <div class="col-12">
                            <label class="form-label">Perangkat Daerah Penyedia</label>
                            <select wire:model="edit_agency_id" class="form-select">
                                <option value="">Pilih Perangkat Daerah...</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                @endforeach
                            </select>
                            @error('edit_agency_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- Category -->
                        <div class="col-12">
                            <label class="form-label">Kategori Layanan</label>
                            <select wire:model="edit_category_id" class="form-select">
                                <option value="">Pilih Kategori (Opsional)</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('edit_category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- Name -->
                        <div class="col-12">
                            <label class="form-label">Nama Katalog</label>
                            <input type="text" wire:model="edit_name" class="form-control" placeholder="Nama Layanan API" />
                            @error('edit_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- Base URL & Rate Limit -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">Base URL API</label>
                            <input type="url" wire:model="edit_base_url" class="form-control" placeholder="https://api.example.com" />
                            @error('edit_base_url') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Rate Limit (Req/Min)</label>
                            <input type="number" wire:model="edit_rate_limit" class="form-control" min="1" />
                            @error('edit_rate_limit') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- Target Token -->
                        <div class="col-12">
                            <label class="form-label">Target Token (Optional)</label>
                            <input type="text" wire:model="edit_target_token" class="form-control" placeholder="Bearer Token Asli (Backend)" />
                            <div class="form-text">Token ini akan otomatis disisipkan oleh Gateway saat meneruskan reques.</div>
                            @error('edit_target_token') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- Mapping Config -->
                        <div class="col-12 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_requires_mapping" wire:model.live="edit_requires_mapping">
                                <label class="form-check-label user-select-none" for="edit_requires_mapping">
                                    Layanan Membutuhkan Mapping Identitas?
                                </label>
                            </div>
                        </div>

                        @if($edit_requires_mapping)
                            <div class="col-12 col-md-6 animate__animated animate__fadeIn">
                                <label class="form-label">URL API Referensi</label>
                                <input type="url" wire:model="edit_mapping_api_url" class="form-control" placeholder="https://..." />
                                @error('edit_mapping_api_url') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6 animate__animated animate__fadeIn">
                                <label class="form-label">Field Parameter</label>
                                <input type="text" wire:model="edit_mapping_field" class="form-control" placeholder="skpd_id" />
                                @error('edit_mapping_field') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea wire:model="edit_description" class="form-control" rows="3"></textarea>
                            @error('edit_description') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">Status</label>
                            <select wire:model.live="edit_status" class="form-select">
                                <option value="active">Aktif</option>
                                <option value="pending">Menunggu Review</option>
                                <option value="rejected">Ditolak</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                            @error('edit_status') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- UAT Document -->
                        <div class="col-12 animate__animated animate__fadeIn" x-show="$wire.edit_status === 'active' || $wire.edit_status === 'pending'">
                             <label class="form-label">
                                 Berita Acara / Dokumen UAT
                                 <span class="text-danger" x-show="$wire.edit_status === 'active'">*</span>
                             </label>
                             <input type="file" wire:model="uat_document" class="form-control" accept=".pdf,.doc,.docx" />
                             <div wire:loading wire:target="uat_document" class="text-primary mt-1 small">Uploading UAT Doc...</div>
                             @error('uat_document') <span class="text-danger small">{{ $message }}</span> @enderror

                             @if($existing_uat_document)
                                <div class="mt-2">
                                    <small class="text-muted d-block">Dokumen UAT saat ini:</small>
                                    <a href="{{ Storage::url($existing_uat_document) }}" target="_blank" class="btn btn-sm btn-label-primary mt-1">
                                        <i class="ti ti-file-text me-1"></i> Lihat Dokumen
                                    </a>
                                </div>
                             @endif
                             <div class="form-text">Wajib diupload jika mengaktifkan layanan (Bukti UAT).</div>
                        </div>

                        <!-- Rejection Reason (Conditional) -->
                        @if($edit_status === 'rejected')
                            <div class="col-12 animate__animated animate__fadeIn">
                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea wire:model="edit_rejection_reason" class="form-control" rows="2" placeholder="Jelaskan alasan penolakan..."></textarea>
                                @error('edit_rejection_reason') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <!-- Cover Image -->
                        <div class="col-12 col-md-6">
                            <label class="form-label">Cover Image</label>
                            <input type="file" wire:model="new_cover_image" class="form-control" accept="image/*" />
                            <div wire:loading wire:target="new_cover_image" class="text-primary mt-1 small">Uploading...</div>
                            @error('new_cover_image') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Simpan Perubahan</button>
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Close endpoint modal after save
    $wire.on('endpoint-saved', () => {
        const modalEl = document.getElementById('modalEndpoint');
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
    });

    // Open catalog edit modal
    $wire.on('open-catalog-modal', () => {
         $('#modalEditCatalog').modal('show');
    });

    // Close catalog edit modal
    $wire.on('close-catalog-modal', () => {
         $('#modalEditCatalog').modal('hide');
    });
</script>
@endscript
