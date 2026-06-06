<div>
    <div class="card border-0 shadow-none">
        <div class="card-header border-bottom d-flex align-items-center justify-content-between p-3">
            <h5 class="mb-0 fw-bold">Daftar Pengajuan</h5>
            <div class="d-flex align-items-center" style="width: 300px;">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari layanan, user, atau instansi...">
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Layanan</th>
                        <th>Deskripsi</th>
                        <th>User / Instansi</th>
                        <th>Dokumen</th>
                        <th>Tanggal</th>
                        @if($statusFilter === 'history') <th>Status</th> @endif
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-heading">{{ $service->name }}</span>
                                    <small class="text-muted">{{ $service->category->name ?? '-' }}</small>
                                    @if($service->base_url)<small class="text-xs text-primary">{{ $service->base_url }}</small>@endif
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small d-block text-truncate" style="max-width: 250px;" title="{{ $service->description }}">{{ Str::limit($service->description, 50) }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ $service->user->name ?? 'Unknown' }}</span>
                                    <small class="text-muted">{{ $service->agency->name ?? '-' }}</small>
                                </div>
                            </td>
                            <td>
                                @if($service->uat_document_path)
                                    <button wire:click="downloadUAT('{{ $service->uat_document_path }}')" class="btn btn-xs btn-label-primary">
                                        <i class="ti ti-download me-1"></i> UAT
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $service->created_at->format('d M Y H:i') }}</small>
                            </td>
                            @if($statusFilter === 'history')
                                <td>
                                    @if($service->status == 'active')
                                        <span class="badge bg-label-success">Active</span>
                                    @elseif($service->status == 'rejected')
                                        <span class="badge bg-label-danger" data-bs-toggle="tooltip" title="{{ $service->rejection_reason }}">Rejected</span>
                                    @endif
                                </td>
                            @endif
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if($service->status == 'pending')
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="approve({{ $service->id }})">
                                                <i class="ti ti-check me-1"></i> Setujui
                                            </a>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);" wire:click="confirmReject({{ $service->id }})">
                                                <i class="ti ti-x me-1"></i> Tolak
                                            </a>
                                        @else
                                             <span class="dropdown-item text-muted disabled">Tidak ada aksi</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}" width="100" class="mb-3 opacity-50" style="filter: grayscale(100%);">
                                    <span class="text-muted">Tidak ada data ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">
            {{ $services->links() }}
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pengajuan Layanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea wire:model="rejectionReason" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan..."></textarea>
                        @error('rejectionReason') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" wire:click="reject" class="btn btn-danger">Tolak Layanan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));

            Livewire.on('open-reject-modal', () => {
                rejectModal.show();
            });

            Livewire.on('close-reject-modal', () => {
                rejectModal.hide();
            });


        });
    </script>
</div>
