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
                            <h3 class="text-white fw-bold mb-1">Tiket Support</h3>
                            <p class="text-white opacity-75 mb-0">Kelola tiket bantuan dari pengguna.</p>
                        </div>
                        <i class="ti ti-ticket text-white opacity-25" style="font-size: 5rem;"></i>
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
                     <select wire:model.live="statusFilter" class="form-select w-auto">
                        <option value="">Semua Status</option>
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="col-md-6 user_status">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                         <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Cari Tiket...">
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($tickets as $ticket)
                    <tr wire:key="{{ $ticket->id }}">
                        <td>
                            <span class="fw-medium">{{ Str::limit($ticket->subject, 30) }}</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-start align-items-center user-name">
                                <div class="avatar-wrapper">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($ticket->user->name, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ $ticket->user->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($ticket->status == 'open')
                                <span class="badge bg-label-success">Open</span>
                            @else
                                <span class="badge bg-label-secondary">Closed</span>
                            @endif
                        </td>
                         <td>
                             {{ $ticket->created_at->diffForHumans() }}
                        </td>
                        <td>
                             <button wire:click="openReplyModal({{ $ticket->id }})" class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                <i class="ti ti-message-dots"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="ti ti-ticket-off fs-1 text-muted mb-2"></i>
                            <p class="text-muted">Tidak ada tiket ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-3 d-flex justify-content-between align-items-center">
             <div class="text-muted small">Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} entries</div>
             <div>
                 {{ $tickets->links() }}
             </div>
        </div>

        <!-- Reply Modal -->
        <div class="modal fade" id="replyTicketModal" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <form wire:submit.prevent="submitReply">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Balas Tiket Support</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if($replyingTicket)
                            <div class="mb-3 p-3 bg-lighter rounded">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">{{ $replyingTicket->subject }}</span>
                                    <span class="text-muted small">{{ $replyingTicket->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="mb-0 text-muted">{{ $replyingTicket->message }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Respon Admin</label>
                                <textarea wire:model="replyMessage" class="form-control" rows="5" placeholder="Tulis balasan anda disini..."></textarea>
                                @error('replyMessage') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Update Status</label>
                                <select wire:model="replyStatus" class="form-select">
                                    <option value="open">Open</option>
                                    <option value="in_progress">Sedang Diproses</option>
                                    <option value="resolved">Selesai (Resolved)</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>Kirim Balasan</span>
                                <span wire:loading><i class="ti ti-loader animate-spin me-1"></i> Mengirim...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('livewire:initialized', () => {
                const replyModal = new bootstrap.Modal(document.getElementById('replyTicketModal'));

                Livewire.on('open-reply-modal', () => {
                    replyModal.show();
                });

                Livewire.on('close-reply-modal', () => {
                    replyModal.hide();
                });
            });
        </script>
    </div>
</div>
