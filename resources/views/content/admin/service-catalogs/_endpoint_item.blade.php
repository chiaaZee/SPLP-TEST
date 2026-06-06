<div class="col-12 animate__animated animate__fadeIn mb-3"
    style="animation-delay: {{ (isset($loop) ? $loop->iteration * 0.1 : 0) . 's' }}" id="endpoint-item-{{ $endpoint->id }}">
    <div class="card endpoint-card mb-0 shadow-sm border-0" style="overflow: visible;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Left: Method & Info -->
                <div class="d-flex align-items-start flex-grow-1">
                    <div class="me-3">
                        <span class="method-badge method-{{ $endpoint->method }}">{{ $endpoint->method }}</span>
                    </div>
                    <div class="d-flex flex-column" style="min-width: 0;">
                        <h6 class="mb-1 text-heading fw-bold text-truncate" title="{{ $endpoint->name }}">
                            {{ $endpoint->name }}
                            @if(!$endpoint->is_public)
                                <span class="badge bg-label-secondary ms-2 text-xs">Admin Only</span>
                            @endif
                        </h6>
                        <div class="d-flex align-items-center">
                            <small class="text-muted font-monospace me-2 text-truncate">
                                <i class="ti ti-link me-1 text-primary"></i>
                                @if($endpoint->path && isset($catalog))
                                    <span
                                        class="fw-bold text-primary">{{ url('/') }}/api/{{ $catalog->slug }}{{ Str::start($endpoint->path, '/') }}</span>
                                    @if(auth()->user()->can('manage_catalogs'))
                                        <div class="text-xs text-muted">Target: {{ $endpoint->url }}</div>
                                    @endif
                                @else
                                    <span class="d-none d-sm-inline text-muted">{{ $endpoint->url }}</span>
                                    @if(auth()->user()->can('manage_catalogs'))
                                        <span class="badge bg-label-danger ms-2 edit-endpoint cursor-pointer"
                                            data-id="{{ $endpoint->id }}" data-name="{{ $endpoint->name }}"
                                            data-method="{{ $endpoint->method }}" data-path="{{ $endpoint->path }}"
                                            data-url="{{ $endpoint->url }}" data-body="{{ $endpoint->request_body }}">
                                            <i class="ti ti-alert-circle me-1"></i> Set Gateway Path
                                        </span>
                                    @endif
                                @endif
                            </small>
                        </div>
                        @if($endpoint->description)
                            <small class="text-muted text-truncate mt-1"
                                style="max-width: 500px;">{{ $endpoint->description }}</small>
                        @endif
                        @if($endpoint->request_body)
                            <div class="mt-2">
                                <button class="btn btn-xs btn-outline-secondary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#body-{{ $endpoint->id }}">
                                    <i class="ti ti-code me-1"></i> Lihat Request Body
                                </button>
                                <div class="collapse mt-2" id="body-{{ $endpoint->id }}">
                                    <div class="card card-body bg-light p-2 small font-monospace">
                                        {{ $endpoint->request_body }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="ms-3">
                    @can('manage_catalogs')
                        {{-- Admin: 3-dot dropdown menu --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical fs-5"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('service-catalogs.endpoint.detail', ['catalog' => isset($catalog) ? $catalog->slug : $endpoint->catalog->slug, 'id' => $endpoint->slug ?? $endpoint->id]) }}">
                                        <i class="ti ti-eye me-2"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item edit-endpoint cursor-pointer"
                                        data-id="{{ $endpoint->id }}"
                                        data-name="{{ $endpoint->name }}"
                                        data-method="{{ $endpoint->method }}"
                                        data-path="{{ $endpoint->path }}"
                                        data-url="{{ $endpoint->url }}"
                                        data-body="{{ $endpoint->request_body }}"
                                        data-is-public="{{ $endpoint->is_public }}">
                                        <i class="ti ti-pencil me-2"></i> Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger delete-endpoint cursor-pointer"
                                        data-id="{{ $endpoint->id }}"
                                        data-name="{{ $endpoint->name }}">
                                        <i class="ti ti-trash me-2"></i> Hapus
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @else
                        {{-- Public/Dinas: Detail button only --}}
                        <a href="{{ route('service-catalogs.endpoint.detail', $endpoint->slug ?? $endpoint->id) }}"
                            class="btn btn-sm btn-label-info" data-bs-toggle="tooltip" title="Lihat Detail">
                            <i class="ti ti-eye me-1"></i> Detail
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
