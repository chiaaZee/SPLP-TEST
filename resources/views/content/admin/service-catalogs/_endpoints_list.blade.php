<style>
    .endpoint-card {
        transition: all 0.3s ease-in-out;
        border: 1px solid transparent;
        /* Prepare for border transition */
    }

    .endpoint-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        border-color: #7367f0 !important;
        /* Primary color border */
        background-color: #fcfcff;
    }
</style>
@forelse($endpoints as $endpoint)
    @include('content.admin.service-catalogs._endpoint_item')
@empty
    <div class="col-12 text-center py-5">
        <div class="d-flex flex-column align-items-center justify-content-center">
            <div class="bg-label-primary p-3 rounded-circle mb-3">
                <i class="ti ti-database-off ti-xl"></i>
            </div>
            <h4 class="text-muted mb-1">Belum ada Endpoint</h4>
            <p class="text-muted">Tambahkan endpoint baru untuk memulai.</p>
        </div>
    </div>
@endforelse

<div class="d-flex justify-content-center mt-4">
    {{ $endpoints->withQueryString()->links('pagination::bootstrap-5') }}
</div>
