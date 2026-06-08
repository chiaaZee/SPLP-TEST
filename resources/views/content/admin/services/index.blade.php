@extends('layouts/layoutMaster')

@section('title', 'Data Layanan (API)')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
        'resources/assets/vendor/libs/select2/select2.scss',
        'resources/assets/vendor/libs/@form-validation/form-validation.scss'
    ])
    <style>
        pre.json-result {
            background: #2f2f2f;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'node_modules/datatables.net-responsive-bs5/js/responsive.bootstrap5.js',
        'node_modules/datatables.net-buttons-bs5/js/buttons.bootstrap5.js',
        'node_modules/jszip/dist/jszip.js',
        'node_modules/pdfmake/build/pdfmake.js',
        'node_modules/datatables.net-buttons/js/buttons.html5.js',
        'node_modules/datatables.net-buttons/js/buttons.print.js',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
        'resources/assets/vendor/libs/select2/select2.js',
        'resources/assets/vendor/libs/@form-validation/popular.js',
        'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'resources/assets/vendor/libs/@form-validation/auto-focus.js'
    ])
@endsection

@section('page-script')
    @vite(['resources/assets/js/admin-services.js'])
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Master Data /</span> Data Layanan (Service Catalog)
    </h4>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-5 service_status"></div>
                <div class="col-md-7 text-md-end text-start">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalService">
                        <i class="ti ti-plus me-1"></i> Tambah Layanan
                    </button>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-services table">
                <thead class="border-top">
                    <tr>
                        <th></th>
                        <th>No</th>
                        <th>Nama Layanan</th>
                        <th>Perangkat Daerah</th>
                        <th>Endpoint</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal Service -->
    <div class="modal fade" id="modalService" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-edit-user">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2" id="modalTitle">Tambah Layanan API</h3>
                        <p class="text-muted">Daftarkan endpoint API dari Perangkat Daerah.</p>
                    </div>
                    <form id="serviceForm" class="row g-3" onsubmit="return false">
                        @csrf
                        <input type="hidden" id="service_id" name="id">

                        <div class="col-12 text-center mb-3">
                            <div class="alert alert-warning" role="alert">
                                <i class="ti ti-info-circle me-1"></i> Contoh Endpoint Gratis:
                                <b>https://jsonplaceholder.typicode.com/posts</b>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="agency_id">Perangkat Daerah Pemilik</label>
                            <select id="agency_id" name="agency_id" class="select2 form-select" data-allow-clear="true">
                                <option value="">Pilih Perangkat Daerah</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->name }} ({{ $agency->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="name">Nama Layanan</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Cek Data Penduduk" />
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="endpoint_url">Endpoint URL (Target)</label>
                            <input type="text" id="endpoint_url" name="endpoint_url" class="form-control"
                                placeholder="https://api.dinaskesehatan.go.id/v1/pasien" />
                            <div class="form-text">Masukkan URL lengkap dari server perangkat daerah tujuan.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="method">HTTP Method</label>
                            <select id="method" name="method" class="form-select">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="status">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description">Deskripsi (Opsional)</label>
                            <textarea id="description" name="description" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Simpan</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--/ Modal Service -->

    <!-- Modal Test API -->
    <div class="modal fade" id="modalTestApi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Test API Result</h3>
                        <p class="text-muted">Hasil request real-time dari server SPLPD ke Endpoint Tujuan.</p>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="fw-bold">Status:</label>
                            <span id="test_status" class="badge bg-label-secondary">-</span>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="fw-bold">Duration:</label>
                            <span id="test_duration" class="fw-bold">-</span>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="fw-bold">Response Body:</label>
                            <pre id="test_body" class="json-result">Waiting for response...</pre>
                        </div>
                    </div>

                    <div class="col-12 text-center">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!--/ Modal Test API -->
@endsection
