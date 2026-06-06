@section('page-style')
@endsection

<div>
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

    <!-- Service Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex flex-column">
                    <h4 class="mb-1">{{ $service->name }}</h4>
                    <p class="mb-0 text-muted">
                        <i class='ti ti-category me-1'></i> {{ $service->category->name ?? 'Uncategorized' }}
                    </p>
                    <p class="mt-2">{{ $service->description ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                        <span class="badge {{ $service->is_public ? 'bg-label-success' : 'bg-label-secondary' }}">
                            <i class="ti ti-{{ $service->is_public ? 'world' : 'lock' }} me-1"></i>
                            {{ $service->is_public ? 'Published' : 'Draft' }}
                        </span>
                        <span class="badge bg-label-primary cursor-pointer" data-bs-toggle="tooltip" title="Gateway Base URL">
                            <i class="ti ti-link me-1"></i> {{ $service->base_url }}
                        </span>
                         <span class="badge bg-label-danger"><i class="ti ti-gauge me-1"></i> Limit: {{ $service->rate_limit ?? 60 }} req/min</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                     <a href="{{ route('user.my-services.index') }}" class="btn btn-label-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Kembali
                    </a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#configModal">
                        <i class='ti ti-settings me-1'></i> Konfigurasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row (Clean White) -->
    <div class="row mb-4">
        <div class="col-lg-4 col-sm-6 mb-4 mb-lg-0">
            <div class="card h-100 border border-light-subtle shadow-sm rounded-4 hover-effect-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-primary-subtle text-primary"><i class="ti ti-users fs-4"></i></span>
                        </div>
                        <div>
                            <h5 class="card-title text-dark mb-0 fw-bold">{{ $active_users_count }}</h5>
                            <small class="text-muted">Active Users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 mb-4 mb-lg-0">
             <div class="card h-100 border border-light-subtle shadow-sm rounded-4 hover-effect-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-success-subtle text-success"><i class="ti ti-activity fs-4"></i></span>
                        </div>
                        <div>
                            <h5 class="card-title text-dark mb-0 fw-bold">{{ $total_requests }}</h5>
                            <small class="text-muted">Total Requests</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 mb-0">
             <div class="card h-100 border border-light-subtle shadow-sm rounded-4 hover-effect-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-warning-subtle text-warning"><i class="ti ti-server fs-4"></i></span>
                        </div>
                        <div>
                            <h5 class="card-title text-dark mb-0 fw-bold">{{ $endpoints->count() }}</h5>
                            <small class="text-muted">Total Endpoints</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar pills with more gap -->
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-sm-row mb-4 gap-3" role="tablist">
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'info' ? 'active' : '' }} rounded-3 px-4 shadow-sm py-2" wire:click="$set('activeTab', 'info')"><i class='ti ti-info-circle me-1'></i> Informasi</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'endpoints' ? 'active' : '' }} rounded-3 px-4 shadow-sm py-2" wire:click="$set('activeTab', 'endpoints')"><i class='ti ti-list me-1'></i> Daftar Endpoint</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'users' ? 'active' : '' }} rounded-3 px-4 shadow-sm py-2" wire:click="$set('activeTab', 'users')"><i class='ti ti-users me-1'></i> Pengguna</button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content p-0 bg-transparent shadow-none border-0">

        <!-- Tab 1: Info -->
    <div class="tab-content p-0 bg-transparent shadow-none border-0">

        <!-- Tab 1: Info -->
        <div class="tab-pane fade {{ $activeTab === 'info' ? 'show active' : '' }}" id="navs-pills-info" role="tabpanel">
            <div class="row g-4">
                <!-- Description Card -->
                <div class="col-lg-8">
                     <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header border-bottom py-3">
                            <h5 class="card-title max-w mb-0 text-dark fw-bold">Deskripsi Layanan</h5>
                        </div>
                        <div class="card-body pt-4">
                            <p class="mb-0 text-muted" style="line-height: 1.8;">
                                {{ $service->description ?: 'Tidak ada deskripsi yang tersedia.' }}
                            </p>
                            <div class="mt-4 p-3 rounded-3 bg-white border border-primary border-opacity-25 d-flex align-items-start gap-3">
                                <i class="ti ti-shield-lock text-primary fs-4 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold text-primary mb-1">Otentikasi</h6>
                                    <p class="mb-0 small text-muted">Layanan ini mewajibkan validasi <strong>API Key</strong>. Pastikan setiap request menyertakan header Authorization.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Details - Custom Colored Card (Refined to match Admin) -->
                <div class="col-lg-4">
                    <div class="card h-100 border-primary shadow-sm" style="border: 2px solid var(--bs-primary) !important;">
                        <div class="card-header bg-label-primary d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 text-primary fw-bold"><i class="ti ti-code me-1"></i> Detail Teknis</h6>
                        </div>
                        <div class="card-body pt-3">
                            <!-- Base URL -->
                            <div class="mb-3">
                                <label class="small text-uppercase fw-bold text-muted mb-1">Base URL</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" value="{{ $service->base_url }}" readonly id="quickBaseUrl">
                                    <button class="btn btn-primary" onclick="navigator.clipboard.writeText('{{ $service->base_url }}')"><i class="ti ti-copy"></i></button>
                                </div>
                            </div>



                            <div class="mb-3">
                                <label class="small text-uppercase fw-bold text-muted mb-1">Rate Limit</label>
                                <h5 class="mb-0 text-dark">{{ $service->rate_limit }} <small class="text-muted fs-6">req/min</small></h5>
                            </div>

                            <div class="mb-0">
                                <label class="small text-uppercase fw-bold text-muted mb-1">Status Publikasi</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="publishSwitch2" wire:click="togglePublish" {{ $service->is_public ? 'checked' : '' }} style="cursor: pointer;">
                                    <label class="form-check-label text-dark" for="publishSwitch2">{{ $service->is_public ? 'Publik' : 'Private' }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Endpoints Tab -->
        <div class="tab-pane fade {{ $activeTab === 'endpoints' ? 'show active' : '' }}" id="navs-pills-endpoints" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center bg-white rounded-top-4">
                     <h5 class="mb-0 fw-bold text-dark">Daftar Endpoint</h5>
                     <button class="btn btn-primary rounded-3 shadow-sm" wire:click="openEndpointModal" wire:loading.attr="disabled" data-bs-toggle="modal" data-bs-target="#endpointModal">
                        <i class="ti ti-plus me-1"></i> Tambah Endpoint
                    </button>
                </div>
                 <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="text-uppercase small fw-bold text-muted ps-4">Method</th>
                                <th class="text-uppercase small fw-bold text-muted">Path</th>
                                <th class="text-uppercase small fw-bold text-muted">Deskripsi</th>
                                <th class="text-uppercase small fw-bold text-muted text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($endpoints as $ep)
                            <tr>
                                <td class="ps-4">
                                    @php
                                        $badgeClass = match($ep->method) {
                                            'GET' => 'bg-success-subtle text-success',
                                            'POST' => 'bg-primary-subtle text-primary',
                                            'PUT' => 'bg-warning-subtle text-warning',
                                            'DELETE' => 'bg-danger-subtle text-danger',
                                            default => 'bg-secondary-subtle text-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-3">{{ $ep->method }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $ep->name }}</span>
                                    <br>
                                    <code class="text-muted small">{{ $ep->path }}</code>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $ep->description ?: '-' }}</span>
                                    @if($ep->auth_mode === 'required')
                                        <i class="ti ti-lock text-muted ms-1 fs-6" title="Locked"></i>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-icon btn-text-secondary rounded-circle" wire:click="editEndpoint({{ $ep->id }})">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-text-danger rounded-circle" onclick="confirm('Hapus endpoint ini?') || event.stopImmediatePropagation()" wire:click="deleteEndpoint({{ $ep->id }})">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada endpoint yang ditambahkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Users Tab -->
        <div class="tab-pane fade {{ $activeTab === 'users' ? 'show active' : '' }}" id="navs-pills-users" role="tabpanel">

            @if(count($pendingRequests) > 0)
            <!-- Pending Requests Card -->
            <div class="card border-warning shadow-sm rounded-4 mb-4" style="border: 1px solid #ff9f43 !important;">
                <div class="card-header bg-label-warning border-bottom py-3 d-flex justify-content-between align-items-center rounded-top-4">
                     <h5 class="mb-0 fw-bold text-warning"><i class="ti ti-alert-circle me-2"></i>Permintaan Akses Masuk</h5>
                     <span class="badge bg-warning rounded-pill px-3">{{ count($pendingRequests) }} Menunggu Persetujuan</span>
                </div>
                 <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="border-bottom">
                            <tr>
                                <th class="text-uppercase small fw-bold text-muted ps-4">User / Instansi</th>
                                <th class="text-uppercase small fw-bold text-muted">Alasan</th>
                                <th class="text-uppercase small fw-bold text-muted">Waktu Request</th>
                                <th class="text-uppercase small fw-bold text-muted text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($pendingRequests as $req)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ substr($req->user->name, 0, 1) }}</span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium text-dark">{{ $req->user->name }}</span>
                                            <small class="text-muted">{{ $req->user->agency->name ?? 'Instansi Umum' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $req->reason }}">
                                        {{ $req->reason ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $req->created_at->diffForHumans() }}</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-success rounded-pill me-1" wire:click="approveRequest({{ $req->id }})" wire:loading.attr="disabled">
                                        <i class="ti ti-check me-1"></i> Terima
                                    </button>
                                    <button class="btn btn-sm btn-label-danger rounded-pill" onclick="confirm('Tolak permintaan ini?') || event.stopImmediatePropagation()" wire:click="rejectRequest({{ $req->id }})" wire:loading.attr="disabled">
                                        <i class="ti ti-x me-1"></i> Tolak
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

             <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center bg-white rounded-top-4">
                     <h5 class="mb-0 fw-bold text-dark">Data Pengguna</h5>
                </div>
                 <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="text-uppercase small fw-bold text-muted ps-4">User</th>
                                <th class="text-uppercase small fw-bold text-muted">Email</th>
                                <th class="text-uppercase small fw-bold text-muted">Waktu Akses</th>
                                <th class="text-uppercase small fw-bold text-muted">Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($accessRequests as $request)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-primary-subtle text-primary fw-bold">{{ substr($request->user->name, 0, 1) }}</span>
                                        </div>
                                        <span class="fw-medium text-dark">{{ $request->user->name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $request->user->email }}</td>
                                <td class="text-muted">{{ $request->updated_at->format('d M Y, H:i') }}</td>
                                <td><span class="badge bg-success-subtle text-success rounded-pill">Aktif</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada user.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

     <!-- Endpoint Modal (Clean Design) -->
    <div class="modal fade" id="endpointModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold">{{ $editingEndpointId ? 'Edit Endpoint' : 'Tambah Endpoint Baru' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="storeEndpoint">
                        <div class="row g-4">
                             <div class="col-12 col-md-4">
                                <label class="form-label fw-medium text-dark">Method <span class="text-danger">*</span></label>
                                <select wire:model="method" class="form-select form-select-lg">
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                    <option value="PUT">PUT</option>
                                    <option value="DELETE">DELETE</option>
                                </select>
                            </div>
                             <div class="col-12 col-md-8">
                                <label class="form-label fw-medium text-dark">Nama Endpoint <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control form-control-lg" placeholder="Contoh: Get User List">
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium text-dark">Gateway Path <span class="text-danger">*</span></label>
                                <div class="d-flex flex-column border rounded-3 overflow-hidden">
                                     <div class="bg-white px-3 py-2 d-flex align-items-center border-bottom">
                                         <i class="ti ti-server ti-xs me-2 text-muted"></i>
                                         <div class="text-muted small font-monospace text-truncate">
                                             {{ url('/') }}/api/{{ $service->slug }}
                                         </div>
                                    </div>
                                    <input type="text" wire:model="path" class="form-control border-0 shadow-none ps-3 py-2" placeholder="/users/list">
                                </div>
                                <div class="form-text text-muted">Path yang akan diakses oleh pengguna via SPLP.</div>
                                @error('path') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12">
                                 <label class="form-label fw-medium text-dark">Target URL <span class="text-danger">*</span></label>
                                 @if($service->base_url)
                                     <div class="d-flex flex-column border rounded-3 overflow-hidden">
                                        <div class="bg-white px-3 py-2 d-flex align-items-center border-bottom">
                                             <i class="ti ti-world-www ti-xs me-2 text-muted"></i>
                                             <div class="text-muted small font-monospace text-truncate">
                                                 {{ rtrim($service->base_url, '/') }}
                                             </div>
                                        </div>
                                        <input type="text" wire:model="url" class="form-control border-0 shadow-none ps-3 py-2" placeholder="/v1/users">
                                    </div>
                                    <div class="form-text text-muted">Path relatif dari Base URL.</div>
                                 @else
                                     <input type="text" wire:model="url" class="form-control" placeholder="https://api.example.com/v1/users">
                                     <div class="form-text text-muted">Masukkan URL lengkap.</div>
                                 @endif
                                 @error('url') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                             <div class="col-12">
                                <label class="form-label fw-medium text-dark">Request Body Template (JSON)</label>
                                <textarea wire:model="request_body" class="form-control font-monospace" rows="3" placeholder='{"key": "value"}'></textarea>
                            </div>

                            <!-- Public Access Switch -->
                            <div class="col-12">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_public" wire:model="is_public" style="transform: scale(1.2); margin-right: 10px;">
                                    <label class="form-check-label fw-medium text-dark" for="is_public" style="padding-top: 2px;">Public Access</label>
                                </div>
                                <div class="form-text mt-0">
                                    <i class="ti ti-info-circle me-1 ti-xs"></i>
                                    Jika aktif, semua user dapat menggunakan endpoint ini <strong>tanpa perlu persetujuan</strong>.
                                </div>
                            </div>

                            <!-- Auth Mode -->
                            <div class="col-12">
                                <label class="form-label d-block mb-3 fw-medium text-dark">Mode Keamanan</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check" name="auth_mode" id="auth_mode_required" value="required" wire:model.live="auth_mode">
                                        <label class="btn btn-outline-danger w-100 p-3 text-start d-flex align-items-center h-100 rounded-3 border-2" for="auth_mode_required">
                                            <i class="ti ti-lock me-3 fs-3"></i>
                                            <div>
                                                <div class="fw-bold mb-1">Terkunci (Locked)</div>
                                                <div class="small opacity-75">Wajib mencantumkan API Key yang valid.</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check" name="auth_mode" id="auth_mode_none" value="none" wire:model.live="auth_mode">
                                        <label class="btn btn-outline-success w-100 p-3 text-start d-flex align-items-center h-100 rounded-3 border-2" for="auth_mode_none">
                                            <i class="ti ti-world me-3 fs-3"></i>
                                            <div>
                                                <div class="fw-bold mb-1">Terbuka (Public)</div>
                                                <div class="small opacity-75">Siapapun dapat mengakses endpoint ini.</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium text-dark">Deskripsi</label>
                                <textarea wire:model="description" class="form-control" rows="2"></textarea>
                                @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                        </div>
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-label-secondary me-2 rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-3 px-4" wire:loading.attr="disabled" wire:target="storeEndpoint">
                                <span wire:loading.remove wire:target="storeEndpoint">
                                    {{ $editingEndpointId ? 'Simpan Perubahan' : 'Tambah Endpoint' }}
                                </span>
                                <span wire:loading wire:target="storeEndpoint">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <!-- Config Modal -->
    <div class="modal fade" id="configModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold">Konfigurasi Layanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="updateConfig">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Base URL <span class="text-danger">*</span></label>
                            <input type="url" wire:model="base_url" class="form-control bg-light" placeholder="https://api.example.com/v1" readonly>
                             <div class="form-text">Hubungi admin jika ingin mengubah Base URL.</div>
                            @error('base_url') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                         <div class="mb-3">
                            <label class="form-label fw-medium">Kategori Layanan <span class="text-danger">*</span></label>
                            <select wire:model="service_category_id" class="form-select">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('service_category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Rate Limit (Requests/Minute) <span class="text-danger">*</span></label>
                            <input type="number" wire:model="rate_limit" class="form-control" placeholder="60">
                             <div class="form-text">Batas maksimum request per menit per user.</div>
                            @error('rate_limit') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                         <div class="mb-3">
                            <label class="form-label fw-medium">Bearer Token (Target API)</label>
                            <div class="input-group input-group-merge">
                                <input type="text" wire:model="target_token" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            <div class="form-text">Token untuk mengakses API asli (disimpan terenkripsi).</div>
                             @error('target_token') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Mapping Field (JSON Path)</label>
                            <input type="text" wire:model="mapping_field" class="form-control" placeholder="data.results">
                            <div class="form-text">Path JSON untuk mengambil data utama (Opsional).</div>
                             @error('mapping_field') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="text-end mt-4">
                             <button type="button" class="btn btn-label-secondary me-2 rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">Simpan Konfigurasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        const endpointModal = new bootstrap.Modal(document.getElementById('endpointModal'));

        Livewire.on('show-endpoint-modal', () => {
            endpointModal.show();
        });

        Livewire.on('hide-endpoint-modal', () => {
            endpointModal.hide();
        });
    });
</script>
@endpush
