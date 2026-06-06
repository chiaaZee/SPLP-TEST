
@section('title', 'Kelola API Keys (HMAC)')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
        'resources/assets/vendor/libs/select2/select2.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
        'resources/assets/vendor/libs/select2/select2.js'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
        'resources/assets/vendor/libs/select2/select2.js'
    ])
@endsection

<div>
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">API Keys (HMAC)</h3>
                            <p class="text-white opacity-75 mb-0">Standar Otentikasi API Berbasis HMAC Signature dengan Token Binding.</p>
                        </div>
                        <i class="ti ti-shield-lock text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_status">
                    <h5 class="mb-0">Daftar API Keys</h5>
                </div>
                <div class="col-md-8">
                    <div class="d-flex align-items-center justify-content-md-end gap-2">
                        <!-- Search -->
                        <div class="input-group input-group-merge" style="max-width: 250px;">
                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari Client / Key..." aria-label="Search...">
                        </div>

                        <!-- Create Button -->
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createKeyModal">
                            <i class="ti ti-plus me-1"></i> Buat Key Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        @if($isAdmin)
                            <th>Pemilik</th>
                            <th>Perangkat Daerah</th>
                        @endif
                        <th>Nama Aplikasi</th>
                        <th>Service Binding</th>
                        <th>Client ID</th>
                        <th>Status</th>
                        <th>Dibuat Pada</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($clients as $client)
                        <tr wire:key="client-{{ $client->id }}">
                            <td>{{ $loop->iteration + ($clients->currentPage() - 1) * $clients->perPage() }}</td>
                            @if($isAdmin)
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $client->user->name ?? '-' }}</span>
                                            <small class="text-muted">{{ $client->user->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        // Display Logic: If mapped SKPD code exists, try to show that Agency Name
                                        $displayAgency = null;
                                        if (!empty($client->mapping_config['skpd_code'])) {
                                            $mappedCode = $client->mapping_config['skpd_code'];
                                            // Ideally we should eagerly load this or map it.
                                            // For performance in loop, checking if we can get it from cached list or relationship?
                                            // Since we don't have direct relationship to 'Target Agency' in model (it's JSON),
                                            // we might need a helper lookup or just show the code.
                                            // Trying to find in loaded agencies if possible (but pagination makes it hard).
                                            // Let's rely on finding by code if effective, or just show code + name if user has it.
                                            // If the key is owned by Admin but mapped to Dinas X, we show Dinas X.

                                            // Temporary: direct lookup (might be N+1 but acceptable for small admin usage)
                                            // Optimization: We could join in query but JSON extract is heavy.
                                            $mappedAgency = \App\Models\Agency::where('code', $mappedCode)->first();
                                            if ($mappedAgency) {
                                                $displayAgency = $mappedAgency;
                                            }
                                        }

                                        // Fallback to Owner's Agency
                                        if (!$displayAgency && $client->user && $client->user->agency) {
                                            $displayAgency = $client->user->agency;
                                        }
                                    @endphp

                                    @if($displayAgency)
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $displayAgency->name }}</span>
                                            <small class="text-muted">{{ $displayAgency->code }}</small>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                            <td><span class="fw-medium">{{ $client->name }}</span></td>
                            <td>
                                @if($client->serviceCatalog)
                                    <span class="badge bg-label-primary">{{ $client->serviceCatalog->name }}</span>
                                @else
                                    <span class="badge bg-label-secondary">Global / All Access</span>
                                @endif
                            </td>
                            <td class="font-monospace text-muted">{{ $client->api_key }}</td>
                            <td>
                                @if($client->status == 'active')
                                    <span class="badge bg-label-success">Active</span>
                                @elseif($client->status == 'inactive' || $client->status == 'suspended')
                                    <span class="badge bg-label-warning">Suspended</span>
                                @else
                                    <span class="badge bg-label-danger">{{ $client->status }}</span>
                                @endif
                            </td>
                            <td>{{ $client->created_at->format('d M Y H:i') }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" wire:click="toggleStatus({{ $client->id }})">
                                            @if($client->status == 'active')
                                                <i class="ti ti-ban me-1 text-warning"></i> Suspend
                                            @else
                                                <i class="ti ti-check me-1 text-success"></i> Activate
                                            @endif
                                        </button>
                                        <button class="dropdown-item {{ $client->status == 'deleted' ? 'disabled' : '' }}" wire:click="confirmDelete({{ $client->id }})">
                                            <i class="ti ti-trash me-1 text-danger"></i> Revoke Key
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 9 : 7 }}" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ti ti-key text-muted opacity-50 mb-3" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted">Tidak ada API Key ditemukan</h5>
                                    <p class="text-muted mb-0">Klik "Buat Key Baru" untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">
            {{ $clients->links() }}
        </div>
    </div>

    <!-- Create Key Modal -->
    <div class="modal fade" id="createKeyModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat API Key Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label class="form-label">Nama Aplikasi / Keterangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="contoh: Web Profil Desa">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Target Layanan (Optional)</label>
                            <select class="form-select @error('service_catalog_id') is-invalid @enderror" wire:model.live="service_catalog_id">
                                <option value="">Global / Tanpa Binding</option>
                                @foreach($formServiceCatalogs as $catalog)
                                    <option value="{{ $catalog->id }}">
                                        {{ $catalog->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Mengikat Key hanya untuk layanan tertentu.</div>
                            @error('service_catalog_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if($canCustomizeMapping)
                            <div class="mb-3 animate__animated animate__fadeIn">
                                <label class="form-label">Target Perangkat Daerah (Pilih untuk Binding SKPD)</label>
                                <div wire:ignore>
                                    <select id="agencySelect" class="select2 form-select" data-placeholder="Pilih Perangkat Daerah...">
                                        <option></option>
                                        @foreach($formAgencies as $agency)
                                            <option value="{{ $agency->id }}">{{ $agency->name }} ({{ $agency->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 animate__animated animate__fadeIn">
                                <label class="form-label">SKPD Code Binding (Auto-Filled)</label>
                                <input type="text" class="form-control @error('skpd_code') is-invalid @enderror" wire:model="skpd_code" readonly placeholder="Otomatis terisi dari perangkat daerah..." style="background-color: #fefeff;">
                                <div class="form-text text-primary">
                                    <i class="ti ti-info-circle me-1"></i> Code ini akan digunakan sebagai parameter `skpd_code` otomatis.
                                </div>
                                @error('skpd_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @else
                            @if($requiresMapping)
                                <div class="alert alert-warning d-flex align-items-center p-2 mb-3 small" role="alert">
                                    <i class="ti ti-alert-triangle me-2"></i>
                                    <div>
                                        Key ini akan otomatis terikat dengan Kode SKPD perangkat daerah Anda.
                                    </div>
                                </div>
                            @endif
                        @endif

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="store" wire:loading.attr="disabled">
                        <span wire:loading.remove>Generate Key</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span> Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


@script
<script>
    // Initialize Select2
    // We need to re-init on livewire updates if not handled carefully, or use wire:ignore
    // Using wire:ignore on the div wrapper as above is good practice.

    $(document).ready(function() {
        initSelect2();
    });

    document.addEventListener('livewire:navigated', () => {
        initSelect2();
    });

    function initSelect2() {
        var selectEl = $('#agencySelect');
        if (selectEl.length) {
            selectEl.select2({
                dropdownParent: $('#createKeyModal'),
                placeholder: 'Pilih Perangkat Daerah...',
                allowClear: true
            });

            // Bind change event to Livewire
            selectEl.on('change', function (e) {
                var data = $(this).val();
                $wire.set('selected_agency_id', data);
            });
        }
    }

    // Close modal on event
    $wire.on('close-create-modal', () => {
        $('#createKeyModal').modal('hide');
        // Reset select2
        $('#agencySelect').val(null).trigger('change');
    });

    // Show Credentials SweetAlert
    $wire.on('credential-created', (event) => {
        const creds = event.credential; // Access directly from event object in Livewire 3

        // Wait a bit for modal to close fully
        setTimeout(() => {
            Swal.fire({
                title: 'Credential Generated!',
                html: `
                    <div class="text-start">
                        <label class="small text-muted">Client ID</label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" value="${creds.api_key}" readonly>
                        </div>
                        <label class="small text-muted">Secret Key (HANYA MUNCUL SEKALI)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="${creds.secret_key}" id="secret-key-copy" readonly>
                            <button class="btn btn-outline-primary" onclick="copySecret()">Copy</button>
                        </div>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Saya Sudah Menyimpan Secret Key',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        }, 300);
    });

    window.copySecret = function () {
        var copyText = document.getElementById("secret-key-copy");
        copyText.select();
        navigator.clipboard.writeText(copyText.value);
    };
</script>
@endscript
