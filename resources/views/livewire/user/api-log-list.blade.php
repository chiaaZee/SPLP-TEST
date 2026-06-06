<div>
    <!-- Search Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari layanan API..." aria-label="Cari layanan API..." aria-describedby="basic-addon-search31" wire:model.live="search" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Grid -->
    <div class="row g-4">
        @forelse($catalogs as $catalog)
        <div class="col-lg-4 col-md-6 stagger-enter" style="animation-delay: {{ $loop->index * 100 }}ms">
            <div class="card h-100 glass-card">
                <div class="card-body">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-api"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0 text-truncate" style="max-width: 180px;" title="{{ $catalog->name }}">{{ $catalog->name }}</h5>

                            </div>
                        </div>
                        <span class="badge bg-label-{{ $catalog->health_color ?? 'success' }} d-flex align-items-center">
                            <i class="ti ti-activity me-1"></i> {{ ucfirst($catalog->health_status ?? 'Healthy') }}
                        </span>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-6" x-data="rollingCounter({{ $catalog->hits_24h }})">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge badge-center rounded-pill bg-label-info me-2"><i class="ti ti-chart-bar"></i></span>
                                <span class="fw-bold fs-5" x-text="current.toLocaleString()">0</span>
                            </div>
                            <small class="text-muted d-block">Hits (24h)</small>
                        </div>
                        <div class="col-6" x-data="rollingCounter({{ $catalog->error_count }})">
                            <div class="d-flex align-items-center mb-1">
                                    <span class="badge badge-center rounded-pill bg-label-danger me-2"><i class="ti ti-alert-triangle"></i></span>
                                <span class="fw-bold fs-5" x-text="current.toLocaleString()">0</span>
                            </div>
                            <small class="text-muted d-block">Errors (24h)</small>
                        </div>
                        <div class="col-12" x-data="rollingCounter({{ $catalog->avg_latency }})">
                                <div class="d-flex align-items-center mb-1">
                                <span class="badge badge-center rounded-pill bg-label-warning me-2"><i class="ti ti-clock"></i></span>
                                <span class="fw-bold fs-5"><span x-text="current">0</span> ms</span>
                                    <small class="text-muted ms-2">Avg. Latency</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action -->
                    <a href="{{ route('api-logs.show', $catalog->slug) }}" class="btn btn-primary w-100 shadow-sm waves-effect waves-light">
                        <i class="ti ti-device-desktop-analytics me-1"></i> Monitor Detail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="ti ti-server-off fs-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted">Tidak ada Layanan API ditemukan</h4>
                    <p class="text-muted mb-0">Coba kata kunci pencarian lain.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
