<div class="col-md-6 col-lg-4 col-xl-3 animate__animated animate__fadeInUp" style="animation-duration: 0.5s; animation-delay: {{ (isset($loop) ? $loop->iteration : 1) * 0.1 }}s">
    <div class="card h-100 border-0 shadow-sm hover-lift overflow-hidden position-relative group">
        <!-- Status Indicator Strip -->
        <!-- Status Indicator Strip -->
        <div class="position-absolute top-0 start-0 w-100 {{ $catalog->status == 'active' ? 'bg-success' : ($catalog->status == 'pending' ? 'bg-warning' : ($catalog->status == 'rejected' ? 'bg-danger' : 'bg-secondary')) }}" style="height: 4px;"></div>

        <div class="card-body p-4 d-flex flex-column">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="service-icon-box">
                    <i class="ti ti-server-2 fs-3"></i>
                </div>
                <!-- Status Badge & Category -->
                <div class="d-flex align-items-center gap-2">
                    @if($catalog->category)
                        <span class="badge bg-label-info rounded-pill">{{ $catalog->category->name }}</span>
                    @endif
                    @php
                        $statusClass = match($catalog->status) {
                            'active' => 'bg-label-success',
                            'pending' => 'bg-label-warning',
                            'rejected' => 'bg-label-danger',
                            default => 'bg-label-secondary',
                        };
                         $statusLabel = match($catalog->status) {
                            'active' => 'Aktif',
                            'pending' => 'Menunggu',
                            'rejected' => 'Ditolak',
                            'inactive' => 'Nonaktif',
                            'draft' => 'Draft',
                            default => ucfirst($catalog->status),
                        };
                    @endphp
                    <span class="badge {{ $statusClass }} rounded-pill">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <!-- Title & Agency -->
            <div class="mb-3">
                <h5 class="fw-bold mb-1 text-truncate" title="{{ $catalog->name }}">
                    {{ $catalog->name }}
                </h5>
                <div class="d-flex align-items-center text-muted small">
                    <i class="ti ti-building-arch me-1 fs-6"></i>
                    <span class="text-truncate" style="max-width: 200px;">{{ $catalog->agency->name }}</span>
                </div>
            </div>

            <!-- Description -->
            <p class="text-muted small mb-4 flex-grow-1" style="min-height: 40px; line-height: 1.5;">
                {{ Str::limit($catalog->description ?? 'Tidak ada deskripsi tersedia.', 80) }}
            </p>

            <!-- Stats & Footer -->
            <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 border-top mt-auto gap-2">
                <div class="d-flex align-items-center text-heading">
                    <i class="ti ti-api me-1"></i>
                    <span class="fw-bold">{{ $catalog->endpoints_count ?? 0 }}</span>
                    <span class="small ms-1 text-muted">Endpoints</span>
                </div>

                <!-- Action Button -->
                @php
                    $isAdmin = auth()->user()->can('manage_catalogs');
                    $myReq = isset($userRequests) && $userRequests ? $userRequests->get($catalog->id) : null;
                    $reqStatus = $myReq ? $myReq->status : null;
                @endphp

                @if($isAdmin)
                    <div class="d-flex gap-2">
                        <a href="{{ route('service-catalogs.show', $catalog->slug) }}" class="btn btn-xs btn-primary rounded-pill waves-effect waves-light px-2">
                            Details <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                        <button type="button" class="btn btn-xs btn-label-danger rounded-pill delete-catalog-btn px-2" data-id="{{ $catalog->slug }}" data-name="{{ $catalog->name }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                @elseif($catalog->status === 'active')
                     @if($reqStatus == 'approved')
                        <a href="{{ route('service-catalogs.show', $catalog->slug) }}" class="btn btn-xs btn-label-success rounded-pill waves-effect px-2">
                            <i class="ti ti-book-2 me-1"></i> Documentation
                        </a>
                    @elseif($reqStatus == 'pending')
                         <span class="badge bg-label-warning rounded-pill px-2"><i class="ti ti-clock me-1"></i> Pending</span>
                    @elseif($reqStatus == 'rejected')
                        <button class="btn btn-xs btn-label-danger rounded-pill request-access-btn px-2" data-id="{{ $catalog->id }}">
                            <i class="ti ti-refresh me-1"></i> Re-apply
                        </button>
                    @else
                        <button class="btn btn-xs btn-outline-primary rounded-pill waves-effect request-access-btn px-2" data-id="{{ $catalog->id }}" data-name="{{ $catalog->name }}">
                            <i class="ti ti-lock-open me-1"></i> Request Access
                        </button>
                    @endif
                @else
                    <span class="badge bg-label-secondary rounded-pill px-2"><i class="ti ti-ban me-1"></i> Inactive</span>
                @endif
            </div>
        </div>
    </div>
</div>
