@extends('layouts/layoutMaster')

@section('title', 'Kelola Template UAT')

@section('content')
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Kelola Template UAT</h3>
                            <p class="text-white opacity-75 mb-0">Upload dan perbarui template dokumen UAT untuk pengguna.</p>
                        </div>
                        <i class="ti ti-file-settings text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <livewire:admin.template-manager />
@endsection
