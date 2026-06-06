<div>
    <div class="card shadow-none border-0">
        <div class="card-header border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold">Kelola Template UAT</h5>
            <small class="text-muted">Terakhir diperbarui: <span class="fw-bold">{{ $lastUpdated }}</span></small>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 border-end">
                    <h6 class="mb-3 text-uppercase small text-muted fw-bold">Upload Template Baru</h6>
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label class="form-label">File Template (Format: .docx, .doc, .pdf | Max: 10MB)</label>
                            <input type="file" wire:model="templateFile" class="form-control" accept=".docx,.doc,.pdf" />
                            @error('templateFile') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="ti ti-alert-triangle me-2"></i>
                            <div class="small">
                                Mengupload file baru akan <strong>menimpa</strong> template yang sudah ada secara permanen.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <i class="ti ti-upload me-2"></i> <span wire:loading.remove>Update Template</span><span wire:loading>Mengupload...</span>
                        </button>
                    </form>
                </div>
                <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                    <h6 class="mb-3 text-uppercase small text-muted fw-bold">Preview Template Saat Ini</h6>
                    <p class="text-muted mb-4 small">
                        Template ini digunakan oleh User sebagai acuan dokumen UAT saat mengajukan layanan baru. Pastikan template selalu up-to-date.
                    </p>

                    <button wire:click="downloadCurrent" class="btn btn-outline-primary w-100 py-3">
                        <i class="ti ti-file-text fs-3 mb-2 d-block"></i>
                        Download Template Aktif
                    </button>

                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted d-block mb-1">Lokasi File:</small>
                        <code class="text-xs bg-light p-1 rounded">public/templates/UAT_TEMPLATE.docx</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
             Livewire.on('show-toast', (event) => {
                 // Assuming standard toaster is available
                 // toastr.success(event.message);
            });
        });
    </script>
</div>
