<div>
    <!-- Hero Section -->
    @if($showBanner)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Konfirmasi Pendaftaran</h3>
                            <p class="text-white opacity-75 mb-0">Setujui atau tolak pendaftaran user baru.</p>
                        </div>
                        <i class="ti ti-user-check text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-9 user_role"> <!-- Expanded width -->
                    <div class="d-flex align-items-center flex-wrap flex-md-nowrap">
                        <select wire:model.live="perPage" class="form-select w-auto me-3">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>

                        @if(count($selected) > 0)
                        <div class="d-flex align-items-center animate__animated animate__fadeIn flex-nowrap">
                            <span class="fw-bold me-3 text-primary text-nowrap">{{ count($selected) }} Terpilih</span>
                            <div class="d-flex gap-2 flex-nowrap">
                                <button wire:click="bulkApprove" wire:loading.attr="disabled" class="btn btn-sm btn-primary text-nowrap">
                                    <span wire:loading.remove wire:target="bulkApprove"><i class="ti ti-check me-1"></i> Setujui Semua</span>
                                    <span wire:loading wire:target="bulkApprove"><i class="ti ti-loader animate-spin me-1"></i> Loading...</span>
                                </button>
                                <button wire:click="confirmBulkReject" wire:loading.attr="disabled" class="btn btn-sm btn-label-secondary text-nowrap">
                                    <span wire:loading.remove wire:target="confirmBulkReject"><i class="ti ti-trash me-1"></i> Tolak Semua</span>
                                    <span wire:loading wire:target="confirmBulkReject"><i class="ti ti-loader animate-spin me-1"></i> Loading...</span>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <!-- Removed empty user_plan column -->
                <div class="col-md-3 user_status">
                    <div class="d-flex align-items-center justify-content-end gap-2">

                        <div class="d-flex align-items-center">
                             <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Cari Nama/Email...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">
                            <div class="form-check">
                                <input wire:model.live="selectAll" class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th class="cursor-pointer" wire:click="sortBy('name')">
                            User
                            @if($sortField === 'name') <i class="ti ti-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                        </th>
                        <th>Kontak</th>
                        <th>Perangkat Daerah</th>
                        <th class="cursor-pointer" wire:click="sortBy('created_at')">
                            Tanggal
                             @if($sortField === 'created_at') <i class="ti ti-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                        </th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($users as $user)
                    <tr wire:key="{{ $user->id }}">
                        <td>
                            <div class="form-check">
                                <input wire:model.live="selected" class="form-check-input" type="checkbox" value="{{ $user->id }}">
                            </div>
                        </td>
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
                             <span class="text-truncate d-flex align-items-center"><i class="ti ti-phone me-1"></i> {{ $user->phone ?? '-' }}</span>
                        </td>
                        <td>
                             <span class="fw-medium">{{ $user->agency ? $user->agency->name : '-' }}</span>
                            @if($user->agency && $user->agency->status == 'pending')
                                <br><small class="text-warning">(Instansi Baru)</small>
                            @endif
                        </td>
                        <td>
                             {{ $user->created_at->format('d M Y, H:i') }}
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="javascript:void(0);" wire:click="approve({{ $user->id }})">
                                        <i class="ti ti-check me-1"></i> Setujui
                                    </a>
                                    <a class="dropdown-item text-danger" href="javascript:void(0);" wire:click="confirmReject({{ $user->id }})">
                                        <i class="ti ti-trash me-1"></i> Tolak
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="ti ti-user-x fs-1 text-muted mb-2"></i>
                            <p class="text-muted">Tidak ada pendaftaran pending saat ini.</p>
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
</div>
