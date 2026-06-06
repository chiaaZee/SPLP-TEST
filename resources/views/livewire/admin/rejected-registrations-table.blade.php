<div>
    <!-- Hero Section -->
    <!-- Hero Section -->
    @if($showBanner)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Riwayat Pendaftaran Ditolak</h3>
                            <p class="text-white opacity-75 mb-0">Daftar user yang pendaftarannya telah ditolak.</p>
                        </div>
                        <i class="ti ti-user-x text-white opacity-25" style="font-size: 5rem;"></i>
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
                            Nama
                            @if($sortField === 'name') <i class="ti ti-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                        </th>
                        <th>Email</th>
                        <th>Perangkat Daerah</th>
                        <th class="cursor-pointer" wire:click="sortBy('created_at')">
                            Tanggal Ditolak
                             @if($sortField === 'created_at') <i class="ti ti-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                        </th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($logs as $log)
                    <tr wire:key="{{ $log->id }}" class="animate__animated animate__fadeIn">
                        <td>
                            <span class="fw-medium">{{ $log->name }}</span>
                        </td>
                        <td>
                             {{ $log->email }}
                        </td>
                        <td>
                            @php
                                $userData = is_string($log->user_data) ? json_decode($log->user_data, true) : $log->user_data;
                                $agencyName = $userData['agency']['name'] ?? '-';
                            @endphp
                            <span class="fw-medium">{{ $agencyName }}</span>
                        </td>
                        <td>
                             {{ $log->created_at->format('d M Y, H:i') }}
                        </td>
                         <td>
                             {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="ti ti-playlist-x fs-1 text-muted mb-2"></i>
                            <p class="text-muted">Tidak ada riwayat penolakan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-3 d-flex justify-content-between align-items-center">
             <div class="text-muted small">Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries</div>
             <div>
                 {{ $logs->links() }}
             </div>
        </div>
    </div>
</div>
