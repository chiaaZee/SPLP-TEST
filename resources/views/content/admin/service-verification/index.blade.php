@extends('layouts/layoutMaster')

@section('title', 'Verifikasi Layanan')

@section('content')
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Verifikasi Layanan</h3>
                            <p class="text-white opacity-75 mb-0">Verifikasi pengajuan layanan API baru dari perangkat daerah.</p>
                        </div>
                        <i class="ti ti-checklist text-white opacity-25" style="font-size: 5rem;"></i>
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
                    <i class="ti ti-clock me-1"></i> Permohonan Baru
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-history" aria-controls="navs-pills-history" aria-selected="false">
                    <i class="ti ti-history me-1"></i> Riwayat
                </button>
            </li>
        </ul>
        <div class="tab-content shadow-none border-0 p-0 bg-transparent">
            <div class="tab-pane fade show active" id="navs-pills-pending" role="tabpanel">
                <livewire:admin.service-verification-table :statusFilter="'pending'" />
            </div>
            <div class="tab-pane fade" id="navs-pills-history" role="tabpanel">
                <livewire:admin.service-verification-table :statusFilter="'history'" />
            </div>
        </div>
    </div>
@endsection
