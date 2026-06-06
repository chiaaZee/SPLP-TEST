@extends('layouts/layoutMaster')

@section('title', 'Kelola Tiket Bantuan')

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
                            <h3 class="text-white fw-bold mb-1">Kelola Tiket Bantuan</h3>
                            <p class="text-white opacity-75 mb-0">Kelola dan balas tiket dari user/dinas.</p>
                        </div>
                        <i class="ti ti-messages text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Semua Tiket Masuk</h5>
                </div>
                <div class="card-datatable table-responsive">
                    <table class="datatables-tickets table border-top">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pengirim</th>
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

    <!-- View/Reply Ticket Modal -->
    <div class="modal fade" id="replyTicketModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail & Balas Tiket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="replyTicketForm">
                    @csrf
                    <input type="hidden" name="ticket_id" id="ticketId">
                    <div class="modal-body">
                        <div id="ticketInfo" class="mb-4"></div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="ticketStatus" class="form-select" required>
                                <option value="open">Baru</option>
                                <option value="in_progress">Sedang Diproses</option>
                                <option value="resolved">Selesai</option>
                                <option value="closed">Ditutup</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Balasan Admin</label>
                            <textarea name="admin_reply" id="adminReply" class="form-control" rows="5" required
                                placeholder="Tulis balasan untuk user..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Balasan</button>
                    </div>
                </form>
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
                ajax: "{{ route('admin.tickets.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'user_name' },
                    { data: 'subject' },
                    { data: 'priority_badge' },
                    { data: 'status_badge' },
                    { data: 'created_at' },
                    { data: 'action' }
                ],
                order: [[5, 'desc']],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: { sLengthMenu: '_MENU_', search: '', searchPlaceholder: 'Cari...' }
            });

            // View Ticket
            $(document).on('click', '.view-ticket', function () {
                const id = $(this).data('id');
                $.get("{{ url('admin/tickets') }}/" + id, function (ticket) {
                    let html = `
                            <div class="row mb-2"><div class="col-3"><strong>Dari:</strong></div><div class="col-9">${ticket.user?.name || '-'}</div></div>
                            <div class="row mb-2"><div class="col-3"><strong>Subjek:</strong></div><div class="col-9">${ticket.subject}</div></div>
                            <div class="row mb-2"><div class="col-3"><strong>Pesan:</strong></div><div class="col-9"><div class="bg-light p-3 rounded">${ticket.message}</div></div></div>
                        `;
                    $('#ticketInfo').html(html);
                    $('#ticketId').val(ticket.id);
                    $('#ticketStatus').val(ticket.status);
                    $('#adminReply').val(ticket.admin_reply || '');
                    $('#replyTicketModal').modal('show');
                });
            });

            // Reply Ticket
            $('#replyTicketForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#ticketId').val();
                $.ajax({
                    url: "{{ url('admin/tickets') }}/" + id + "/reply",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        $('#replyTicketModal').modal('hide');
                        dt.ajax.reload();
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, showCancelButton: false });
                    },
                    error: function (xhr) {
                        Swal.fire({ icon: 'error', title: 'Error!', text: xhr.responseJSON?.message || 'Terjadi kesalahan.', showCancelButton: false });
                    }
                });
            });
        });
    </script>
@endsection
