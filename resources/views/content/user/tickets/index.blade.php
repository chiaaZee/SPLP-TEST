@extends('layouts/layoutMaster')

@section('title', 'Tiket Bantuan')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
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
                            <h3 class="text-white fw-bold mb-1">Tiket Bantuan</h3>
                            <p class="text-white opacity-75 mb-0">Kirim pertanyaan atau laporan masalah kepada Admin.</p>
                        </div>
                        <i class="ti ti-help-circle text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0">Daftar Tiket Saya</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                        <i class="ti ti-plus me-1"></i> Buat Tiket Baru
                    </button>
                </div>
                <div class="card-datatable table-responsive">
                    <table class="datatables-tickets table border-top">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subjek</th>
                                <th>Prioritas</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- New Ticket Modal -->
    <div class="modal fade" id="newTicketModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Tiket Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="newTicketForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Subjek</label>
                            <input type="text" name="subject" class="form-control" required
                                placeholder="Contoh: Gagal Akses API Kependudukan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prioritas</label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Rendah</option>
                                <option value="medium" selected>Sedang</option>
                                <option value="high">Tinggi (Urgent)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pesan / Deskripsi Masalah</label>
                            <textarea name="message" class="form-control" rows="5" required
                                placeholder="Jelaskan masalah Anda secara detail..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Tiket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Ticket Modal -->
    <div class="modal fade" id="viewTicketModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Tiket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="ticketDetailContent">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script type="module">
        $(function () {
            // DataTable
            const dt = $('.datatables-tickets').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('tickets.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'subject' },
                    { data: 'priority_badge' },
                    { data: 'status_badge' },
                    { data: 'created_at' },
                    { data: 'action' }
                ],
                order: [[4, 'desc']],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: { sLengthMenu: '_MENU_', search: '', searchPlaceholder: 'Cari...' }
            });

            // Create Ticket
            $('#newTicketForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('tickets.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        $('#newTicketModal').modal('hide');
                        $('#newTicketForm')[0].reset();
                        dt.ajax.reload();
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, showCancelButton: false });
                    },
                    error: function (xhr) {
                        Swal.fire({ icon: 'error', title: 'Error!', text: xhr.responseJSON?.message || 'Terjadi kesalahan.', showCancelButton: false });
                    }
                });
            });

            // View Ticket
            $(document).on('click', '.view-ticket', function () {
                const id = $(this).data('id');
                $.get("{{ url('tickets') }}/" + id, function (ticket) {
                    let html = `
                            <div class="mb-3"><strong>Subjek:</strong> ${ticket.subject}</div>
                            <div class="mb-3"><strong>Pesan:</strong><br><p class="bg-light p-3 rounded">${ticket.message}</p></div>
                            <hr>
                            <div class="mb-3"><strong>Balasan Admin:</strong><br>
                                ${ticket.admin_reply ? '<p class="bg-success-subtle p-3 rounded">' + ticket.admin_reply + '</p>' : '<em class="text-muted">Belum ada balasan.</em>'}
                            </div>
                        `;
                    $('#ticketDetailContent').html(html);
                    $('#viewTicketModal').modal('show');
                });
            });
        });
    </script>
@endsection
