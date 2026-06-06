@section('title', 'Riwayat Aktivitas')

@section('page-style')
<style>
    .timeline .timeline-item .timeline-event {
        padding-bottom: 2rem;
    }
</style>
@endsection

<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-7 z-1">
                            <h3 class="text-white fw-bold display-6 mb-2">Riwayat Aktivitas</h3>
                            <p class="mb-0 text-white lead opacity-75">Jejak lengkap aktivitas akun Anda, mulai dari pengajuan akses hingga pengelolaan API.</p>
                        </div>
                        <div class="col-md-5 d-none d-md-block position-relative">
                             <i class="ti ti-activity-heartbeat position-absolute end-0 top-50 translate-middle-y text-white opacity-25" style="font-size: 10rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Timeline Aktivitas</h5>
                    <a href="{{ route('pages-profile-user') }}" class="btn btn-secondary btn-sm"><i class="ti ti-arrow-left me-1"></i> Kembali ke Profil</a>
                </div>
                <div class="card-body">
                    <ul class="timeline ms-1 mb-0">
                        @forelse($activities as $activity)
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-{{ $activity['color'] }}"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">{{ $activity['title'] }}</h6>
                                    <small class="text-muted">{{ $activity['time']->format('d M Y, H:i') }}</small>
                                </div>
                                <p class="mb-0 text-muted">{{ $activity['description'] }}</p>
                            </div>
                        </li>
                        @empty
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-secondary"></span>
                            <div class="timeline-event">
                                <p class="mb-0">Belum ada aktivitas tercatat.</p>
                            </div>
                        </li>
                        @endforelse
                    </ul>

                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
