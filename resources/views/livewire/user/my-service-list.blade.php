@section('title', 'Layanan Saya')

@section('page-style')
    <style>
        .service-icon-box {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        }
        .hover-lift:hover .service-icon-box {
            transform: scale(1.1);
        }

        /* Custom FadeInUp Animation */
        @keyframes fadeInUpCustom {
            from {
                opacity: 0;
                transform: translate3d(0, 40px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        .animate-fade-up {
            animation-name: fadeInUpCustom;
            animation-duration: 0.6s;
            animation-fill-mode: both;
        }

        /* Dark Mode Adjustments */
        .dark-style .service-card {
            background-color: #2f3349; /* Dark card bg */
            border-color: #434968;
        }
        .dark-style .bg-label-secondary {
            background-color: #424659 !important;
            color: #d0d4f1 !important;
        }

        /* Gradients for Stats not needed anymore as we use label classes, kept for ref if needed */
        .stat-box-info {
            background: linear-gradient(135deg, rgba(0,207,232,0.1) 0%, rgba(0,207,232,0.2) 100%);
            border: 1px solid rgba(0,207,232,0.1);
        }
        .stat-box-primary {
            background: linear-gradient(135deg, rgba(115,103,240,0.1) 0%, rgba(115,103,240,0.2) 100%);
            border: 1px solid rgba(115,103,240,0.1);
        }
        .dark-style .stat-box-info {
            background: rgba(0,207,232,0.15);
            border-color: rgba(0,207,232,0.2);
        }
        .dark-style .stat-box-primary {
            background: rgba(115,103,240,0.15);
            border-color: rgba(115,103,240,0.2);
        }
    </style>
@endsection

<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white overflow-hidden" style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%);">
                <div class="card-body p-4 position-relative">
                    <div class="row align-items-center">
                        <div class="col-md-8 position-relative z-1">
                            <h3 class="text-white fw-bold mb-2">Layanan Saya</h3>
                            <p class="text-white opacity-90 mb-4" style="max-width: 600px;">
                                Kelola layanan API perangkat daerah Anda di satu tempat. Ajukan layanan baru untuk dipublikasikan ke katalog SPLP setelah melalui proses verifikasi.
                            </p>
                            <button class="btn btn-white text-primary fw-bold waves-effect waves-light" wire:click="openModal">
                                <i class="ti ti-plus me-1"></i> Ajukan Layanan Baru
                            </button>
                        </div>
                        <div class="col-md-4 d-none d-md-block position-relative">
                             <i class="ti ti-server-2 position-absolute text-white opacity-25 animate-fade-up" style="font-size: 10rem; right: -20px; bottom: -60px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SOP & Filter Row -->
    <div class="row mb-4">
        <div class="col-12">
             <div class="card mb-4 bg-label-secondary border-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-info-circle ti-md text-primary me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-heading">Alur Pengajuan Layanan</h6>
                                <small class="text-muted">1. Ajukan & Lengkapi Data &rarr; 2. Verifikasi Admin &rarr; 3. Terbit di Katalog</small>
                            </div>
                        </div>
                        <div class="ms-auto" style="min-width: 250px;">
                             <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" placeholder="Cari layanan saya..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="row g-4">
        @forelse($services as $service)
            <div class="col-md-6 col-lg-4 animate-fade-up" style="animation-duration: 0.5s; animation-delay: {{ $loop->iteration * 0.1 }}s">
                <div class="card h-100 border-0 shadow-sm hover-lift position-relative overflow-hidden service-card group">
                    <!-- Decor Circles -->
                    <div class="position-absolute top-0 end-0 p-3" style="opacity: 0.05;">
                        <i class="ti ti-server-2" style="font-size: 8rem; transform: rotate(-20deg) translate(20px, -20px);"></i>
                    </div>

                    <div class="card-body p-4 d-flex flex-column position-relative z-1">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center">
                                @php
                                    $iconColor = match($service->status) {
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        'inactive' => 'secondary',
                                        default => 'primary'
                                    };
                                    // Use template specific label classes for safe background colors
                                    $bgClass = 'bg-label-' . $iconColor;
                                @endphp
                                <div class="avatar avatar-md me-3">
                                    <span class="avatar-initial rounded-3 {{ $bgClass }}">
                                        <i class="ti ti-server-2 fs-4"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 me-3">
                                    <h5 class="fw-bold mb-0 text-heading text-wrap" title="{{ $service->name }}">{{ $service->name }}</h5>
                                    <small class="text-muted d-block text-truncate">{{ $service->category->name ?? 'Uncategorized' }}</small>
                                </div>
                            </div>

                            <!-- Status Badge -->
                             <div class="d-flex align-items-center">
                                <span class="badge {{ $bgClass }} rounded-pill px-3">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-muted mb-4 small" style="min-height: 40px; line-height: 1.6;">
                            {{ Str::limit($service->description, 90) }}
                        </p>

                        <!-- Stats Box -->
                        <div class="row g-2 mb-4 align-items-stretch mt-auto">
                             <div class="col-6">
                                 <div class="d-flex align-items-center p-3 rounded-3 h-100 bg-label-primary">
                                     <div class="me-3">
                                         <small class="d-block mb-1 text-uppercase text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Perangkat Daerah</small>
                                         <h5 class="mb-0 fw-bold text-primary counter-value" data-target="{{ $service->connected_agencies_count }}">0</h5>
                                     </div>
                                     <div class="avatar avatar-xs ms-auto">
                                         <span class="avatar-initial rounded-circle bg-primary text-white">
                                             <i class="ti ti-users" style="font-size: 0.9rem;"></i>
                                         </span>
                                     </div>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="d-flex align-items-center p-3 rounded-3 h-100 bg-label-info">
                                     <div class="me-3">
                                         <small class="d-block mb-1 text-uppercase text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Akses</small>
                                         <h5 class="mb-0 fw-bold text-info counter-value" data-target="{{ $service->api_logs_count }}">0</h5>
                                     </div>
                                      <div class="avatar avatar-xs ms-auto">
                                         <span class="avatar-initial rounded-circle bg-info text-white">
                                             <i class="ti ti-activity" style="font-size: 0.9rem;"></i>
                                         </span>
                                     </div>
                                 </div>
                             </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="d-flex align-items-center justify-content-between">
                             <div class="text-muted small">
                                 <i class="ti ti-calendar me-1"></i> {{ $service->created_at->format('d M Y') }}
                             </div>

                            @if($service->status == 'rejected')
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-label-danger" data-bs-toggle="tooltip" title="Alasan: {{ $service->rejection_reason }}">
                                        Lihat Alasan
                                    </button>
                                    <button class="btn btn-sm btn-primary" wire:click="edit({{ $service->id }})">
                                        <i class="ti ti-edit me-1"></i> Edit
                                    </button>
                                </div>
                            @else
                                <a href="{{ route('user.my-services.show', $service->slug) }}" class="btn btn-sm btn-icon btn-label-secondary rounded-pill waves-effect" data-bs-toggle="tooltip" title="Detail Layanan">
                                    <i class="ti ti-chevron-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Color Strip -->
                    <div class="position-absolute bottom-0 start-0 w-100 bg-{{ $iconColor }}" style="height: 4px;"></div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="d-flex flex-column align-items-center justify-content-center animate-fade-up">
                    <div class="mb-4 p-4 rounded-circle bg-label-primary bg-opacity-25">
                         <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}" alt="No Services" width="180" class="opacity-75" style="filter: grayscale(100%);">
                    </div>
                    <h4 class="text-heading fw-bold mb-2">Belum Ada Layanan</h4>
                    <p class="text-muted mb-4" style="max-width: 450px;">
                        Layanan yang Anda ajukan akan muncul di sini. Mulai berkontribusi dengan mempublikasikan layanan API Anda.
                    </p>
                    <button class="btn btn-primary btn-lg shadow-sm" wire:click="openModal">
                        <i class="ti ti-plus me-2"></i> Ajukan Layanan Baru
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $services->links() }}
    </div>

    <!-- Modal Pengajuan -->
    <!-- Modal Pengajuan -->
    @if($isModalOpen)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom p-4">
                    <div>
                        <h5 class="modal-title fw-bold text-heading">{{ $editingId ? 'Edit Layanan' : 'Ajukan Layanan Baru' }}</h5>
                        <small class="text-muted">Lengkapi data layanan untuk dipublikasikan.</small>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeModal" style="box-shadow: none;"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Left Side: Form -->
                        <div class="col-lg-8">
                             <form wire:submit.prevent="store">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Nama Layanan <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="ti ti-server-2"></i></span>
                                            <input type="text" wire:model="name" class="form-control" placeholder="Contoh: API Kependudukan V2" />
                                        </div>
                                        @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Kategori <span class="text-danger">*</span></label>
                                        <select wire:model="category_id" class="form-select">
                                            <option value="">Pilih Kategori...</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                    </div>
                                     <div class="col-md-6">
                                        <label class="form-label fw-medium">Perangkat Daerah</label>
                                        <input type="text" class="form-control bg-label-secondary" value="{{ auth()->user()->agency->name ?? '-' }}" readonly />
                                     </div>

                                    <!-- New Fields -->
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Target API URL (Base URL) <span class="text-danger">*</span></label>
                                        <input type="url" wire:model="base_url" class="form-control" placeholder="https://api.instansi.go.id/v1" />
                                        @error('base_url') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-medium">API Token (Bearer) <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="target_token" class="form-control" placeholder="Bearer eyJhbGciOi..." />
                                        @error('target_token') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Cover Image <span class="text-danger">*</span></label>
                                        <input type="file" wire:model="cover_image" class="form-control" accept="image/*" />
                                        @if ($cover_image)
                                            <div class="mt-2 text-center">
                                                <small class="d-block mb-1 text-muted">Preview Baru:</small>
                                                <img src="{{ $cover_image->temporaryUrl() }}" class="img-fluid rounded" style="max-height: 100px;">
                                            </div>
                                        @elseif ($editingId && $existingCover)
                                            <div class="mt-2 text-center">
                                                <small class="d-block mb-1 text-muted">Saat ini:</small>
                                                <img src="{{ Storage::url($existingCover) }}" class="img-fluid rounded" style="max-height: 100px;">
                                            </div>
                                        @endif
                                        @error('cover_image') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Dokumen UAT <span class="text-danger">*</span></label>
                                        <input type="file" wire:model="uat_document" class="form-control" accept=".pdf,.doc,.docx" />
                                        @if ($uat_document)
                                             <div class="mt-2">
                                                <small class="text-success"><i class="ti ti-check me-1"></i> File baru siap diupload</small>
                                            </div>
                                        @elseif ($editingId && $existingUat)
                                            <div class="mt-2">
                                                <small class="text-muted d-block mb-1">File Terlampir:</small>
                                                <a href="{{ Storage::url($existingUat) }}" download class="btn btn-xs btn-label-secondary">
                                                    <i class="ti ti-download me-1"></i> Download Dokumen Lama
                                                </a>
                                            </div>
                                        @endif
                                        @error('uat_document') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-medium">Deskripsi & Fungsi <span class="text-danger">*</span></label>
                                        <textarea wire:model="description" class="form-control" rows="3" placeholder="Jelaskan secara singkat kegunaan API ini..."></textarea>
                                        @error('description') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="mt-4 d-flex align-items-center gap-2">
                                    <button type="submit" class="btn btn-primary px-4" wire:loading.attr="disabled">
                                        <i class="ti ti-send me-2"></i> <span wire:loading.remove>{{ $editingId ? 'Simpan Perubahan' : 'Ajukan Layanan' }}</span><span wire:loading>Mengirim...</span>
                                    </button>
                                    <button type="button" class="btn btn-label-secondary" wire:click="closeModal">Batal</button>
                                </div>
                            </form>
                        </div>

                        <!-- Right Side: Info -->
                        <div class="col-lg-4">
                            <div class="card bg-label-primary border-0 mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded bg-primary text-white"><i class="ti ti-info-circle"></i></span>
                                        </div>
                                        <h6 class="mb-0 fw-bold text-primary">Alur Pengajuan</h6>
                                    </div>
                                    <ul class="ps-3 mb-0 text-muted small" style="list-style-type: circle;">
                                        <li class="mb-1">Isi formulir pengajuan.</li>
                                        <li class="mb-1">Admin melakukan verifikasi.</li>
                                        <li class="mb-1">Jadwal UAT (User Acceptance Test).</li>
                                        <li>Layanan <strong>Terbit</strong> di Katalog.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card border border-dashed">
                                <div class="card-body p-3 text-center">
                                    <div class="mb-2">
                                        <i class="ti ti-file-type-pdf text-danger fs-2"></i>
                                    </div>
                                    <h6 class="mb-1 small fw-bold">Dokumen Spesifikasi</h6>
                                    <p class="text-muted small mb-2" style="font-size: 0.75rem;">Wajib dilengkapi saat UAT.</p>
                                    <a href="{{ asset('templates/UAT_TEMPLATE.docx') }}" class="btn btn-xs btn-outline-primary w-100" download>
                                        <i class="ti ti-download me-1"></i> Download Template
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@script
<script>
    // Tooltips Init
    window.initMyServiceTooltips = () => {
         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
         var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
           return new bootstrap.Tooltip(tooltipTriggerEl);
         });
    }

    // Counter Animation
    window.initCounters = () => {
        const counters = document.querySelectorAll('.counter-value');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const duration = 1000; // ms
            const step = target / (duration / 16);

            let current = 0;
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.innerText = Math.ceil(current).toLocaleString('id-ID');
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.innerText = target.toLocaleString('id-ID');
                }
            };
            updateCounter();
        });
    }

    $wire.on('refresh-ui', () => {
        setTimeout(() => {
            window.initMyServiceTooltips();
            window.initCounters();
        }, 500);
    });

    document.addEventListener('livewire:navigated', () => {
        window.initMyServiceTooltips();
        window.initCounters();
    });

    // Init on load
    window.initMyServiceTooltips();
    window.initCounters();
</script>
@endscript
