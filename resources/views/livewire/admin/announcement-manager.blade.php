@section('title', 'Manajemen Pengumuman')

<div>
    {{-- SweetAlert2 is assumed to be loaded via layout or main.js --}}

    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Manajemen Pengumuman</h3>
                            <p class="text-white opacity-75 mb-0">Kelola pengumuman, banner, dan informasi penting untuk pengguna.</p>
                        </div>
                        <i class="ti ti-speakerphone text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                    <div class="col-md-4">
                        <h5 class="mb-0">Daftar Pengumuman</h5>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center justify-content-end gap-3">
                           <div class="w-px-250">
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari...">
                           </div>
                            <button wire:click="create" class="btn btn-primary text-nowrap">
                                <i class="ti ti-plus me-1"></i> Buat Pengumuman Baru
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Penempatan</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $item)
                            <tr>
                                <td>{{ $announcements->firstItem() + $loop->index }}</td>
                                <td>{{ $item->title }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->type == 'info' ? 'info' : ($item->type == 'warning' ? 'warning' : ($item->type == 'danger' ? 'danger' : 'success')) }}">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">{{ ucfirst($item->placement) }}</span>
                                </td>
                                <td>
                                    <small class="d-block">Mulai: {{ $item->start_date ? $item->start_date->format('d M Y H:i') : '-' }}</small>
                                    <small>Selesai: {{ $item->end_date ? $item->end_date->format('d M Y H:i') : '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->is_active ? 'success' : 'secondary' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $item->id }})">
                                                <i class="ti ti-pencil me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);" wire:click="confirmDelete({{ $item->id }})">
                                                <i class="ti ti-trash me-1"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data pengumuman.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div wire:ignore.self class="modal fade" id="announcementModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">{{ $announcementId ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$dispatch('close-modal')"></button>
                </div>
                <form wire:submit.prevent="{{ $announcementId ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Judul</label>
                                <input type="text" wire:model="title" class="form-control @error('title') is-invalid @enderror" placeholder="Judul Pengumuman">
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Isi Pengumuman</label>
                                <textarea wire:model="content" class="form-control @error('content') is-invalid @enderror" rows="3"></textarea>
                                @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipe (Warna)</label>
                                <select wire:model="type" class="form-select @error('type') is-invalid @enderror">
                                    <option value="info">Info (Biru)</option>
                                    <option value="warning">Warning (Kuning)</option>
                                    <option value="danger">Danger (Merah)</option>
                                    <option value="success">Success (Hijau)</option>
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penempatan</label>
                                <select wire:model="placement" class="form-select @error('placement') is-invalid @enderror">
                                    <option value="modal">Popup (Modal)</option>
                                    <option value="banner">Banner (Widget)</option>
                                </select>
                                @error('placement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" wire:model="start_date" class="form-control @error('start_date') is-invalid @enderror">
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="datetime-local" wire:model="end_date" class="form-control @error('end_date') is-invalid @enderror">
                                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveSwitch">
                                    <label class="form-check-label" for="isActiveSwitch">Aktifkan Pengumuman</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" wire:click="$dispatch('close-modal')">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $announcementId ? 'Simpan Perubahan' : 'Simpan' }}</span>
                            <span wire:loading><i class="ti ti-loader animate-spin me-1"></i> Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        const modalEl = document.getElementById('announcementModal');
        const modal = new bootstrap.Modal(modalEl);

        Livewire.on('open-modal', () => {
            modal.show();
        });

        Livewire.on('close-modal', () => {
            modal.hide();
        });
    });
</script>
@endscript
