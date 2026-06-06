@extends('layouts/layoutMaster')

<style>
    .catalog-card-hover {
        transition: all 0.3s ease-in-out;
        border: 1px solid transparent;
    }

    .catalog-card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        border-color: #7367f0 !important;
    }
</style>

@section('title', 'Katalog Layanan API')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/animate-css/animate.scss',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
        'resources/assets/vendor/libs/select2/select2.scss',
        'resources/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css',
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
        'resources/assets/vendor/libs/select2/select2.js',
        'resources/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js',
        'resources/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js',
        'resources/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js'
    ])
@endsection

@section('page-script')
    @include('content.admin.service-catalogs._request-access-script')

    <script type="module">
        $(function () {
            $('.select2').select2({ dropdownParent: $('#modalCatalog') });

            $('#catalogForm').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('service-catalogs.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        $('#modalCatalog').modal('hide');
                        $('#catalogForm')[0].reset();

                        // Append new card
                        if ($('#empty-state-placeholder').length) {
                            $('#empty-state-placeholder').remove();
                        }
                        $('#catalogs-container').prepend(res.html);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.success,
                            showConfirmButton: false,
                            timer: 3000,
                            customClass: {
                                popup: 'colored-toast'
                            }
                        });
                    },
                    error: function (err) {
                        Swal.fire({ icon: 'error', title: 'Error!', text: JSON.stringify(err.responseJSON.errors), showCancelButton: false });
                    }
                });
            });

            // Toggle Mapping Inputs
            $('#requires_mapping').on('change', function() {
                if($(this).is(':checked')) {
                    $('.mapping-config').removeClass('d-none');
                } else {
                    $('.mapping-config').addClass('d-none');
                }
            });
        });
    </script>
@endsection

@section('content')


    <!-- Tabs & Content -->
    <div class="nav-align-top mb-4">
        @can('manage_catalogs')
        <ul class="nav nav-tabs nav-fill" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-catalogs" aria-controls="navs-catalogs" aria-selected="true">
                    <i class="ti ti-server me-1"></i> Daftar Layanan
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-categories" aria-controls="navs-categories" aria-selected="false">
                    <i class="ti ti-category me-1"></i> Kategori Layanan
                </button>
            </li>
        </ul>
        @endcan
        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-catalogs" role="tabpanel">
                 <!-- Hero Section inside Tab -->
                 <div class="row mb-4">
                    <div class="col-12">
                        <div class="card text-white h-100 overflow-hidden"
                            style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-7 z-1">
                                        <h3 class="text-white fw-bold display-6 mb-2">Eksplorasi Katalog API</h3>
                                        <p class="mb-3 text-white lead opacity-75">Integrasikan sistem Anda dengan layanan publik yang efisien.</p>
                                        @can('manage_catalogs')
                                            <button class="btn btn-white text-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCatalog">
                                                <i class="ti ti-plus me-1"></i> Tambah Katalog
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                            <i class="ti ti-api position-absolute text-white opacity-25 animate__animated animate__pulse animate__infinite"
                                style="font-size: 15rem; right: 0; bottom: -4rem;"></i>
                        </div>
                    </div>
                </div>

                <!-- Livewire Catalog List -->
                @livewire('admin.service-catalog-list')
            </div>

            <div class="tab-pane fade" id="navs-categories" role="tabpanel">
                 <!-- Hero Section inside Tab -->
                 <div class="row mb-4">
                    <div class="col-12">
                        <div class="card text-white h-100 overflow-hidden"
                            style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-7 z-1">
                                        <h3 class="text-white fw-bold display-6 mb-2">Kelola Kategori</h3>
                                        <p class="mb-3 text-white lead opacity-75">Atur kategori untuk pengelompokan layanan API yang lebih rapi.</p>
                                    </div>
                                </div>
                            </div>
                            <i class="ti ti-category position-absolute text-white opacity-25 animate__animated animate__pulse animate__infinite"
                                style="font-size: 15rem; right: 0; bottom: -4rem;"></i>
                        </div>
                    </div>
                </div>

                @can('manage_catalogs')
                <!-- Livewire Category Table -->
                <div class="card">
                     @livewire('admin.service-category-table')
                </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Modal Catalog -->
    <div class="modal fade" id="modalCatalog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Buat Katalog Baru</h3>
                        <p class="text-muted">Kelompokkan API dalam satu katalog layanan.</p>
                    </div>
                    <form id="catalogForm" class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="agency_id">Instansi Pemilik</label>
                            <select id="agency_id" name="agency_id" class="select2 form-select" required>
                                <option value="">Pilih Instansi</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nama Katalog</label>
                            <input type="text" name="name" class="form-control" placeholder="Misal: Layanan Kependudukan"
                                required />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select select2">
                                <option value="">Pilih Kategori (Opsional)</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Base URL API</label>
                            <input type="url" name="base_url" class="form-control" placeholder="https://api.example.com" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Target Token (Optional)</label>
                            <input type="text" name="target_token" class="form-control" placeholder="Bearer Token Asli" />
                        </div>

                        <!-- Mapping Config -->
                        <div class="col-12">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="requires_mapping" name="requires_mapping" value="1">
                                <label class="form-check-label" for="requires_mapping">Layanan Membutuhkan Mapping Identitas?</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mapping-config d-none">
                            <label class="form-label">URL API Referensi (Dynamic Mapping)</label>
                            <input type="url" name="mapping_api_url" class="form-control" placeholder="Exp: .../api/splpd/v1/skpd" />
                            <div class="form-text">Endpoint untuk mengambil daftar referensi mapping.</div>
                        </div>
                        <div class="col-12 col-md-6 mapping-config d-none">
                            <label class="form-label">Nama Field Parameter</label>
                            <input type="text" name="mapping_field" class="form-control" value="skpd_id" placeholder="Exp: skpd_id" />
                            <div class="form-text">Nama field yang akan di-inject ke request.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Rate Limit (Req/Min)</label>
                            <input type="number" name="rate_limit" class="form-control" value="60" min="1" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Cover Image</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*" />
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Simpan</button>
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
