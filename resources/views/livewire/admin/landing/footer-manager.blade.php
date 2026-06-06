@section('title', 'Admin - Pengaturan Footer')

@section('page-style')
<style>
    .purple-widget {
        background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);
        color: white;
    }
    .purple-widget i {
        opacity: 0.25;
        font-size: 5rem;
    }
</style>
@endsection

<div>
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card purple-widget h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Pengaturan Footer</h3>
                            <p class="text-white opacity-75 mb-0">Kelola informasi kontak dan sosial media di halaman depan.</p>
                        </div>
                        <i class="ti ti-layout-bottombar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
     <form wire:submit.prevent="save">
        <div class="row">
            <!-- Contact Info -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0"><i class="ti ti-address-book me-2"></i>Informasi Kontak</h5>
                    </div>
                    <div class="card-body mt-4">
                        <div class="mb-3">
                            <label class="form-label">Alamat Kantor</label>
                            <textarea wire:model="address" class="form-control" rows="3" placeholder="Alamat lengkap instansi..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Resmi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                <input type="email" wire:model="email" class="form-control" placeholder="admin@lumajangkab.go.id">
                            </div>
                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                <input type="text" wire:model="phone" class="form-control" placeholder="0812...">
                            </div>
                            @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                         <div class="mb-3">
                            <label class="form-label">Estimasi Waktu Respon</label>
                            <input type="text" wire:model="response_time" class="form-control" placeholder="Contoh: Respon dalam 24 jam kerja">
                             @error('response_time') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                         <div class="mb-3">
                            <label class="form-label">Jam Operasional</label>
                            <input type="text" wire:model="work_hours" class="form-control" placeholder="Contoh: Senin - Jumat (08:00 - 15:00)">
                             @error('work_hours') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                         <div class="mb-3">
                            <label class="form-label">Link Google Maps (Embed/Share)</label>
                            <textarea wire:model="google_map" class="form-control" rows="2" placeholder="https://maps.google.com..."></textarea>
                            @error('google_map') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                         <div class="mb-3">
                            <label class="form-label">Versi Aplikasi</label>
                            <input type="text" wire:model="app_version" class="form-control" placeholder="v1.0.0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0"><i class="ti ti-share me-2"></i>Social Media</h5>
                    </div>
                    <div class="card-body mt-4">
                        <div class="mb-3">
                            <label class="form-label">Facebook URL</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-brand-facebook"></i></span>
                                <input type="text" wire:model="facebook" class="form-control" placeholder="https://facebook.com/...">
                            </div>
                        </div>
                         <div class="mb-3">
                            <label class="form-label">Instagram URL</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-brand-instagram"></i></span>
                                <input type="text" wire:model="instagram" class="form-control" placeholder="https://instagram.com/...">
                            </div>
                        </div>
                         <div class="mb-3">
                            <label class="form-label">Twitter / X URL</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-brand-twitter"></i></span>
                                <input type="text" wire:model="twitter" class="form-control" placeholder="https://twitter.com/...">
                            </div>
                        </div>
                         <div class="mb-3">
                            <label class="form-label">Youtube Channel URL</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-brand-youtube"></i></span>
                                <input type="text" wire:model="youtube" class="form-control" placeholder="https://youtube.com/...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Actions -->
        <div class="row">
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="ti ti-device-floppy me-2"></i>Simpan Perubahan</span>
                    <span wire:loading><i class="ti ti-loader-2 ti-spin me-2"></i>Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>
</div>
