<div wire:poll.5s class="row mb-4 g-3">
    <!-- 1. Card Pertama: Admin (Perangkat Daerah) vs User (Layanan Disetujui) -->
    <div class="col-md-3 col-sm-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
        <div class="card h-100 bg-label-info">
            <a href="{{ $isAdmin ? route('agency.index', ['filter' => 'connected']) : '#' }}" class="text-decoration-none h-100 d-block">
                <div class="card-body d-flex align-items-center position-relative">
                    <div class="avatar me-3">
                        <span class="avatar-initial rounded bg-info text-white">
                            <i class="ti {{ $isAdmin ? 'ti-building' : 'ti-file-check' }} fs-3"></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-1">
                            <h4 class="mb-0 fw-bold text-info me-2">{{ $card1_value }}</h4>
                            <span class="badge bg-info text-white shadow-sm position-absolute top-0 end-0 m-3" style="font-size: 0.7rem;">Active</span>
                        </div>
                        <small class="text-muted text-nowrap">{{ $card1_label }}</small>
                        @if(!empty($card1_sub))
                            <div class="mt-1">
                                <small class="text-success fw-bold" style="font-size: 0.75rem;">
                                    <i class="ti ti-link me-1"></i>{{ $card1_sub }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- 2. Card Kedua: Admin (Total Katalog) vs User (Layanan Terhubung) -->
    <div class="col-md-3 col-sm-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
        <div class="card h-100 bg-label-primary">
            <div class="card-body d-flex align-items-center position-relative">
                <div class="avatar me-3">
                    <span class="avatar-initial rounded bg-primary text-white">
                        <i class="ti {{ $isAdmin ? 'ti-server' : 'ti-link' }} fs-3"></i>
                    </span>
                </div>
                <div class="d-flex flex-column">
                     <div class="d-flex align-items-center mb-1">
                        <h4 class="mb-0 fw-bold text-primary me-2">{{ $card2_value }}</h4>
                        <span class="badge bg-primary text-white shadow-sm position-absolute top-0 end-0 m-3" style="font-size: 0.7rem;">Active</span>
                     </div>
                     <small class="text-muted">{{ $card2_label }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Total Request (Transactions) - Shared -->
    <div class="col-md-3 col-sm-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
        <div class="card h-100 bg-label-warning">
            <div class="card-body d-flex align-items-center position-relative">
                <div class="avatar me-3">
                    <span class="avatar-initial rounded bg-warning text-white">
                        <i class="ti ti-chart-arrows fs-3"></i>
                    </span>
                </div>
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center mb-1">
                        <h4 class="mb-0 fw-bold text-warning me-2">{{ $totalTransactions }}</h4>
                        <span class="badge bg-warning text-white shadow-sm position-absolute top-0 end-0 m-3" style="font-size: 0.7rem;">{{ $isAdmin ? 'Global' : 'Personal' }}</span>
                    </div>
                    <small class="text-muted">Total Request</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Success Rate - Shared -->
    <div class="col-md-3 col-sm-6 animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
        <div class="card h-100 bg-label-success">
            <div class="card-body d-flex align-items-center position-relative">
                <div class="avatar me-3">
                    <span class="avatar-initial rounded bg-success text-white">
                        <i class="ti ti-activity-heartbeat fs-3"></i>
                    </span>
                </div>
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center mb-1">
                        <h4 class="mb-0 fw-bold text-success me-2">{{ $successRate }}%</h4>
                        @php
                            $errorRateFormatted = number_format($errorRate, 1);
                        @endphp
                        <span class="badge bg-danger text-white shadow-sm position-absolute top-0 end-0 m-3" style="font-size: 0.7rem;">{{ $errorRateFormatted }}% Error</span>
                    </div>
                    <small class="text-muted">Success Rate</small>
                </div>
            </div>
        </div>
    </div>
</div>
