@extends('layouts/layoutMaster')

@section('title', 'Konfirmasi Pendaftaran')

@section('content')
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Manajemen Pendaftaran User</h3>
                            <p class="text-white opacity-75 mb-0">Setujui pendaftaran baru atau lihat riwayat penolakan.</p>
                        </div>
                        <i class="ti ti-users-group text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-pending" aria-controls="navs-pills-pending" aria-selected="true">
                    <i class="ti ti-user-check me-1"></i> Pendaftaran Baru
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-rejected" aria-controls="navs-pills-rejected" aria-selected="false">
                    <i class="ti ti-file-text me-1"></i> Log Registrasi
                </button>
            </li>
        </ul>
        <div class="tab-content shadow-none border-0 p-0 bg-transparent">
            <div class="tab-pane fade show active" id="navs-pills-pending" role="tabpanel">
                <livewire:admin.confirm-registrations-table :showBanner="false" />
            </div>
            <div class="tab-pane fade" id="navs-pills-rejected" role="tabpanel">
                <livewire:admin.registration-history-table :showBanner="false" />
            </div>
        </div>
    </div>
@endsection

@section('page-script')
{{-- Filament Notification automatically handled --}}
@endsection
