<div wire:poll.10s="loadData" class="card h-100">
    <div class="card-header pb-0">
         <h5 class="card-title mb-0">Kategori Terpopuler</h5>
         <small class="text-muted">Berdasarkan Total Request</small>
    </div>
    <div class="card-body d-flex flex-column justify-content-center align-items-center relative">
         @if(count($categoryHitsStats) > 0)
            <div id="categoryHitsChart" wire:ignore style="min-height: 350px; width: 100%;"></div>
         @else
            <div class="d-flex h-100 align-items-center justify-content-center">
                <small class="text-muted">Belum ada aktivitas API</small>
            </div>
         @endif
    </div>

    @script
    <script>
        Livewire.on('update-category-hits-chart', (event) => {
            const data = event.data;

            if (window.categoryHitsChartInstance) {
                window.categoryHitsChartInstance.updateSeries(data.map(c => c.total));
                window.categoryHitsChartInstance.updateOptions({
                    labels: data.map(c => c.name)
                });
            }
        });
    </script>
    @endscript
</div>
