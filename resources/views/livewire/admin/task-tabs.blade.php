<ul class="nav nav-tabs nav-fill" role="tablist">
    <li class="nav-item">
        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-users" aria-controls="navs-users" aria-selected="true">
            <i class="ti ti-user-plus me-1"></i> Pendaftaran User
            @if($pendingUsersCount > 0)
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1">{{ $pendingUsersCount }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-requests" aria-controls="navs-requests" aria-selected="false">
            <i class="ti ti-key me-1"></i> Permintaan Akses
            @if($pendingRequestsCount > 0)
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1">{{ $pendingRequestsCount }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tickets" aria-controls="navs-tickets" aria-selected="false">
            <i class="ti ti-ticket me-1"></i> Tiket Support
            @if($openTicketsCount > 0)
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger ms-1">{{ $openTicketsCount }}</span>
            @endif
        </button>
    </li>
</ul>
