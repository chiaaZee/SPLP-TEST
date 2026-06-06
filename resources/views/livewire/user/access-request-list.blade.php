<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white overflow-hidden" style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4 position-relative">
                    <div class="row align-items-center">
                        <div class="col-md-9 position-relative z-1">
                            <h3 class="text-white fw-bold mb-2">Permohonan Akses API Saya</h3>
                            <p class="text-white opacity-90 mb-0">Pantau status permohonan akses Anda ke berbagai layanan.</p>
                        </div>
                        <div class="col-md-3 d-none d-md-block position-relative">
                             <i class="ti ti-file-certificate position-absolute text-white opacity-25" style="font-size: 8rem; right: -20px; bottom: -50px;"></i>
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
                        <th>Layanan</th>
                        <th>Penyedia</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Tanggal Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($requests as $request)
                    <tr wire:key="{{ $request->id }}">
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-heading">{{ $request->serviceCatalog->name ?? '-' }}</span>
                                <small class="text-muted">{{ $request->serviceCatalog->category->name ?? '-' }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">{{ $request->serviceCatalog->user->agency->name ?? ($request->serviceCatalog->user->name ?? '-') }}</span>
                        </td>
                        <td>
                            @php
                                $status = $request->status;
                                $badgeClass = match($status) {
                                    'pending_owner' => 'bg-label-warning',
                                    'pending_admin' => 'bg-label-info',
                                    'approved' => 'bg-label-success',
                                    'rejected' => 'bg-label-danger',
                                    default => 'bg-label-secondary'
                                };
                                $statusText = match($status) {
                                    'pending_owner' => 'Menunggu Pemilik',
                                    'pending_admin' => 'Verifikasi Admin SPLPD',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => 'Pending'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                        <td>
                            @if($status === 'rejected')
                                <small class="text-danger d-block">
                                    <strong>Alasan:</strong> {{ $request->admin_note ?? ($request->owner_note ?? '-') }}
                                </small>
                            @elseif($status === 'approved')
                                <small class="text-success"><i class="ti ti-check"></i> Akses diberikan</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small">{{ $request->created_at->format('d M Y H:i') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="ti ti-file-certificate fs-1 text-muted mb-2"></i>
                            <p class="text-muted">Anda belum mengajukan permohonan akses API apapun.</p>
                             <a href="{{ route('service-catalogs.index') }}" class="btn btn-primary mt-2">Jelajahi Katalog</a>
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
</div>
