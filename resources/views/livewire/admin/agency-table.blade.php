<div>
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Manajemen Perangkat Daerah</h3>
                            <p class="text-white opacity-75 mb-0">Kelola data perangkat daerah yang terdaftar.</p>
                        </div>
                        <i class="ti ti-building-skyscraper text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Table -->
    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role">
                     <select wire:model.live="perPage" class="form-select w-auto">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-8 user_status">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control w-auto" placeholder="Cari Perangkat Daerah...">

                        <select wire:model.live="connectionStatus" class="form-select w-auto">
                            <option value="">Semua Status</option>
                            <option value="connected">Terhubung</option>
                            <option value="disconnected">Belum Terhubung</option>
                        </select>

                        <button class="btn btn-primary" wire:click="openModal">
                            <i class="ti ti-plus me-1"></i> Tambah Perangkat Daerah
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Logo</th>
                        <th class="cursor-pointer" wire:click="sortBy('name')">
                            Nama Perangkat Daerah
                            @if($sortField === 'name') <i class="ti ti-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                        </th>
                        <th>Kode</th>
                        <th>Kontak</th>
                        <th>Status</th>
                        <th>Katalog Digunakan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($agencies as $agency)
                    <tr wire:key="{{ $agency->id }}" class="animate__animated animate__fadeIn">
                        <td>{{ $loop->iteration + ($agencies->currentPage() - 1) * $agencies->perPage() }}</td>
                        <td>
                            <div class="avatar avatar-md">
                                @if($agency->logo)
                                <img src="{{ asset('assets/img/agency/' . $agency->logo) }}" alt="Logo" class="rounded-circle">
                                @else
                                <span class="avatar-initial rounded-circle bg-label-secondary">{{ substr($agency->name, 0, 2) }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="fw-medium">{{ $agency->name }}</span>
                            @if($agency->address)
                            <br><small class="text-muted text-truncate" style="max-width: 200px; display: inline-block;">{{ $agency->address }}</small>
                            @endif
                        </td>
                        <td><span class="badge bg-label-info">{{ $agency->code }}</span></td>
                        <td>
                            <div><i class="ti ti-mail me-1 text-muted"></i> {{ $agency->email ?? '-' }}</div>
                            <div><i class="ti ti-phone me-1 text-muted"></i> {{ $agency->phone ?? '-' }}</div>
                        </td>
                        <td>
                            @if($agency->status == 'active')
                                <span class="badge bg-label-success">Active</span>
                            @else
                                <span class="badge bg-label-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center cursor-pointer" wire:click="openServiceModal({{ $agency->id }})">
                                <span class="badge bg-label-primary me-2">{{ $this->getConnectedServiceCount($agency) }}</span>
                                <small class="text-muted">Layanan</small>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $agency->id }})">
                                        <i class="ti ti-pencil me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item text-danger" href="javascript:void(0);" wire:click="confirmDelete({{ $agency->id }})">
                                        <i class="ti ti-trash me-1"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="ti ti-building-off fs-1 text-muted mb-2"></i>
                            <p class="text-muted">Data instansi tidak ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-3 d-flex justify-content-between align-items-center">
             <div class="text-muted small">Showing {{ $agencies->firstItem() ?? 0 }} to {{ $agencies->lastItem() ?? 0 }} of {{ $agencies->total() }} entries</div>
             <div>
                 {{ $agencies->links() }}
             </div>
        </div>
    </div>

    <!-- Agency Form Modal -->
    <div class="modal fade" id="agencyModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-label-primary">
                    <h5 class="modal-title" id="agencyModalLabel">{{ $isEditMode ? 'Edit Perangkat Daerah' : 'Tambah Perangkat Daerah Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nama Perangkat Daerah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Dinas Komunikasi dan Informatika">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Perangkat Daerah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" wire:model="code" placeholder="DISKOMINFO">
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" wire:model="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="email@example.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone" placeholder="08123456789">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" wire:model="address" rows="2"></textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Logo Perangkat Daerah</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror" wire:model="logo">
                                <div wire:loading wire:target="logo" class="text-primary mt-1">Uploading...</div>
                                @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                @if ($logo)
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">Preview:</small>
                                        <img src="{{ $logo->temporaryUrl() }}" class="rounded" width="80">
                                    </div>
                                @elseif ($existingLogo)
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">Current Logo:</small>
                                        <img src="{{ asset('assets/img/agency/' . $existingLogo) }}" class="rounded" width="80">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $isEditMode ? 'Simpan Perubahan' : 'Simpan' }}</span>
                            <span wire:loading><i class="ti ti-loader animate-spin me-1"></i> Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Services List Modal -->
    <div class="modal fade" id="servicesModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        <i class="ti ti-server me-2"></i>Katalog Layanan - {{ $selectedAgency->name ?? '' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeServiceModal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Nama Layanan</th>
                                    <th>Status Koneksi</th>
                                    <th>Terakhir Diakses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agencyServices as $service)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-medium text-heading">{{ $service->name }}</div>
                                        <small class="text-muted">{{ $service->category->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if(isset($serviceStatuses[$service->id]))
                                            @php $status = $serviceStatuses[$service->id]; @endphp
                                            @if($status['status'] == 'connected')
                                                <span class="badge bg-success">Connected</span>
                                            @elseif($status['status'] == 'disconnected')
                                                <span class="badge bg-danger">Disconnected ({{ $status['code'] }})</span>
                                            @else
                                                <span class="badge bg-label-secondary">Belum ada akses</span>
                                            @endif
                                        @else
                                            <span class="badge bg-label-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($serviceStatuses[$service->id]) && $serviceStatuses[$service->id]['last_check'])
                                            <div class="small text-muted">
                                                {{ \Carbon\Carbon::parse($serviceStatuses[$service->id]['last_check'])->diffForHumans() }}
                                            </div>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ \Carbon\Carbon::parse($serviceStatuses[$service->id]['last_check'])->format('d M Y H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ti ti-server-off fs-1 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Belum ada layanan yang disetujui.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-body">
                    <button type="button" class="btn btn-label-secondary" wire:click="closeServiceModal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const agencyModalElement = document.getElementById('agencyModal');
            const agencyModal = new bootstrap.Modal(agencyModalElement);

            Livewire.on('open-agency-modal', () => {
                agencyModal.show();
            });

            Livewire.on('close-agency-modal', () => {
                agencyModal.hide();
            });

            // Handle manual close (backdrop click) to reset form state in Livewire if needed
            agencyModalElement.addEventListener('hidden.bs.modal', () => {
                Livewire.dispatch('reset-modal-state'); // Optional: Add listener in PHP if strict reset needed
            });

            // Services Modal
            const servicesModalElement = document.getElementById('servicesModal');
            const servicesModal = new bootstrap.Modal(servicesModalElement);

            Livewire.on('open-service-modal', () => {
                servicesModal.show();
            });

            Livewire.on('close-service-modal', () => {
                servicesModal.hide();
            });
        });
    </script>
</div>
