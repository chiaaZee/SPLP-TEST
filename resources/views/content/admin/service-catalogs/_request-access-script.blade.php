<script type="module">
    $(function () {
        // Shared Request Access Logic
        $(document).on('click', '.request-access-btn', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');

            if (typeof Swal === 'undefined') {
                alert('Library SweetAlert belum dimuat. Silakan refresh halaman.');
                return;
            }

            Swal.fire({
                title: 'Upload Dokumen Permohonan',
                text: "Silakan upload Surat Permohonan untuk: " + name,
                html: `
                        <div class="text-start">
                            <label for="swal-input-file" class="form-label">Pilih Dokumen (PDF/DOC/IMG)</label>
                            <input type="file" id="swal-input-file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png,.zip">
                        </div>
                    `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Kirim Dokumen',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const fileInput = document.getElementById('swal-input-file');
                    const file = fileInput.files[0];
                    if (!file) {
                        Swal.showValidationMessage('Mohon pilih file dokumen!');
                    }
                    return file;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    var formData = new FormData();
                    formData.append('service_catalog_id', id);
                    formData.append('attachment', result.value);

                    $.ajax({
                        url: "{{ route('service-catalogs.request-access') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (res) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Terkirim!',
                                text: res.message,
                                showConfirmButton: false,
                                timer: 3000,
                                customClass: {
                                    popup: 'colored-toast'
                                }
                            });
                            // Refresh Livewire Component without page reload
                            Livewire.dispatch('refreshCatalogList');
                        },
                        error: function (err) {
                            var msg = err.responseJSON.message || 'Terjadi kesalahan';
                            if (err.status == 422) {
                                msg = err.responseJSON.message || 'Harap cek kembali inputan anda.';
                            }
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: msg, showCancelButton: false });
                        }
                    });
                }
            });
        });
    });
</script>
