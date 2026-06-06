@extends('layouts/layoutMaster')

@section('title', 'Notifikasi & Tugas')

@section('content')
<!-- Hero Section (Purple Widget) -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card text-white h-100 overflow-hidden"
            style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-md-7 z-1">
                        <h3 class="text-white fw-bold display-6 mb-2">Pusat Tugas Admin</h3>
                        <p class="mb-4 text-white lead opacity-75">Kelola semua permintaan pending, pendaftaran user baru, dan tiket support di satu tempat.</p>
                    </div>
                </div>
            </div>
            <i class="ti ti-clipboard-list position-absolute text-white opacity-25 animate__animated animate__pulse animate__infinite"
                style="font-size: 15rem; right: 0; bottom: -4rem;"></i>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="nav-align-top mb-4">
            <livewire:admin.task-tabs />
            <div class="tab-content">
                <!-- Pending Users -->
                <div class="tab-pane fade show active" id="navs-users" role="tabpanel">
                    <livewire:admin.confirm-registrations-table :showBanner="false" />
                </div>

                <!-- Access Requests -->
                <div class="tab-pane fade" id="navs-requests" role="tabpanel">
                    <livewire:admin.access-requests-table :statusFilter="'pending'" :showBanner="false" />
                </div>

                <!-- Tickets -->
                <div class="tab-pane fade" id="navs-tickets" role="tabpanel">
                    <livewire:admin.tickets-table :showBanner="false" :statusFilter="'open'" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
