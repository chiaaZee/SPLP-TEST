<div>
    <!-- Header -->
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Kelola Pengumuman</h5>
        <button wire:click="create" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Buat Pengumuman
        </button>
    </div>

    <!-- Search & Filters -->
    <div class="card-body border-bottom">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari pengumuman...">
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Konten</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($announcements as $item)
                <tr>
                    <td><strong>{{ $item->title }}</strong></td>
                    <td>
                        @php
                            $badgeClass = match($item->type) {
                                'info' => 'bg-label-info',
                                'warning' => 'bg-label-warning',
                                'danger' => 'bg-label-danger',
                                'success' => 'bg-label-success',
                                default => 'bg-label-secondary'
                            };
                            $icon = match($item->type) {
                                'info' => 'ti-info-circle',
                                'warning' => 'ti-alert-triangle',
                                'danger' => 'ti-alert-circle',
                                'success' => 'ti-check',
                                default => 'ti-bell'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            <i class="ti {{ $icon }} me-1"></i> {{ ucfirst($item->type) }}
                        </span>
                    </td>
                    <td>{{ Str::limit($item->content, 50) }}</td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:click="toggleStatus({{ $item->id }})" {{ $item->is_active ? 'checked' : '' }}>
                        </div>
                    </td>
                    <td class="text-center">
                        <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-2">
                            <i class="ti ti-pencil"></i>
                        </button>
                        <button onclick="confirmDeleteAnnouncement({{ $item->id }})" class="btn btn-sm btn-icon btn-text-danger rounded-pill">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">Belum ada pengumuman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="card-footer">
        {{ $announcements->links() }}
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalAnnouncement" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Pengumuman' : 'Buat Pengumuman' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" wire:model="title" class="form-control @error('title') is-invalid @enderror" placeholder="Contoh: Maintenance Server">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe</label>
                            <select wire:model="type" class="form-select @error('type') is-invalid @enderror">
                                <option value="info">Info</option>
                                <option value="warning">Warning</option>
                                <option value="danger">Danger (Critical)</option>
                                <option value="success">Success</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konten</label>
                            <textarea wire:model="content" class="form-control @error('content') is-invalid @enderror" rows="4"></textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveSwitch">
                            <label class="form-check-label" for="isActiveSwitch">Aktif (Tampilkan langsung)</label>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-label-secondary me-1" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('open-announcement-modal', () => {
            new bootstrap.Modal('#modalAnnouncement').show();
        });

        $wire.on('close-announcement-modal', () => {
            const el = document.getElementById('modalAnnouncement');
            const modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        });

        window.confirmDeleteAnnouncement = function(id) {
            Swal.fire({
                title: 'Hapus Pengumuman?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', id);
                }
            });
        }
    </script>
    @endscript
</div>
