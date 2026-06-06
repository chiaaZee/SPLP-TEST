<li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
      data-bs-auto-close="outside" aria-expanded="false">
      <i class="ti ti-bell ti-md"></i>
      @if($pendingCount > 0)
        <span class="badge bg-danger rounded-pill badge-notifications">{{ $pendingCount }}</span>
      @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-end py-0" style="min-width: 360px; max-width: 360px;">
      <li class="dropdown-menu-header border-bottom">
        <div class="dropdown-header d-flex align-items-center py-3">
          <h5 class="text-body mb-0 me-auto">Notification</h5>
            @if($pendingCount > 0)
             <a href="javascript:void(0)" wire:click="markAllRead" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip"
            data-bs-placement="top" title="Mark all as read"><i class="ti ti-mail-opened fs-4"></i></a>
            @endif
        </div>
      </li>
      <li class="dropdown-notifications-list scrollable-container">
        <ul class="list-group list-group-flush">
          @php $hasNotifications = false; @endphp

          @if($allNotifications->count() > 0)
            @php $hasNotifications = true; @endphp
            @foreach($allNotifications as $item)
              @php
                $className = class_basename($item);
                $isRead = false;
                // For System Notifications, we know read status.
                // For Tasks (User, Ticket, Request), they are physically "Unread" until processed,
                // but user wants them separated from Badge count.
                // We'll treat Tasks as "Read" visually (no bold, no dot) IF we want to signify they are just "Tasks".
                // But usually tasks need attention.
                // User said "tugasnya tetap ada" (tasks remain).
                // "jumlah angkanya hilang" (badge cleared).
                // Let's render Tasks normally purely as actionable items.

                if($className === 'DatabaseNotification') {
                    $isRead = !is_null($item->read_at);
                } else {
                    $isRead = true; // Tasks don't have "read" status for badge purposes, so show as "normal".
                }
              @endphp

              {{-- 1. System Notification --}}
              @if($className === 'DatabaseNotification')
                  <li class="list-group-item list-group-item-action dropdown-notifications-item {{ $isRead ? '' : 'bg-lighter' }}">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                            @php $status = $item->data['status'] ?? 'info'; @endphp
                            @if($status == 'approved')
                                <span class="avatar-initial rounded-circle bg-label-success"><i class="ti ti-check"></i></span>
                            @elseif($status == 'rejected')
                                <span class="avatar-initial rounded-circle bg-label-danger"><i class="ti ti-x"></i></span>
                            @else
                                <span class="avatar-initial rounded-circle bg-label-info"><i class="ti ti-bell"></i></span>
                            @endif
                        </div>
                      </div>
                      <div class="flex-grow-1 cursor-pointer" style="min-width: 0;" wire:click="markAsRead('{{ $item->id }}')">
                        <h6 class="mb-1 {{ $isRead ? '' : 'fw-bold' }} text-wrap">{{ $item->data['catalog_name'] ?? 'Notification' }}</h6>
                        <p class="mb-1 text-wrap" style="word-break: break-word; line-height: 1.3;">{{ $item->data['message'] ?? '' }}</p>
                        <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        @if(!$isRead)
                            <a href="javascript:void(0)" wire:click.stop="markAsRead('{{ $item->id }}')" class="dropdown-notifications-read"><span
                                class="badge badge-dot"></span></a>
                        @else
                            <a href="javascript:void(0)" class="dropdown-notifications-read text-muted"><i class="ti ti-mail-opened fs-5"></i></a>
                        @endif
                      </div>
                    </div>
                  </li>

              {{-- 2. Pending Registration (User) --}}
              @elseif($className === 'User')
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-warning">{{ substr($item->name, 0, 2) }}</span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $item->name }}</h6>
                        <p class="mb-1">{{ $item->agency?->name ?? 'Perangkat Daerah Belum Terdaftar' }}</p>
                        <small class="text-muted">Menunggu konfirmasi pendaftaran</small>
                        <div class="mt-1">
                          <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                        </div>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="{{ route('admin.confirm-registrations') }}" class="dropdown-notifications-read"><i class="ti ti-chevron-right text-muted"></i></a>
                      </div>
                    </div>
                  </li>

              {{-- 3. Open Ticket (SupportTicket) --}}
              @elseif($className === 'SupportTicket')
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-primary"><i class="ti ti-ticket"></i></span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">{{ Str::limit($item->subject, 30) }}</h6>
                        <p class="mb-1">{{ $item->user?->name ?? 'User' }}</p>
                        <small class="text-muted">Tiket baru menunggu balasan</small>
                        <div class="mt-1">
                          <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                        </div>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="{{ route('admin.tickets.index') }}" class="dropdown-notifications-read"><i class="ti ti-chevron-right text-muted"></i></a>
                      </div>
                    </div>
                  </li>

              {{-- 4. Access Request (ServiceAccessRequest) --}}
              @elseif($className === 'ServiceAccessRequest')
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                            @php $status = $item->status; @endphp
                            @if($status == 'approved')
                                <span class="avatar-initial rounded-circle bg-label-success"><i class="ti ti-check"></i></span>
                            @elseif($status == 'rejected')
                                <span class="avatar-initial rounded-circle bg-label-danger"><i class="ti ti-x"></i></span>
                            @else
                                <span class="avatar-initial rounded-circle bg-label-info"><i class="ti ti-key"></i></span>
                            @endif
                        </div>
                      </div>
                      <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $item->user?->name ?? 'User' }}</h6>
                            <p class="mb-1 text-truncate">{{ Str::limit($item->serviceCatalog?->name ?? 'Katalog', 25) }}</p>
                            <small class="text-muted">Permintaan akses API</small>
                            <small class="text-muted d-block">{{ $item->created_at->diffForHumans() }}</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="{{ route('access-requests.index') }}" class="dropdown-notifications-read"><i class="ti ti-chevron-right text-muted"></i></a>
                      </div>
                    </div>
                  </li>
              @endif

            @endforeach
          @endif

          {{-- No notifications --}}
          @if(!$hasNotifications)
            <li class="list-group-item list-group-item-action dropdown-notifications-item">
              <div class="text-center p-3">
                <small class="text-muted">Tidak ada notifikasi baru</small>
              </div>
            </li>
          @endif
        </ul>
      </li>
      <li class="dropdown-menu-footer border-top">
        <a href="{{ route('notifications.index') }}"
          class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
          View all notifications
        </a>
      </li>
    </ul>
</li>
