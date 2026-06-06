<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white overflow-hidden" style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4 position-relative">
                    <div class="row align-items-center">
                        <div class="col-md-9 position-relative z-1">
                            <h3 class="text-white fw-bold mb-2">Permintaan Masuk</h3>
                            <p class="text-white opacity-90 mb-0">
                                Setujui atau tolak permintaan akses ke layanan API Anda. Setelah Anda setujui, permintaan akan diteruskan ke Admin SPLPD untuk verifikasi akhir.
                            </p>
                        </div>
                        <div class="col-md-3 d-none d-md-block position-relative">
                             <i class="ti ti-mail-forward position-absolute text-white opacity-25" style="font-size: 8rem; right: -20px; bottom: -50px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Layanan Diminta</th>
                        <th>Alasan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($requests as $request)
                    <tr wire:key="{{ $request->id }}">
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold">{{ $request->user->name }}</span>
                                <small class="text-muted">{{ $request->user->agency->name ?? '-' }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-primary">{{ $request->serviceCatalog->name }}</span>
                        </td>
                        <td>
                            <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $request->reason }}">
                                {{ $request->reason }}
                            </span>
                            @if($request->attachment)
                                <a href="{{ Storage::url($request->attachment) }}" target="_blank" class="ms-1 text-primary"><i class="ti ti-link"></i> Doc</a>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small">{{ $request->created_at->format('d M Y H:i') }}</span>
                        </td>
                        <td>
                            <button wire:click="approve({{ $request->id }})" class="btn btn-sm btn-success me-1">
                                <i class="ti ti-check me-1"></i> Setujui
                            </button>
                            <button wire:click="confirmReject({{ $request->id }})" class="btn btn-sm btn-label-danger">
                                <i class="ti ti-x me-1"></i> Tolak
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="ti ti-inbox fs-1 text-muted mb-2"></i>
                            <p class="text-muted">Tidak ada permintaan masuk yang menunggu persetujuan Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-3">
             {{ $requests->links() }}
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Permintaan</h5>
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
                    <button type="button" class="btn btn-danger" wire:click="reject">Tolak Permintaan</button>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('open-reject-modal', () => {
        var myModal = new bootstrap.Modal(document.getElementById('rejectModal'));
        myModal.show();
    });

    $wire.on('close-reject-modal', () => {
        var el = document.getElementById('rejectModal');
        var modal = bootstrap.Modal.getInstance(el);
        if (modal) {
            modal.hide();
        }
    });
</script>
@endscript
