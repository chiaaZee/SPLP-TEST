/**
 * Page Admin User
 */

'use strict';

// Datatable (jquery)
$(function () {
    var dt_table = $('.datatables-users'),
        modal = $('#modalUser'),
        form = $('#userForm'),
        select2 = $('.select2');

    // Select2
    if (select2.length) {
        var $this = select2;
        $this.wrap('<div class="position-relative"></div>').select2({
            placeholder: 'Pilih Instansi',
            dropdownParent: $this.parent()
        });
    }

    // Role Change Handler (Toggle Agency Field)
    $('#role').on('change', function () {
        var role = $(this).val();
        if (role === 'dinas') {
            $('#agency_div').slideDown();
        } else {
            $('#agency_div').slideUp();
            // Optional: clear selection if hidden
            // $('#agency_id').val('').trigger('change');
        }
    });

    // Trigger on init
    $('#role').trigger('change');

    // Users datatable
    if (dt_table.length) {
        var dt_user = dt_table.DataTable({
            processing: true,
            serverSide: true,
            ajax: baseUrl + 'admin/users',
            columns: [
                { data: '', defaultContent: '', orderable: false, searchable: false }, // For responsive control column
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role_label', name: 'role' },
                { data: 'agency_name', name: 'agency.name' },
                { data: 'status_label', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: 'control',
                    searchable: false,
                    orderable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return '';
                    }
                },
                {
                    targets: 2,
                    render: function (data, type, full, meta) {
                        return '<div class="d-flex justify-content-start align-items-center user-name">' +
                            '<div class="avatar-wrapper">' +
                            '<div class="avatar me-3">' +
                            '<span class="avatar-initial rounded-circle bg-label-secondary">' +
                            (full['name'].match(/\b\w/g) || []).slice(0, 2).join('').toUpperCase() +
                            '</span>' +
                            '</div>' +
                            '</div>' +
                            '<div class="d-flex flex-column">' +
                            '<span class="fw-medium">' +
                            full['name'] +
                            '</span>' +
                            '<small class="text-muted">' +
                            full['email'] +
                            '</small>' +
                            '</div>' +
                            '</div>';
                    }
                }
            ],
            order: [[2, 'asc']],
            dom:
                '<"row mx-2"' +
                '<"col-md-2"<"me-3"l>>' +
                '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>' +
                '>t' +
                '<"row mx-2"' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                '>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-label-secondary dropdown-toggle mx-3',
                    text: '<i class="ti ti-screen-share me-1 ti-xs"></i>Export',
                    buttons: [
                        {
                            extend: 'print',
                            text: '<i class="ti ti-printer me-2" ></i>Print',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
                        },
                        {
                            extend: 'csv',
                            text: '<i class="ti ti-file-text me-2" ></i>Csv',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1, 2, 3, 4, 5] }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1, 2, 3, 4, 5] }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="ti ti-file-description me-2"></i>Pdf',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1, 2, 3, 4, 5] }
                        },
                        {
                            extend: 'copy',
                            text: '<i class="ti ti-copy me-2" ></i>Copy',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1, 2, 3, 4, 5] }
                        }
                    ]
                }
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return 'Details of ' + data['name'];
                        }
                    }),
                    type: 'column',
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.title !== ''
                                ? '<tr data-dt-row="' +
                                col.rowIndex +
                                '" data-dt-column="' +
                                col.columnIndex +
                                '">' +
                                '<td>' +
                                col.title +
                                ':' +
                                '</td> ' +
                                '<td>' +
                                col.data +
                                '</td>' +
                                '</tr>'
                                : '';
                        }).join('');

                        return data ? $('<table class="table"/><tbody />').append(data) : false;
                    }
                }
            }
        });

        // Reset Form
        modal.on('hidden.bs.modal', function () {
            form[0].reset();
            $('#user_id').val('');
            $('#agency_id').val('').trigger('change');
            $('#role').trigger('change');
            $('#modalTitle').text('Tambah User Baru');
        });

        // Submit Form
        form.on('submit', function (e) {
            e.preventDefault();

            var id = $('#user_id').val();
            var url = id ? baseUrl + 'admin/users/' + id : baseUrl + 'admin/users';
            var method = id ? 'PUT' : 'POST';

            $.ajax({
                data: form.serialize(),
                url: url,
                type: method,
                success: function (data) {
                    modal.modal('hide');
                    dt_user.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.success,
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                },
                error: function (data) {
                    var errors = data.responseJSON.errors;
                    var errorMsg = '<ul>';
                    $.each(errors, function (key, value) {
                        errorMsg += '<li>' + value + '</li>';
                    });
                    errorMsg += '</ul>';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMsg,
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                }
            });
        });

        // Edit Record
        $(document).on('click', '.edit-record', function () {
            var id = $(this).data('id');
            $.get(baseUrl + 'admin/users/' + id + '/edit', function (data) {
                $('#modalTitle').text('Edit User');
                $('#user_id').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#role').val(data.role).trigger('change');
                $('#status').val(data.status);
                $('#agency_id').val(data.agency_id).trigger('change');
                $('#password').val(''); // Clear password field
                modal.modal('show');
            });
        });

        // Delete Record
        $(document).on('click', '.delete-record', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: baseUrl + 'admin/users/' + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            dt_user.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.success,
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                        },
                        error: function (data) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Gagal menghapus data.',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                }
                            });
                        }
                    });
                }
            });
        });
    }
});
