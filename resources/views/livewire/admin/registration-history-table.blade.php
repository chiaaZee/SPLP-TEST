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
                            <h3 class="text-white fw-bold mb-1">Log Riwayat Registrasi</h3>
                            <p class="text-white opacity-75 mb-0">Daftar user yang telah disetujui atau ditolak.</p>
                        </div>
                        <i class="ti ti-history text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-6 user_role">
                    <div class="d-flex align-items-center">
                        <select wire:model.live="perPage" class="form-select w-auto me-3">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 user_status">
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
                        <th class="cursor-pointer" wire:click="sortBy('name')">
                            User
                            @if($sortField === 'name') <i class="ti ti-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                        </th>
                        <th>Perangkat Daerah</th>
                        <th>Status</th>
                        <th class="cursor-pointer" wire:click="sortBy('updated_at')">
                            Tanggal Diproses
                             @if($sortField === 'updated_at') <i class="ti ti-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                        </th>
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
                            <span class="fw-medium">{{ $user->agency ? $user->agency->name : '-' }}</span>
                        </td>
                        <td>
                            @if($user->status == 'active')
                                <span class="badge bg-label-success">Disetujui</span>
                            @elseif($user->status == 'rejected')
                                <span class="badge bg-label-secondary">Ditolak</span>
                            @else
                                <span class="badge bg-label-info">{{ $user->status }}</span>
                            @endif
                        </td>
                        <td>
                             {{ $user->updated_at->format('d M Y, H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="ti ti-history fs-1 text-muted mb-2"></i>
                            <p class="text-muted">Tidak ada riwayat registrasi.</p>
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
