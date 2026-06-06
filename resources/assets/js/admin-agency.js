/**
 * Page Agency Management
 */

'use strict';

// Datatable (jquery)
$(function () {

    // Variable declaration for table
    var dt_table = $('.datatables-agency'),
        bsValidationForms = $('.needs-validation'),
        form = $('#agencyForm'),
        select2 = $('.select2');

    // Select2
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Select value',
                dropdownParent: $this.parent()
            });
        });
    }

    // Users datatable
    if (dt_table.length) {
        var dt_agency = dt_table.DataTable({
            processing: true,
            serverSide: true,
            ajax: baseUrl + 'admin/agency',
            columns: [
                { data: '', defaultContent: '', orderable: false, searchable: false },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'code', name: 'code' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
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
                    targets: 1, // Number column
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, meta) {
                        return data;
                    }
                }
            ],
            order: [[3, 'desc']], // Sort by name
            dom:
                '<"row me-2"' +
                '<"col-md-2"<"me-3"l>>' +
                '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"f>>' +
                '>t' +
                '<"row mx-2"' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                '>',
            displayLength: 10,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            language: {
                sLengthMenu: '_MENU_',
                search: '',
                searchPlaceholder: 'Cari Instansi..'
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return 'Detail of ' + data['name'];
                        }
                    }),
                    type: 'column',
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.title !== '' // ? Do not show row in modal parameter
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
    }

    // Load Satu Data List
    function loadSatuDataList() {
        var $sel = $('#satu_data_id');
        $.get(baseUrl + 'admin/agency-helper/satu-data/list', function (res) {
            $sel.empty().append('<option value="">Pilih Instansi Satu Data...</option>');
            $.each(res, function (i, item) {
                $sel.append(new Option(item.text, item.id));
            });
        }).fail(function () {
            console.error('Failed to load Satu Data list');
        });
    }
    loadSatuDataList();

    // Edit Record
    $(document).on('click', '.edit-record', function () {
        var agency_id = $(this).data('id');
        $('#modalTitle').text('Edit Instansi');
        $('#agency_id').val(agency_id);

        // Fetch Data
        $.get(baseUrl + 'admin/agency/' + agency_id + '/edit', function (data) {
            $('#code').val(data.code);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#phone').val(data.phone);
            $('#address').val(data.address);
            $('#status').val(data.status);

            $('#modalAgency').modal('show');
        });
    });

    // Create Record (Reset Form)
    $('#modalAgency').on('show.bs.modal', function (event) {
        if (!$('#agency_id').val()) {
            $('#modalTitle').text('Tambah Instansi Baru');
            form[0].reset();
        }
    });

    $('#modalAgency').on('hidden.bs.modal', function () {
        $('#agency_id').val('');
    });


    // Delete Record
    $(document).on('click', '.delete-record', function () {
        var agency_id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data instansi akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'DELETE',
                    url: baseUrl + 'admin/agency/' + agency_id,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        dt_agency.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: response.success,
                            customClass: { confirmButton: 'btn btn-success' }
                        });
                    },
                    error: function (error) {
                        Swal.fire({ icon: 'error', title: 'Error', text: error.responseJSON.message });
                    }
                });
            }
        });
    });

    // Form Validation & Submit
    if (form.length) {
        var fv = FormValidation.formValidation(form[0], {
            fields: {
                name: { validators: { notEmpty: { message: 'Nama instansi harus diisi' } } },
                code: { validators: { notEmpty: { message: 'Kode instansi harus diisi' } } },
                email: { validators: { notEmpty: { message: 'Email harus diisi' }, emailAddress: { message: 'Email tidak valid' } } }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: '',
                    rowSelector: '.col-12'
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            }
        }).on('core.form.valid', function () {
            var id = $('#agency_id').val();
            var url = id ? (baseUrl + 'admin/agency/' + id) : (baseUrl + 'admin/agency');

            var formData = new FormData(form[0]);
            if (id) {
                formData.append('_method', 'PUT'); // Simulate PUT for Laravel
            }

            $.ajax({
                url: url,
                type: 'POST', // Always POST for FormData (file upload)
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    $('#modalAgency').modal('hide');
                    dt_agency.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.success,
                        customClass: { confirmButton: 'btn btn-success' }
                    });
                },
                error: function (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: JSON.stringify(err.responseJSON.errors) });
                }
            });
        });
    }

});
