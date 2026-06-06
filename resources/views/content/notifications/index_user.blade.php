@extends('layouts/layoutMaster')

@section('title', 'Notifikasi Saya')

@section('content')
@section('content')


<!-- Hero Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card text-white h-100 overflow-hidden"
            style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-md-7 z-1">
                        <h3 class="text-white fw-bold display-6 mb-2">Pusat Notifikasi</h3>
                        <p class="mb-0 text-white lead opacity-75">Pantau status permintaan akses dan pembaruan sistem terbaru Anda di sini.</p>
                    </div>
                </div>
            </div>
            <i class="ti ti-bell-ringing position-absolute text-white opacity-25 animate__animated animate__pulse animate__infinite"
                style="font-size: 15rem; right: -2rem; bottom: -4rem;"></i>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Riwayat Notifikasi</h5>
        @if($notifications->count() > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-mail-opened me-1"></i> Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>
    <ul class="list-group list-group-flush">
        @forelse($notifications as $notification)
            <li class="list-group-item list-group-item-action d-flex align-items-center {{ $notification->read_at ? '' : 'bg-lighter' }}">
                <div class="avatar me-3">
                    @if(isset($notification->data['status']) && $notification->data['status'] == 'approved')
                        <span class="avatar-initial rounded-circle bg-label-success"><i class="ti ti-check"></i></span>
                    @elseif(isset($notification->data['status']) && $notification->data['status'] == 'rejected')
                        <span class="avatar-initial rounded-circle bg-label-danger"><i class="ti ti-x"></i></span>
                    @else
                        <span class="avatar-initial rounded-circle bg-label-info"><i class="ti ti-bell"></i></span>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-1">{{ $notification->data['catalog_name'] ?? 'System Notification' }}</h6>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-1 text-muted small">{{ $notification->data['message'] ?? 'Check your account for updates.' }}</p>
                </div>
                <!-- Actions removed as per request -->
            </li>
        @empty
            <li class="list-group-item text-center py-5">
                <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}" width="150" class="mb-3 opacity-25">
                <p class="text-muted">Belum ada notifikasi.</p>
            </li>
        @endforelse
    </ul>
    <div class="card-footer d-flex justify-content-center border-top">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
