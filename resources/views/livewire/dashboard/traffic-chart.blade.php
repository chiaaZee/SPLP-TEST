<div wire:poll.10s="loadData" class="card h-100">
    <div class="card-header pb-0 d-flex justify-content-between">
         <div>
            <h5 class="card-title mb-0">Tren Akses API</h5>
            <small class="text-muted">7 Hari Terakhir</small>
         </div>
         <div>
            <span wire:loading class="spinner-border spinner-border-sm text-primary" role="status"></span>
         </div>
    </div>
    <div class="card-body">
        <div id="trafficChart" wire:ignore style="min-height: 350px;"></div>
    </div>

    @script
    <script>
        Livewire.on('update-traffic-chart', (event) => {
            const data = event.data;
            const isDark = document.documentElement.classList.contains('dark-style');

            // Re-use chart update logic or init logic
            if (window.trafficChartInstance) {
                window.trafficChartInstance.updateSeries([{
                    name: 'Hits',
                    data: data.hits
                }, {
                    name: 'Errors',
                    data: data.errors
                }]);

                // Also update categories if days change (e.g. crossing midnight)
                window.trafficChartInstance.updateOptions({
                    xaxis: { categories: data.categories }
                });
            } else {
                // Initial Init (Fallback if not initialized in main script)
                // However, optimal way is to let main script init it, and this just updates it.
                // We will rely on window.trafficChartInstance being set in main dashboard script.
            }
        });
    </script>
    @endscript
</div>
