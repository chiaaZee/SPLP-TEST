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
                            <h3 class="text-white fw-bold mb-1">Permohonan Akses API</h3>
                            <p class="text-white opacity-75 mb-0">Kelola permohonan akses user terhadap katalog layanan.</p>
                        </div>
                        <i class="ti ti-key text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <!-- Tabs -->
            @if($showBanner)
            <div class="nav-align-top mb-4">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link {{ $statusFilter == 'pending' ? 'active' : '' }}" wire:click="$set('statusFilter', 'pending')">
                            <i class="ti ti-clock me-1"></i> Permohonan Baru
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link {{ $statusFilter == 'history' ? 'active' : '' }}" wire:click="$set('statusFilter', 'history')">
                            <i class="ti ti-file-text me-1"></i> Log Permohonan
                        </button>
                    </li>
                </ul>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-9 user_role">
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
                <div class="col-md-3 user_status">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <div class="d-flex align-items-center">
                             <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Cari...">
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
                        <th>Perangkat Daerah</th>
                        <th>Pemohon</th>
                        <th>Katalog API</th>
                        <th>Lampiran</th>
                        <th class="cursor-pointer" wire:click="sortBy('created_at')">
                            Tanggal
                             @if($sortField === 'created_at') <i class="ti ti-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                        </th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($requests as $req)
                    <tr wire:key="{{ $req->id }}" class="animate__animated animate__fadeIn">
                         <td>
                            <div class="form-check">
                                <input wire:model.live="selected" class="form-check-input" type="checkbox" value="{{ $req->id }}">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ $req->user->agency->name ?? 'N/A' }}</span>
                                <small class="text-muted">Kode: {{ $req->user->agency->code ?? '-' }}</small>
                            </div>
                        </td>
                        <td>
                             <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ $req->user->name ?? '-' }}</span>
                                <small class="text-muted">{{ $req->user->email ?? '-' }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-info">{{ $req->serviceCatalog->name ?? '-' }}</span>
                        </td>
                        <td>
                            @if($req->attachment)
                                <a href="{{ route('access-requests.download', $req->id) }}" class="btn btn-xs btn-label-primary">
                                    <i class="ti ti-download me-1"></i> Unduh
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                             {{ $req->created_at->format('d M Y, H:i') }}
                        </td>
                        <td>
                            @if($req->status == 'pending' || $req->status == 'pending_admin')
                                <span class="badge bg-label-primary">Pending Admin</span>
                            @elseif($req->status == 'pending_owner')
                                <span class="badge bg-label-warning">Pending Owner</span>
                            @elseif($req->status == 'approved')
                                <span class="badge bg-label-success">Disetujui</span>
                            @elseif($req->status == 'rejected')
                                <span class="badge bg-label-secondary">Ditolak</span>
                            @else
                                <span class="badge bg-label-secondary">{{ $req->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($req->status == 'pending' || $req->status == 'pending_admin')
                            <button type="button" class="btn btn-sm btn-label-primary" wire:click="openApprovalModal({{ $req->id }})">
                                <i class="ti ti-eye me-1"></i> Detail
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="ti ti-folder-off fs-1 text-muted mb-2"></i>
                            <p class="text-muted">Tidak ada permohonan akses saat ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-3 d-flex justify-content-between align-items-center">
             <div class="text-muted small">Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} entries</div>
             <div>
                 {{ $requests->links() }}
             </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-label-primary">
                    <h5 class="modal-title" id="approvalModalLabel">Detail Permohonan Akses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($approvingRequest)
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-2">Detail Pemohon</h6>
                            <div class="d-flex p-3 rounded bg-lighter border">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-label-info">{{ substr($approvingRequest->user->agency->name ?? 'A', 0, 2) }}</span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $approvingRequest->user->agency->name ?? 'Unknown Agency' }}</h6>
                                    <small class="text-muted">{{ $approvingRequest->user->name ?? '-' }} ({{ $approvingRequest->user->email ?? '-' }})</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-semibold mb-2">Layanan yang Diajukan</h6>
                             <div class="d-flex align-items-center p-2 border rounded">
                                <i class="ti ti-api fs-2 text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-0">{{ $approvingRequest->serviceCatalog->name ?? '-' }}</h6>
                                    <small class="text-muted">Kode: {{ $approvingRequest->serviceCatalog->slug ?? '-' }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="ti ti-info-circle me-2"></i>
                            <div>
                                Pastikan User ini berhak mengakses data layanan tersebut.
                            </div>
                        </div>

                        <hr>

                        <!-- Mapping Toggle -->
                        <div class="mb-3">
                            <label class="form-label d-block fw-bold mb-2">Izin Mapping Manual?</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" wire:model="canCustomizeMapping">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Izinkan Custom Mapping</label>
                            </div>
                            <small class="text-muted d-block ms-4">
                                Jika <strong>Aktif</strong>: User ini bisa membuat API Key yang mengakses <strong>SKPD Lain</strong> (via Dropdown).
                                <br>
                                Jika <strong>Nonaktif</strong>: API Key user ini terkunci otomatis ke SKPD mereka sendiri ({{ $approvingRequest->user->agency->code ?? 'CODE' }}).
                            </small>
                        </div>

                        <div class="mb-3">
                             <label class="form-label">Catatan Admin</label>
                             <textarea class="form-control" rows="2" wire:model="approvalNote" placeholder="Catatan persetujuan / penolakan..."></textarea>
                        </div>
                    @endif
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-label-danger" wire:click="reject({{ $approvingRequest->id ?? 0 }})" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> Tolak
                    </button>
                    <div>
                        <button type="button" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="submitApprove" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="ti ti-check me-1"></i> Setujui</span>
                            <span wire:loading><i class="ti ti-loader animate-spin me-1"></i> Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts to handle Modal -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const approvalModalElement = document.getElementById('approvalModal');
            const approvalModal = new bootstrap.Modal(approvalModalElement);

            Livewire.on('open-approval-modal', () => {
                approvalModal.show();
            });

            Livewire.on('close-approval-modal', () => {
                approvalModal.hide();
            });
        });
    </script>
</div>
