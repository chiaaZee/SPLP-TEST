/**
 * Page Admin Services
 */

'use strict';

// Datatable (jquery)
$(function () {
    var dt_table = $('.datatables-services'),
        modal = $('#modalService'),
        form = $('#serviceForm'),
        select2 = $('.select2');

    // Select2
    if (select2.length) {
        var $this = select2;
        $this.wrap('<div class="position-relative"></div>').select2({
            placeholder: 'Pilih Instansi',
            dropdownParent: $this.parent()
        });
    }

    // Users datatable
    if (dt_table.length) {
        var dt_service = dt_table.DataTable({
            processing: true,
            serverSide: true,
            ajax: baseUrl + 'admin/services',
            columns: [
                { data: '', defaultContent: '', orderable: false, searchable: false },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'agency_name', name: 'agency.name' },
                { data: 'endpoint_url', name: 'endpoint_url' },
                { data: 'method_label', name: 'method' },
                { data: 'status_label', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
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
                    targets: 4, // Endpoint
                    render: function (data, type, full, meta) {
                        return '<small class="text-truncate d-inline-block" style="max-width: 200px;">' + full['endpoint_url'] + '</small>';
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
                        { extend: 'print', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4, 5, 6] } },
                        { extend: 'csv', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4, 5, 6] } },
                        { extend: 'excel', className: 'dropdown-item', exportOptions: { columns: [1, 2, 3, 4, 5, 6] } }
                    ]
                }
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return 'Details ' + data['name'];
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
            $('#service_id').val('');
            $('#agency_id').val('').trigger('change');
            $('#modalTitle').text('Tambah Layanan API');
        });

        // Submit Form
        form.on('submit', function (e) {
            e.preventDefault();

            var id = $('#service_id').val();
            var url = id ? baseUrl + 'admin/services/' + id : baseUrl + 'admin/services';
            var method = id ? 'PUT' : 'POST';

            $.ajax({
                data: form.serialize(),
                url: url,
                type: method,
                success: function (data) {
                    modal.modal('hide');
                    dt_service.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.success,
                        customClass: { confirmButton: 'btn btn-success' }
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
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                }
            });
        });

        // Edit Record
        $(document).on('click', '.edit-record', function () {
            var id = $(this).data('id');
            $.get(baseUrl + 'admin/services/' + id + '/edit', function (data) {
                $('#modalTitle').text('Edit Layanan');
                $('#service_id').val(data.id);
                $('#name').val(data.name);
                $('#endpoint_url').val(data.endpoint_url);
                $('#method').val(data.method);
                $('#status').val(data.status);
                $('#description').val(data.description);
                $('#agency_id').val(data.agency_id).trigger('change');
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
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1' },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: baseUrl + 'admin/services/' + id,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (data) {
                            dt_service.ajax.reload();
                            Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.success, customClass: { confirmButton: 'btn btn-success' } });
                        },
                        error: function (data) {
                            Swal.fire({ icon: 'error', title: 'Error!', text: 'Gagal menghapus data.', customClass: { confirmButton: 'btn btn-danger' } });
                        }
                    });
                }
            });
        });

        // Test API Logic
        $(document).on('click', '.test-api', function () {
            var id = $(this).data('id');
            var modalTest = $('#modalTestApi');

            // Reset State
            $('#test_status').attr('class', 'badge bg-label-secondary').text('Loading...');
            $('#test_duration').text('...');
            $('#test_body').text('Sending Request...');

            modalTest.modal('show');

            $.ajax({
                url: baseUrl + 'admin/services/' + id + '/test',
                type: 'GET',
                success: function (response) {
                    var statusClass = response.success ? 'bg-label-success' : 'bg-label-danger';
                    $('#test_status').attr('class', 'badge ' + statusClass).text(response.status + (response.success ? ' OK' : ' Error'));
                    $('#test_duration').text(response.duration);

                    var body = response.body;
                    if (typeof body === 'object') {
                        body = JSON.stringify(body, null, 2);
                    }
                    $('#test_body').text(body);
                },
                error: function (err) {
                    $('#test_status').attr('class', 'badge bg-label-danger').text('System Error');
                    $('#test_duration').text('0 ms');
                    $('#test_body').text(JSON.stringify(err, null, 2));
                }
            });
        });
    }
});
