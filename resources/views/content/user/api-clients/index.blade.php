@extends('layouts/layoutMaster')

@section('title', 'Kelola API Keys (HMAC)')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
        'resources/assets/vendor/libs/select2/select2.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
        'resources/assets/vendor/libs/select2/select2.js'
    ])
@endsection

@section('content')
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">API Keys (HMAC)</h3>
                            <p class="text-white opacity-75 mb-0">Standar Otentikasi API Berbasis HMAC Signature dengan Token Binding.</p>
                        </div>
                        <i class="ti ti-shield-lock text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar API Keys</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createKeyModal">
                        <i class="ti ti-plus me-1"></i> Buat Key Baru
                    </button>
                </div>
                <div class="card-datatable table-responsive">
                    <table class="datatables-clients table border-top">
                        <thead>
                            <tr>
                                <th>#</th>
                                @if(auth()->user()->hasRole('admin'))
                                    <th>Pemilik</th>
                                    <th>Nama Dinas</th>
                                @endif
                                <th>Nama Aplikasi</th>
                                <th>Service Binding</th>
                                <th>Client ID</th>
                                <th>Status</th>
                                <th>Dibuat Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Key Modal -->
    <div class="modal fade" id="createKeyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat API Key Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createKeyForm">
                        <div class="mb-3">
                            <label class="form-label">Nama Aplikasi / Keterangan</label>
                            <input type="text" class="form-control" name="name" placeholder="contoh: Web Profil Desa" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Target Layanan (Optional)</label>
                            <select class="form-select select2" name="service_catalog_id" id="service_catalog_id" data-placeholder="Pilih Layanan...">
                                <option value="">Global / Tanpa Binding</option>
                                @foreach($serviceCatalogs as $catalog)
                                    <option value="{{ $catalog->id }}"
                                        data-slug="{{ $catalog->slug }}"
                                        data-requires-mapping="{{ $catalog->requires_mapping }}"
                                        data-can-customize="{{ $catalog->can_customize_mapping ?? 0 }}"
                                        data-mapping-field="{{ $catalog->mapping_field }}">
                                        {{ $catalog->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Mengikat Key hanya untuk layanan tertentu.</div>
                        </div>

                        <div class="mb-3 d-none" id="mapping_container">
                            <label class="form-label">Target SKPD (Super User Mode)</label>
                            <select class="form-select select2" name="skpd_code" id="skpd_code">
                                <option value="">Global Access (Semua SKPD)</option>
                                @foreach($agencies ?? [] as $agency)
                                    <option value="{{ $agency->code }}">{{ $agency->name }} ({{ $agency->code }})</option>
                                @endforeach
                            </select>
                            <div class="form-text text-primary">
                                <i class="ti ti-star me-1"></i> Anda memiliki izin khusus untuk memilih target SKPD.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-submit-key">Generate Key</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script type="module">
        $(function () {
            // Select2 Init
            $('.select2').select2({
                dropdownParent: $('#createKeyModal')
            });

            var dt_table = $('.datatables-clients');
            var isAdmin = "{{ auth()->user()->hasRole('admin') }}" === "1";

            var columns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            ];

            if (isAdmin) {
                columns.push({ data: 'user_name', name: 'user_name' });
                columns.push({ data: 'user_agency', name: 'user_agency' });
            }

            columns.push(
                { data: 'name', name: 'name' },
                { data: 'service_catalog_id', name: 'service_catalog_id', render: function(data, type, row) {
                     return row.service_catalog ? row.service_catalog.name : '<span class="badge bg-label-secondary">Global</span>';
                }},
                { data: 'api_key', name: 'api_key' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            );

            var dt_clients = dt_table.DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('api-clients.index') }}",
                columns: columns,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                order: [[ isAdmin ? 5 : 4, 'desc']] // Adjust sort column index
            });

            // Handle Service Selection Change
            $('#service_catalog_id').on('change', function() {
                var selectedOption = $(this).find(':selected');
                var canCustomize = selectedOption.data('can-customize') == 1; // Check Permission

                var container = $('#mapping_container');
                var select = $('#skpd_code');

                if (canCustomize) {
                     container.removeClass('d-none');
                } else {
                     container.addClass('d-none');
                     select.val('').trigger('change'); // Clear selection if hidden
                }
            });

            // Submit Create
            $('#btn-submit-key').on('click', function() {
                var btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

                $.ajax({
                    url: "{{ route('api-clients.store') }}",
                    type: 'POST',
                    data: $('#createKeyForm').serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    success: function(data) {
                        $('#createKeyModal').modal('hide');
                        $('#createKeyForm')[0].reset();
                        $('.select2').val('').trigger('change');
                        $('#mapping_container').addClass('d-none');

                        Swal.fire({
                            title: 'Credential Generated!',
                            html: `
                                <div class="text-start">
                                    <label class="small text-muted">Client ID</label>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" value="${data.api_key}" readonly>
                                    </div>
                                    <label class="small text-muted">Secret Key (HANYA MUNCUL SEKALI)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="${data.secret_key}" id="secret-key-copy" readonly>
                                        <button class="btn btn-outline-primary" onclick="copySecret()">Copy</button>
                                    </div>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: 'Saya Sudah Menyimpan Secret Key'
                        });
                        dt_clients.ajax.reload();
                    },
                    error: function(xhr) {
                         var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error creating key';
                         Swal.fire('Error', msg, 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('Generate Key');
                    }
                });
            });

            window.copySecret = function () {
                var copyText = document.getElementById("secret-key-copy");
                copyText.select();
                navigator.clipboard.writeText(copyText.value);
            };

            // Delete
            $(document).on('click', '.delete-client-btn', function () {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Revoke Key?',
                    text: "Aplikasi akan kehilangan akses.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Revoke!',
                    customClass: { confirmButton: 'btn btn-danger me-3', cancelButton: 'btn btn-label-secondary' },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('api-clients') }}/" + id,
                            type: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function (res) { dt_clients.ajax.reload(); Swal.fire('Revoked!', '', 'success'); }
                        });
                    }
                });
            });
        });
    </script>
@endsection
