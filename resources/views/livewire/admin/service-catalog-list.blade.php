<div>
    <style>
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .service-icon-box {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(115, 103, 240, 0.1) 0%, rgba(115, 103, 240, 0.2) 100%);
            color: #7367F0;
        }
        .dark-style .service-icon-box {
            background: rgba(115, 103, 240, 0.15);
            color: #8f85f3;
        }
    </style>
    <!-- Search & Filter Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari layanan..." wire:model.live.debounce.300ms="search" />
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterCategory">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                     <select class="form-select" wire:model.live="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="pending">Menunggu Review</option>
                        <option value="inactive">Nonaktif</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <style>
        .catalog-loading-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 10;
            display: none;
            justify-content: center;
            align-items: center;
            border-radius: 0.5rem;
            backdrop-filter: blur(4px);
        }
        .dark-style .catalog-loading-overlay {
            background: rgba(47, 51, 73, 0.8);
        }
    </style>

    <!-- Catalog Grid Wrapper with Overlay -->
    <div class="position-relative" style="min-height: 300px;">

        <!-- Loading Overlay -->
        <div wire:loading.flex wire:target="search, filterStatus, filterCategory" class="catalog-loading-overlay animate__animated animate__fadeIn">
            <div class="d-flex flex-column align-items-center">
                <div class="spinner-border text-primary mb-2" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="text-muted fw-bold">Memuat data...</span>
            </div>
        </div>

        <!-- Catalog Icons Grid -->
        <div class="row g-4 mb-4">
            @forelse($catalogs as $catalog)
                @include('content.admin.service-catalogs._catalog_card')
            @empty
                <div class="col-12 text-center py-5">
                    <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}" alt="No Data" width="200"
                        class="mb-4 opacity-50">
                    <h5 class="text-muted">Layanan tidak ditemukan.</h5>
                    <p>Coba kata kunci pencarian lain.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $catalogs->links() }}
    </div>
</div>
