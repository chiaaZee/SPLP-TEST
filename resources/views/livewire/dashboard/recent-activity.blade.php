<div wire:poll.5s class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0">Aktivitas Terakhir</h5>
        </div>
        <div>
            <span wire:loading class="spinner-border spinner-border-sm text-primary" role="status"></span>
            <a href="{{ route('api-logs.index') }}" class="btn btn-sm btn-primary ms-2">Lihat Semua</a>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Waktu</th>
                    @if($isAdmin)
                        <th>Pengguna</th>
                    @endif
                    <th>Layanan API</th>
                    <th>IP Address</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($recentActivities as $log)
                    <tr wire:key="log-{{ $log->id }}" class="animate__animated animate__fadeIn">
                        <td>{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</td>
                        @if($isAdmin)
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $log->user_name }}</span>
                                    <small class="text-muted">{{ $log->agency_name ?? 'Umum/Publik' }}</small>
                                </div>
                            </td>
                        @endif
                        <td>
                            <span class="fw-semibold text-primary">{{ $log->service_name }}</span>
                        </td>
                        <td>{{ $log->ip_address ?? '-' }}</td>
                        <td>
                            @if($log->status_code >= 200 && $log->status_code < 300)
                                <span class="badge bg-label-success">{{ $log->status_code }} OK</span>
                            @elseif($log->status_code >= 400 && $log->status_code < 500)
                                <span class="badge bg-label-warning">{{ $log->status_code }} Client Err</span>
                            @else
                                <span class="badge bg-label-danger">{{ $log->status_code }} Server Err</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 5 : 4 }}" class="text-center py-5 text-muted">
                            Belum ada aktivitas terbaru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
