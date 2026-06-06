<div>
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Manajemen User</h3>
                            <p class="text-white opacity-75 mb-0">Kelola pengguna, hak akses, dan status akun.</p>
                        </div>
                        <i class="ti ti-users text-white opacity-25" style="font-size: 5rem;"></i>
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
                    <div class="d-flex align-items-center">
                        <select wire:model.live="perPage" class="form-select w-auto me-3">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <button class="btn btn-primary" wire:click="openModal">
                            <i class="ti ti-plus me-1"></i> Tambah User
                        </button>
                    </div>
                </div>
                <div class="col-md-8 user_status">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <!-- Status Filter -->
                        <div class="w-px-200">
                             <select wire:model.live="statusFilter" class="form-select">
                                <option value="all">Semua (Aktif & Suspend)</option>
                                <option value="active">Active (Aktif)</option>
                                <option value="suspended">Suspended (Suspend)</option>
                             </select>
                        </div>
                        <!-- Search -->
                        <div class="w-px-250">
                             <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Cari User...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="cursor-pointer" wire:click="sortBy('name')">User</th>
                        <th>Role</th>
                        <th>Perangkat Daerah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($users as $user)
                    <tr wire:key="{{ $user->id }}" class="animate__animated animate__fadeIn">
                        <td>
                            <div class="d-flex justify-content-start align-items-center user-name">
                                <div class="avatar-wrapper">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($user->name, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ $user->name }}</span>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                             @php
                                $roleColor = match($user->role) {
                                    'admin' => 'danger',
                                    'dinas' => 'info',
                                    'user' => 'primary',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-label-{{ $roleColor }}">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td>{{ $user->agency->name ?? '-' }}</td>
                        <td>
                            @php
                                $statusColor = match($user->status) {
                                    'active' => 'success',
                                    'suspended' => 'warning',
                                    'inactive' => 'secondary',
                                    'pending' => 'info',
                                    'rejected' => 'danger',
                                    default => 'secondary'
                                };
                                $statusIcon = match($user->status) {
                                    'active' => 'ti-circle-check',
                                    'suspended' => 'ti-alert-triangle',
                                    'inactive' => 'ti-user-off',
                                    default => 'ti-user'
                                };
                            @endphp
                            <span class="badge bg-label-{{ $statusColor }}">
                                <i class="ti {{ $statusIcon }} ti-xs me-1"></i>
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $user->id }})">
                                        <i class="ti ti-pencil me-1"></i> Edit
                                    </a>
                                     @if($user->status != 'suspended')
                                    <a class="dropdown-item text-warning" href="javascript:void(0);" wire:click="toggleSuspend({{ $user->id }})">
                                        <i class="ti ti-ban me-1"></i> Suspend
                                    </a>
                                    @else
                                    <a class="dropdown-item text-success" href="javascript:void(0);" wire:click="toggleSuspend({{ $user->id }})">
                                        <i class="ti ti-check me-1"></i> Unsuspend
                                    </a>
                                    @endif
                                    <a class="dropdown-item text-danger" href="javascript:void(0);" wire:click="confirmDelete({{ $user->id }})">
                                        <i class="ti ti-trash me-1"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="ti ti-users-off fs-1 text-muted mb-2"></i>
                            <p class="text-muted">Tidak ada user ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-3 d-flex justify-content-between align-items-center">
             <div class="text-muted small">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries</div>
             <div>
                 {{ $users->links() }}
             </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-label-primary">
                    <h5 class="modal-title" id="userModalLabel">{{ $isEditMode ? 'Edit User' : 'Tambah User Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="John Doe">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="john@example.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password {{ $isEditMode ? '(Kosongkan jika tidak ubah)' : '<span class="text-danger">*</span>' }}</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="********">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                             <div class="col-md-6 mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" wire:model.live="role">
                                    <option value="user">User Umum</option>
                                    <option value="dinas">Admin Dinas</option>
                                    <option value="admin">Super Admin</option>
                                </select>
                                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="active">Active (Aktif)</option>
                                    <option value="inactive">Inactive (Nonaktif)</option>
                                    <option value="suspended">Suspended (Ditangguhkan)</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            @if($role !== 'admin')
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Perangkat Daerah</label>
                                <select class="form-select @error('agency_id') is-invalid @enderror" wire:model="agency_id">
                                    <option value="">-- Pilih Perangkat Daerah --</option>
                                    @foreach($agencies as $agency)
                                        <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Wajib diisi untuk User Dinas/Umum yang mewakili perangkat daerah.</div>
                                @error('agency_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            @endif

                             <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" class="form-control @error('nip') is-invalid @enderror" wire:model="nip">
                                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" class="form-control @error('jabatan') is-invalid @enderror" wire:model="jabatan">
                                @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        @if($isEditMode)
                        <div class="alert alert-warning mt-2" role="alert">
                            <h6 class="alert-heading mb-1"><i class="ti ti-alert-triangle me-1"></i> Zona Bahaya</h6>
                            <p class="mb-0">Mengubah status ke <strong>Suspended</strong> akan mematikan semua akses API key milik user ini secara instan.</p>
                        </div>
                         <!-- Quick Suspend Button inside Modal -->
                         <div class="d-flex justify-content-end mt-2">
                             @if($status !== 'suspended')
                                <button type="button" class="btn btn-outline-warning" wire:click="$set('status', 'suspended')">
                                    <i class="ti ti-ban me-1"></i> Set Status to Suspended
                                </button>
                             @else
                                <button type="button" class="btn btn-outline-success" wire:click="$set('status', 'active')">
                                    <i class="ti ti-check me-1"></i> Set Status to Active
                                </button>
                             @endif
                         </div>
                        @endif

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

    <!-- Scripts -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const userModalElement = document.getElementById('userModal');
            const userModal = new bootstrap.Modal(userModalElement);

            Livewire.on('open-user-modal', () => {
                userModal.show();
            });

            Livewire.on('close-user-modal', () => {
                userModal.hide();
            });
        });
    </script>
</div>
