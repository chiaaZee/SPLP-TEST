<div wire:poll.10s="loadData" class="card h-100">
    <div class="card-header pb-0">
         <h5 class="card-title mb-0">
             {{ $isAdmin ? 'Distribusi Kategori' : 'Kategori Disetujui' }}
         </h5>
         <small class="text-muted">{{ $isAdmin ? 'Semua Katalog' : 'Katalog Disetujui' }}</small>
    </div>
    <div class="card-body d-flex flex-column justify-content-center align-items-center relative">
         @if(count($categoryStats) > 0)
            <div id="categoryChart" wire:ignore style="min-height: 350px; width: 100%;"></div>
         @else
            <div class="d-flex h-100 align-items-center justify-content-center">
                <small class="text-muted">Belum ada data kategori</small>
            </div>
         @endif
    </div>

    @script
    <script>
        Livewire.on('update-popular-category-chart', (event) => {
            const data = event.data;

            if (window.categoryChartInstance) {
                window.categoryChartInstance.updateSeries(data.map(c => c.total));
                window.categoryChartInstance.updateOptions({
                    labels: data.map(c => c.name)
                });
            }
        });
    </script>
    @endscript
</div>
