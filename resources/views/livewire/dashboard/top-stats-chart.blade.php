<div wire:poll.10s="loadData" class="card h-100">
    <div class="card-header pb-0">
         <h5 class="card-title mb-0">
            {{ $isAdmin ? 'Top 10 Perangkat Daerah Teraktif' : 'Top 10 Layanan Terpopuler' }}
         </h5>
         <small class="text-muted">Total Hits (Semua Endpoint)</small>
    </div>
    <div class="card-body">
         @if(count($topStats['data']) > 0)
            <!-- Compact Container -->
            <div id="topStatsChart" wire:ignore></div>
         @else
            <div class="d-flex h-100 align-items-center justify-content-center p-5">
                <small class="text-muted">Belum ada data</small>
            </div>
         @endif
    </div>

    @script
    <script>
        Livewire.on('update-top-stats-chart', (event) => {
            const data = event.data;

            if (window.topStatsChartInstance) {
                // Update names map for tooltip
                if (data.names) {
                    window.topStatsMap = data.names;
                }

                window.topStatsChartInstance.updateSeries([{
                    name: 'Total Request',
                    data: data.data
                }]);

                window.topStatsChartInstance.updateOptions({
                    xaxis: { categories: data.categories },
                    // Recalculate width if categories grow
                     chart: {
                        width: Math.max(document.querySelector("#topStatsChart").clientWidth, data.data.length * 60) + "px"
                    }
                });
            }
        });
    </script>
    @endscript
</div>
