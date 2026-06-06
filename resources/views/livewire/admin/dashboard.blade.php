
<div>
    @push('page-script')
    @vite([
        'resources/assets/vendor/libs/apex-charts/apexcharts.js'
    ])
    <script type="module">
        $(function () {
             // 1. Traffic Chart (Line) - Real Data from Livewire
             const trafficEl = document.querySelector("#trafficChart");
             if (trafficEl) {
                 // Get Dynamic Data from Livewire
                 const rawData = @json($dailyTraffic);

                 // Check for Dark Mode
                 const isDark = document.documentElement.classList.contains('dark-style');

                 const options = {
                    series: [{
                        name: 'Hits',
                        data: rawData.hits
                    }, {
                        name: 'Errors',
                        data: rawData.errors
                    }],
                    chart: {
                        type: 'area', // EXACTLY matching the reference
                        height: 350,
                        toolbar: { show: false },
                        foreColor: isDark ? '#d0d2d6' : '#6f6b7d'
                    },
                    colors: ['#7367f0', '#ea5455'],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    grid: {
                        borderColor: isDark ? '#444' : '#e7e7e7',
                    },
                    xaxis: {
                        categories: rawData.categories,
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            formatter: function (val) {
                                return val.toFixed(0);
                            }
                        }
                    },
                    fill: { opacity: 0.3 }
                 };
                 new ApexCharts(trafficEl, options).render();
             }
        });
    </script>
    @endpush

    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
             <div class="card bg-primary text-white overflow-hidden"
                  style="background: linear-gradient(45deg, #7367F0, #9e95f5);">
                  <div class="card-body d-flex justify-content-between align-items-center">
                      <div>
                          <h4 class="text-white mb-0">Selamat Datang, Admin! 👋</h4>
                          <p class="mb-0 op-8">
                              Pantau ekosistem SPLPD Lumajang secara real-time.
                          </p>
                      </div>
                      <div class="d-none d-md-block">
                          <i class="ti ti-layout-dashboard ti-xl opacity-50" style="font-size: 4rem;"></i>
                      </div>
                  </div>
             </div>
        </div>
    </div>

    <!-- Stats Cards (V2 Structure) -->
    <div class="row mb-4 g-3">
        <!-- Total Hits API (All Time) -->
        <div class="col-md-4">
            <div class="card h-100 bg-label-primary">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar me-3">
                        <span class="avatar-initial rounded bg-primary text-white">
                            <i class="ti ti-chart-bar fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">{{ number_format($totalTransaksi) }}</h4>
                        <small class="text-muted">Total Hits (All Time)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Latency (All Time) -->
        <div class="col-md-4">
            <div class="card h-100 bg-label-warning">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar me-3">
                        <span class="avatar-initial rounded bg-warning text-white">
                            <i class="ti ti-bolt fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-warning">{{ $avgLatency }} ms</h4>
                        <small class="text-muted">Avg Latency (All Time)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Rate (All Time/Calculated) -->
        <div class="col-md-4">
            <div class="card h-100 bg-label-success">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar me-3">
                        <span class="avatar-initial rounded bg-success text-white">
                            <i class="ti ti-thumb-up fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-success">{{ 100 - $errorRate }}%</h4>
                        <small class="text-muted">Success Rate</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Traffic Chart Only -->
        <div class="col-lg-12 mb-4 mb-lg-0">
            <div class="card h-100">
                <div class="card-header pb-0 d-flex justify-content-between">
                     <div>
                        <h5 class="card-title mb-0">Tren Akses API</h5>
                        <small class="text-muted">7 Hari Terakhir</small>
                     </div>
                </div>
                <!-- V2 Layout: Full Width Chart -->
                <div class="card-body">
                    <div id="trafficChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
