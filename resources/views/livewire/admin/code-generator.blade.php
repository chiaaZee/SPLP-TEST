@section('title', 'Code Generator')

<div>
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Code Generator</h3>
                            <p class="text-white opacity-75 mb-0">Utilitas untuk membuat kode unik sementara (Ephemeral) dengan enkripsi SHA-256.</p>
                        </div>
                        <i class="ti ti-wand text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Instructions Column -->
        <div class="col-md-5 mb-4 mb-md-0">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Cara Penggunaan</h5>
                </div>
                <div class="card-body mt-4">
                    <ul class="timeline timeline-dashed">
                        <li class="timeline-item timeline-item-transparent pb-4">
                            <span class="timeline-point timeline-point-primary"></span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <h6 class="mb-0">1. Generate Kode</h6>
                                </div>
                                <div class="text-muted small">Klik tombol <b>Generate New Code</b> untuk membuat kode unik baru berbasis SHA-256.</div>
                            </div>
                        </li>
                        <li class="timeline-item timeline-item-transparent pb-4">
                            <span class="timeline-point timeline-point-warning"></span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <h6 class="mb-0">2. Salin Segera</h6>
                                </div>
                                <div class="text-muted small">Kode ini bersifat <b>ephemeral</b> (sementara) dan tidak disimpan di database. Salin segera setelah muncul.</div>
                            </div>
                        </li>
                        <li class="timeline-item timeline-item-transparent border-0 pb-0">
                            <span class="timeline-point timeline-point-info"></span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <h6 class="mb-0">3. Gunakan di Aplikasi</h6>
                                </div>
                                <div class="text-muted small">Gunakan kode ini untuk <code>SPLPD_ACCESS_TOKEN=...</code> di environment (.env) aplikasi sumber API.</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Generator Column -->
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5">

                    @if($generatedCode)
                        <div class="w-100 mb-4 animate__animated animate__fadeIn" x-data="{ copied: false }">
                            <div class="mb-3">
                                <div class="avatar avatar-xl mb-3 mx-auto">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <i class="ti ti-check ti-lg"></i>
                                    </span>
                                </div>
                                <h5 class="text-success mb-1">Kode Berhasil Dibuat!</h5>
                                <p class="text-muted">Silakan salin kode di bawah ini.</p>
                            </div>

                            <div class="position-relative">
                                <textarea
                                    class="form-control font-monospace text-center fs-5 text-primary fw-bold bg-label-secondary border-0 pt-3"
                                    id="generatedCodeInput"
                                    rows="3"
                                    readonly
                                >{{ $generatedCode }}</textarea>

                                <button
                                    type="button"
                                    class="btn position-absolute top-0 end-0 m-2 btn-sm"
                                    :class="copied ? 'btn-success' : 'btn-primary'"
                                    @click="
                                        navigator.clipboard.writeText($el.previousElementSibling.value);
                                        copied = true;
                                        setTimeout(() => copied = false, 2000);
                                        toastr.success('Kode berhasil disalin!', 'Berhasil');
                                    "
                                >
                                    <i class="ti" :class="copied ? 'ti-check' : 'ti-copy'"></i>
                                    <span x-text="copied ? 'Tersalin' : 'Copy'"></span>
                                </button>
                            </div>
                            <div class="form-text mt-3 text-warning bg-warning p-2 rounded bg-opacity-10">
                                <i class="ti ti-alert-triangle me-1"></i> Jangan refresh halaman sebelum menyimpan kode ini.
                            </div>
                        </div>
                    @else
                        <div class="text-center mb-4">
                            <div class="avatar avatar-xl mb-3 mx-auto">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="ti ti-lock-access ti-lg"></i>
                                </span>
                            </div>
                            <h5 class="mb-2">Generator Kode Aman</h5>
                            <p class="text-muted">Belum ada kode yang dibuat. Klik tombol di bawah untuk memulai.</p>
                        </div>
                    @endif

                    <button wire:click="generate" wire:loading.attr="disabled" class="btn btn-primary btn-lg w-100">
                        <span wire:loading.remove><i class="ti ti-wand me-2"></i> Generate New Code</span>
                        <span wire:loading><i class="ti ti-loader-2 ti-spin me-2"></i> Generating...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page-script')
    <script>
        // The copyToClipboard function is no longer needed as Alpine.js handles it.
        // Keeping it commented out or removing it based on preference.
        /*
        function copyToClipboard() {
            var copyText = document.getElementById("generatedCodeInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            navigator.clipboard.writeText(copyText.value).then(function() {
                toastr.success('Kode berhasil disalin ke clipboard!', 'Copied!');
            }, function(err) {
                toastr.error('Gagal menyalin kode.', 'Error');
            });
        }
        */
    </script>
@endpush
