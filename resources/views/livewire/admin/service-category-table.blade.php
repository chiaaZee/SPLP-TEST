<div>
    <!-- Header -->
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Kelola Kategori Layanan</h5>
        <button wire:click="create" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Tambah Kategori
        </button>
    </div>

    <!-- Search & Filters -->
    <div class="card-body border-bottom p-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="input-group input-group-merge">
                    <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari kategori..." aria-label="Cari kategori..." aria-describedby="basic-addon-search31">
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Slug</th>
                    <th>Jumlah Layanan</th>
                    <th>Deskripsi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($categories as $category)
                <tr>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td><span class="badge bg-label-secondary">{{ $category->slug }}</span></td>
                    <td>
                        <span class="badge bg-label-info">{{ $category->services_count }} Terhubung</span>
                    </td>
                    <td>{{ Str::limit($category->description, 50) }}</td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $category->id }})">
                                    <i class="ti ti-pencil me-1"></i> Edit
                                </a>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmDeleteCategory({{ $category->id }})">
                                    <i class="ti ti-trash me-1"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5">Belum ada kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="card-footer">
        {{ $categories->links() }}
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalCategory" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Kategori' : 'Tambah Kategori' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" wire:model.live="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Kependudukan">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" wire:model="slug" class="form-control @error('slug') is-invalid @enderror">
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="3"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
        $wire.on('open-category-modal', () => {
            new bootstrap.Modal('#modalCategory').show();
        });

        $wire.on('close-category-modal', () => {
            const el = document.getElementById('modalCategory');
            const modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        });

        window.confirmDeleteCategory = function(id) {
            Swal.fire({
                title: 'Hapus Kategori?',
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
