@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/apex-charts/apexcharts.js'
    ])
@endsection

@section('page-style')
<style>
    /* Custom Scrollbar for Chart */
    .chart-scroll-wrapper::-webkit-scrollbar {
        height: 8px; /* Horizontal scrollbar height */
    }
    .chart-scroll-wrapper::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.05);
        border-radius: 4px;
    }
    .chart-scroll-wrapper::-webkit-scrollbar-thumb {
        background: rgba(115, 103, 240, 0.4); /* Primary color semi-transparent */
        border-radius: 4px;
        transition: background 0.3s;
    }
    .chart-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(115, 103, 240, 0.8);
    }
    .dark-style .chart-scroll-wrapper::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.05);
    }
    .dark-style .chart-scroll-wrapper::-webkit-scrollbar-thumb {
        background: rgba(115, 103, 240, 0.6);
    }
</style>
@endsection

@section('page-script')
<script type="module">
    $(function () {
        const rawData = @json($dailyTraffic);
        const categoryData = @json($categoryStats);
        const categoryHitsData = @json($categoryHitsStats);
        const topStatsData = @json($topStats);
        const isDark = document.documentElement.classList.contains('dark-style');

        // Counter Animation
        function animateCounters() {
            const counters = document.querySelectorAll('.counter-value');
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target');
                const speed = 2000;
                const inc = target / (speed / 16); // 60fps approx

                let count = 0;
                const updateCount = () => {
                    count += inc;
                    if (count < target) {
                        counter.innerText = Math.ceil(count);
                        requestAnimationFrame(updateCount);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            });
        }
        // Run after short delay to ensure rendering
        setTimeout(animateCounters, 500);

        // Common Donut Options
        // Common Donut Options
        function getDonutOptions(seriesData, labelsData) {
            return {
                series: seriesData,
                labels: labelsData,
                chart: {
                    type: 'donut',
                    height: 350,
                    toolbar: { show: false },
                    foreColor: isDark ? '#d0d2d6' : '#6f6b7d',
                    fontFamily: 'Public Sans, sans-serif'
                },
                // Modern Pastel Palette (Consistent with TopStats)
                colors: [
                    '#7367F0', '#00CFE8', '#28C76F', '#FF9F43', '#EA5455',
                    '#A8DADC', '#457B9D', '#1D3557', '#F4A261', '#E76F51'
                ],
                stroke: {
                    lineCap: 'round',
                    width: 4,
                    colors: [isDark ? '#2f3349' : '#ffffff'] // Adapt stroke to background
                },
                dataLabels: {
                    enabled: false, // Clean look
                    formatter: function (val) { return parseInt(val) + '%' }
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    fontSize: '13px',
                    markers: {
                        width: 10,
                        height: 10,
                        radius: 12,
                        offsetX: -4
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 5
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%', // Thinner ring
                            labels: {
                                show: true,
                                name: {
                                    fontSize: '0.9rem',
                                    fontFamily: 'Public Sans',
                                    offsetY: 25 // Move label down more
                                },
                                value: {
                                    fontSize: '1.5rem',
                                    color: isDark ? '#d0d2d6' : '#5d596c',
                                    fontFamily: 'Public Sans',
                                    fontWeight: 600,
                                    offsetY: -15, // Move value up more
                                    formatter: function (val) { return parseInt(val) }
                                },
                                total: {
                                    show: true,
                                    fontSize: '0.9rem',
                                    color: isDark ? '#a5a3ae' : '#6f6b7d',
                                    label: 'Total',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                    }
                                }
                            }
                        }
                    }
                }
            };
        }

        // Run after animation delay to ensure containers have dimensions
        setTimeout(() => {
            // 1. Traffic Chart (Area)
            const trafficEl = document.querySelector("#trafficChart");
            if (trafficEl) {
                const options = {
                    series: [{
                        name: 'Hits',
                        data: rawData.hits
                    }, {
                        name: 'Errors',
                        data: rawData.errors
                    }],
                    chart: {
                        type: 'area',
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
                window.trafficChartInstance = new ApexCharts(trafficEl, options);
                window.trafficChartInstance.render();
            }

            // 2. Category Chart (Donut - Counts)
            const categoryEl = document.querySelector("#categoryChart");
            if (categoryEl && categoryData.length > 0) {
                const opts = getDonutOptions(
                    categoryData.map(c => c.total),
                    categoryData.map(c => c.name)
                );
                window.categoryChartInstance = new ApexCharts(categoryEl, opts);
                window.categoryChartInstance.render();
            }

            // 3. Top Stats Chart (Bar - Vertical with Horizontal Scroll)
            const topStatsEl = document.querySelector("#topStatsChart");

            if (topStatsEl && topStatsData.data.length > 0) {
                // Dynamic Width Calculation: 100% min, or 50px per bar if more content
                const minWidth = "100%";
                // Increased spacing per bar for cleaner look
                const calculatedWidth = Math.max(topStatsEl.clientWidth, topStatsData.data.length * 60) + "px";

                topStatsEl.style.width = calculatedWidth;

                // Initialize Map
                window.topStatsMap = topStatsData.names || {};

                const topOptions = {
                    series: [{
                        name: 'Total Request',
                        data: topStatsData.data
                    }],
                    chart: {
                        type: 'bar',
                        height: 400,
                        width: '100%',
                        toolbar: { show: false },
                        foreColor: isDark ? '#d0d2d6' : '#6f6b7d',
                        fontFamily: 'Public Sans, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 30, // Max for capsule
                            endingShape: 'rounded', // Fallback for older versions
                            horizontal: false,
                            columnWidth: '40%',
                            distributed: true,
                            dataLabels: {
                                position: 'top'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -25,
                        style: {
                            fontSize: '12px',
                            colors: [isDark ? '#d0d2d6' : '#6f6b7d'],
                            fontFamily: 'Public Sans, sans-serif',
                            fontWeight: 600
                        }
                    },
                    colors: [
                        '#7367F0', '#00CFE8', '#28C76F', '#FF9F43', '#EA5455',
                        '#A8DADC', '#457B9D', '#1D3557', '#F4A261', '#E76F51'
                    ],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.5,
                            gradientToColors: undefined,
                            inverseColors: false,
                            opacityFrom: 0.9,
                            opacityTo: 0.6,
                            stops: [0, 100]
                        }
                    },
                    grid: {
                        show: true,
                        borderColor: isDark ? '#444' : '#f0f0f0',
                        strokeDashArray: 4,
                        padding: { top: 20, right: 20, bottom: 50, left: 20 }
                    },
                    xaxis: {
                        categories: topStatsData.categories, // Now Codes
                        labels: {
                            rotate: -45,
                            rotateAlways: false, // Auto rotate
                            style: {
                                fontSize: '11px',
                                fontWeight: 500
                            },
                            trim: true,
                            maxHeight: 120
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        show: false
                    },
                    legend: { show: false },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light',
                        y: {
                            formatter: function (val) {
                                return val + " Hits"
                            }
                        },
                        x: {
                            formatter: function(val) {
                                // Lookup full name from map, fallback to code
                                return (window.topStatsMap && window.topStatsMap[val]) ? window.topStatsMap[val] : val;
                            }
                        },
                        marker: { show: false } // Modern tooltip
                    }
                };
                window.topStatsChartInstance = new ApexCharts(topStatsEl, topOptions);
                window.topStatsChartInstance.render();
            }

            // 4. Category Hits Chart (Donut - Hits)
            const categoryHitsEl = document.querySelector("#categoryHitsChart");
            if (categoryHitsEl && categoryHitsData.length > 0) {
                const opts = getDonutOptions(
                    categoryHitsData.map(c => c.total),
                    categoryHitsData.map(c => c.name)
                );
                window.categoryHitsChartInstance = new ApexCharts(categoryHitsEl, opts);
                window.categoryHitsChartInstance.render();
            }
        }, 800); // 800ms delay to wait for Animate.css (0.6s) to finish
    });
</script>
@endsection

@section('content')
<!-- Announcements Partial -->
@include('content.dashboard.announcement-display')

<!-- Hero Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white overflow-hidden"
             style="background: linear-gradient(45deg, #7367F0, #9e95f5);">
             <div class="card-body d-flex justify-content-between align-items-center">
                 <div>
                     <h4 class="text-white mb-0 animate__animated animate__fadeInLeft">Selamat Datang, {{ auth()->user()->name }}! 👋</h4>
                     <p class="mb-0 op-8 animate__animated animate__fadeInLeft" style="animation-delay: 0.1s">
                         Pantau aktivitas akses API {{ $isAdmin ? 'ekosistem SPLPD' : 'aplikasi Anda' }} secara real-time.
                     </p>
                 </div>
                 <div class="d-none d-md-block animate__animated animate__fadeInRight">
                     <i class="ti ti-layout-dashboard ti-xl opacity-50" style="font-size: 4rem;"></i>
                 </div>
             </div>
        </div>
    </div>
</div>

<!-- Stats Rows (V2 Style) -->
<livewire:dashboard.stats-cards />

<!-- REMOVED OLD ADMIN STATS & CHART ROW (Clean up) -->
<!-- Chart Row -->
<div class="row mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.5s">
    <!-- Traffic Chart (Wide) -->
    <div class="col-md-8 mb-4 mb-md-0">
        <livewire:dashboard.traffic-chart />
    </div>

    <!-- Category Chart (Narrow) -->
    <div class="col-md-4">
        <livewire:dashboard.popular-category-chart />
    </div>
</div>

<!-- Row 2: Top Stats & Hits Distribution -->
<div class="row mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.6s">
    <!-- Top Stats (Bar Chart - Horizontal Scroll) -->
    <div class="col-md-8 mb-4 mb-md-0">
        <livewire:dashboard.top-stats-chart />
    </div>

    <!-- Category HITS Chart (Narrow) -->
    <div class="col-md-4">
        <livewire:dashboard.category-hits-chart />
    </div>
</div>

<!-- Recent Activity Row -->
<div class="row animate__animated animate__fadeInUp" style="animation-delay: 0.7s">
    <div class="col-12">
        <livewire:dashboard.recent-activity />
    </div>
</div>
@endsection

