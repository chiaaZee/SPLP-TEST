@extends('layouts/layoutMaster')

@section('title', 'Detail Katalog API')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css',
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
])
<style>
    pre.code-snippet {
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 0.9rem;
        padding: 1rem;
        border-radius: 0.5rem;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    /* Modern Endpoint List Styles */
    table.datatables-endpoints {
        border-collapse: separate;
        border-spacing: 0 1rem; /* Space between rows */
    }
    table.datatables-endpoints thead {
        display: none; /* Hide header */
    }
    table.datatables-endpoints tbody tr {
        background: var(--bs-body-bg); /* Use theme bg (white/dark) */
        box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.3);
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
    }
    table.datatables-endpoints tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(165, 163, 174, 0.45);
        z-index: 1;
    }
    table.datatables-endpoints tbody td {
        border: none !important;
        background-color: transparent !important;
        vertical-align: middle;
    }
    /* Method Badge Styles */
    .method-badge {
        font-weight: 800;
        font-size: 0.8rem;
        padding: 0.5rem 0.8rem;
        border-radius: 0.375rem;
        min-width: 70px;
        text-align: center;
        display: inline-block;
    }
    .method-GET { background-color: rgba(40, 199, 111, 0.15); color: #28c76f; }
    .method-POST { background-color: rgba(255, 159, 67, 0.15); color: #ff9f43; }
    .method-PUT { background-color: rgba(0, 207, 232, 0.15); color: #00cfe8; }
    .method-DELETE { background-color: rgba(234, 84, 85, 0.15); color: #ea5455; }
</style>
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',

        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
        'resources/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js',
        'resources/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js',
        'resources/assets/vendor/libs/apex-charts/apexcharts.js'
    ])
@endsection

@section('page-script')
    @vite(['resources/assets/js/admin-service-catalogs.js'])
    @include('content.admin.service-catalogs._request-access-script')

    <script type="module">
        $(function() {
            var chartData = JSON.parse('{!! json_encode($chartData ?? null) !!}');

            if(chartData) {
                // Usage Chart (Scatter Plot: Date vs Time)
                var chartOptions = {
                    series: chartData.series,
                    chart: {
                        type: 'scatter',
                        height: 350,
                        zoom: { enabled: true, type: 'xy' },
                        toolbar: { show: false }
                    },
                    colors: ['#7367f0', '#28c76f', '#ea5455', '#ff9f43', '#00cfe8'],
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            formatter: function(val) {
                                return new Date(val).toLocaleDateString("id-ID", {day: 'numeric', month: 'short'});
                            }
                        },
                        tooltip: { enabled: false }
                    },
                    yaxis: {
                        title: { text: 'Waktu (Jam)' },
                        min: 0,
                        max: 24,
                        tickAmount: 6,
                        labels: {
                            formatter: function(val) {
                                var h = Math.floor(val);
                                var m = Math.round((val - h) * 60);
                                return (h < 10 ? "0" + h : h) + ":" + (m < 10 ? "0" + m : m);
                            }
                        }
                    },
                    tooltip: {
                        custom: function({series, seriesIndex, dataPointIndex, w}) {
                            var data = w.config.series[seriesIndex].data[dataPointIndex];
                            var timeStr = "";
                            var val = data.y;
                            var h = Math.floor(val);
                            var m = Math.round((val - h) * 60);
                            timeStr = (h < 10 ? "0" + h : h) + ":" + (m < 10 ? "0" + m : m);

                            var userName = data.user ? '<br><span class="text-primary small"><i class="ti ti-user me-1"></i>' + data.user + '</span>' : '';

                            return '<div class="px-3 py-2 border border-primary bg-white rounded shadow-sm">' +
                                '<div class="fw-bold mb-1">' + w.config.series[seriesIndex].name + '</div>' +
                                '<div class="small text-muted mb-1"><i class="ti ti-calendar me-1"></i>' + new Date(data.x).toLocaleDateString("id-ID", {weekday: 'long', day: 'numeric', month: 'short'}) + '</div>' +
                                '<div class="small text-muted"><i class="ti ti-clock me-1"></i>' + timeStr + '</div>' +
                                userName +
                                '</div>';
                        }
                    },
                    grid: {
                        xaxis: { lines: { show: true } },
                        yaxis: { lines: { show: true } },
                    }
                };

                if (document.getElementById('usageChart')) {
                    new ApexCharts(document.getElementById('usageChart'), chartOptions).render();
                }
            }
        });
    </script>
@endsection

@section('content')
    @livewire('admin.service-catalog-detail', [
        'slug' => $catalog->slug,
        'stats' => $stats ?? [],
        'chartData' => $chartData ?? [],
        'hasAccess' => $hasAccess ?? false
    ])

    <!-- Modal Test API -->
    <div class="modal fade" id="modalTestApi" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
          <div class="modal-body">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="text-center mb-4">
              <h3 class="mb-2">Test Result</h3>
              <p class="text-muted">Live response from endpoint.</p>
            </div>

            <div class="border rounded p-3 bg-label-secondary">
                 <!-- Header: Status & Info -->
                 <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                    <div>
                        <span id="test_status" class="badge bg-secondary">-</span>
                        <span id="test_method" class="fw-bold ms-2"></span>
                    </div>
                    <div class="text-muted small">
                        <i class="ti ti-clock me-1"></i> <span id="test_duration">-</span>
                    </div>
                 </div>

                 <!-- Body: JSON content -->
                 <div class="code-container" style="max-height: 400px; overflow-y: auto;">
                     <pre id="test_body" class="m-0 bg-transparent text-body" style="white-space: pre-wrap; font-family: 'Consolas', monospace; font-size: 0.85rem;">Waiting for request...</pre>
                 </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Modal Documentation (Modern Split Layout) -->
    <div class="modal fade" id="modalDocs" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered modal-simple">
        <div class="modal-content p-0 overflow-hidden">
          <div class="modal-body p-0">
             <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>

             <div class="row g-0 h-100">
                <!-- Left Panel: Context -->
                <div class="col-lg-5 border-end bg-body p-5">
                    <div class="mb-4">
                         <span class="badge bg-label-primary mb-2">API Documentation</span>
                         <h3 class="mb-2">Integration Guide</h3>
                         <p class="text-muted">Implementasikan endpoint ini ke dalam aplikasi Anda.</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase small fw-bold text-muted">Endpoint URL</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text" id="doc_method_badge">GET</span>
                            <input type="text" class="form-control" id="doc_url_input" readonly value="">
                            <button class="btn btn-outline-primary" type="button" id="btnCopyUrl"><i class="ti ti-copy"></i></button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase small fw-bold text-muted">Headers Requirements</label>
                        <ul class="list-group list-group-flush border rounded-3 text-start">
                            <li class="list-group-item d-flex justify-content-between align-items-center small">
                                <code>Authorization</code>
                                <span class="text-muted">Bearer &lt;token&gt;</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center small">
                                <code>Accept</code>
                                <span class="text-muted">application/json</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Description Box -->
                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                        <i class="ti ti-info-circle me-2"></i>
                        <div class="small">
                             Pastikan Token API Anda valid dan memiliki hak akses ke layanan ini.
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Code Window -->
                <div class="col-lg-7 bg-dark d-flex flex-column">
                    <!-- MacOS-like Header -->
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom border-secondary">
                        <div class="d-flex gap-2">
                            <div class="rounded-circle bg-danger" style="width: 10px; height: 10px;"></div>
                            <div class="rounded-circle bg-warning" style="width: 10px; height: 10px;"></div>
                            <div class="rounded-circle bg-success" style="width: 10px; height: 10px;"></div>
                        </div>
                        <!-- Language Tabs -->
                        <div class="nav nav-pills nav-code-tabs" role="tablist">
                             <button class="btn btn-sm btn-text-secondary text-white active" data-bs-toggle="tab" data-bs-target="#code_curl_tab">cURL</button>
                             <button class="btn btn-sm btn-text-secondary text-white" data-bs-toggle="tab" data-bs-target="#code_php_tab">PHP</button>
                             <button class="btn btn-sm btn-text-secondary text-white" data-bs-toggle="tab" data-bs-target="#code_js_tab">NodeJS</button>
                             <button class="btn btn-sm btn-text-secondary text-white" data-bs-toggle="tab" data-bs-target="#code_android_tab">Kotlin</button>
                        </div>
                        <button class="btn btn-sm btn-icon btn-text-secondary text-white" id="btnCopyCode" title="Copy Code"><i class="ti ti-copy"></i></button>
                    </div>

                    <!-- Code Area -->
                    <div class="flex-grow-1 p-4 overflow-auto position-relative tab-content" style="max-height: 500px;">
                         <div class="tab-pane fade show active" id="code_curl_tab">
                            <pre class="m-0 bg-transparent text-white" style="font-family: 'Fira Code', 'Consolas', monospace; font-size: 0.9rem;" id="doc_code_curl">// Loading...</pre>
                         </div>
                         <div class="tab-pane fade" id="code_php_tab">
                             <pre class="m-0 bg-transparent text-white" style="font-family: 'Fira Code', 'Consolas', monospace; font-size: 0.9rem;" id="doc_code_php">// Loading...</pre>
                         </div>
                         <div class="tab-pane fade" id="code_js_tab">
                             <pre class="m-0 bg-transparent text-white" style="font-family: 'Fira Code', 'Consolas', monospace; font-size: 0.9rem;" id="doc_code_js">// Loading...</pre>
                         </div>
                         <div class="tab-pane fade" id="code_android_tab">
                             <pre class="m-0 bg-transparent text-white" style="font-family: 'Fira Code', 'Consolas', monospace; font-size: 0.9rem;" id="doc_code_android">// Loading...</pre>
                         </div>
                    </div>
                </div>
             </div>
          </div>
        </div>
      </div>
    </div>
@endsection
