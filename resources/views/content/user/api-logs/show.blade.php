@extends('layouts/layoutMaster')

@section('title', 'Monitor: ' . $catalog->name)

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
    ])
    <style>
        /* Custom Scrollbar */
        .log-scroll-area::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .log-scroll-area::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }
        .log-scroll-area::-webkit-scrollbar-thumb {
            background: #bbb;
            border-radius: 4px;
        }
        .log-scroll-area::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        /* Dark Mode Scrollbar Overrides */
        .dark-style .log-scroll-area::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }
        .dark-style .log-scroll-area::-webkit-scrollbar-thumb {
            background: #666;
        }
        .dark-style .log-scroll-area::-webkit-scrollbar-thumb:hover {
            background: #888;
        }

        /* JSON Syntax Highlighting (Always Dark Theme for Code) */
        .json-key { color: #9cdcfe; }      /* Light Blue */
        .json-string { color: #ce9178; }   /* Orange/Brown */
        .json-number { color: #b5cea8; }   /* Light Green */
        .json-boolean { color: #569cd6; }  /* Blue */
        .json-null { color: #569cd6; }     /* Blue */

        /* Modal Layout */
        .modal-api-logs .modal-body {
            height: 75vh;
            overflow: hidden;
            background-color: #fff; /* Light Mode Default */
        }
        .dark-style .modal-api-logs .modal-body {
            background-color: #2f3349; /* Dark Mode Default */
        }

        /* Sidebar Navigation */
        .log-sidebar {
            background-color: #f8f9fa;
            border-right: 1px solid #e7e7e8;
        }
        .dark-style .log-sidebar {
            background-color: #2f3349; /* Match Dark Modal BG */
            border-right: 1px solid #444;
        }

        .nav-pills .nav-link {
            border-radius: 0;
            padding: 1rem 1.25rem;
            color: #5d596c;
            font-weight: 500;
        }
        .dark-style .nav-pills .nav-link {
            color: #b6bee3;
        }

        .nav-pills .nav-link.active {
            background-color: #fff !important;
            color: #7367f0 !important;
            border-left: 3px solid #7367f0;
            box-shadow: none !important;
        }
        .dark-style .nav-pills .nav-link.active {
            background-color: #3b405c !important; /* Slightly lighter than sidebar */
            color: #7367f0 !important;
        }

        .nav-pills .nav-link:hover {
             background-color: rgba(0,0,0,0.02);
        }
        .dark-style .nav-pills .nav-link:hover {
             background-color: rgba(255,255,255,0.02);
        }

        /* Code Content Wrapper */
        .log-content-wrapper {
            background-color: #1e1e1e !important; /* Strictly Force Dark */
            color: #d4d4d4 !important;
        }

        pre {
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            white-space: pre-wrap !important; /* Critical for wrapping */
            word-wrap: break-word;
            color: #d4d4d4;
        }
    </style>
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'resources/assets/vendor/libs/apex-charts/apexcharts.js'
    ])
@endsection

@section('content')
<!-- Hero Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card text-white h-100 overflow-hidden"
            style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-white fw-bold mb-1">Monitor: {{ $catalog->name }}</h3>
                        <p class="text-white opacity-75 mb-0">
                             Detail aktivitas, error log, dan performa layanan.
                        </p>
                    </div>
                    <i class="ti ti-activity-heartbeat text-white opacity-25" style="font-size: 5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="row">
    <div class="col-12">
        <div class="nav-align-top mb-4">
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-overview" aria-controls="navs-overview" aria-selected="true">
                        <i class="ti ti-chart-line me-1"></i> Overview
                    </button>
                </li>
                @if(auth()->user()->hasRole('admin'))
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-consumers" aria-controls="navs-consumers" aria-selected="false">
                        <i class="ti ti-users me-1"></i> Top Konsumen
                    </button>
                </li>
                @endif
                <li class="nav-item">
                     <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-logs" aria-controls="navs-logs" aria-selected="false">
                        <i class="ti ti-list-details me-1"></i> Log Aktivitas
                    </button>
                </li>
            </ul>
             <div class="tab-content">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="navs-overview" role="tabpanel">
                      <!-- Statistics Summary Row -->
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
                                          <h4 class="mb-0 fw-bold text-primary">{{ number_format($totalHits) }}</h4>
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

                          <!-- Success Rate (7 Days) -->
                          <div class="col-md-4">
                              <div class="card h-100 bg-label-success">
                                  <div class="card-body d-flex align-items-center">
                                      <div class="avatar me-3">
                                          <span class="avatar-initial rounded bg-success text-white">
                                              <i class="ti ti-thumb-up fs-3"></i>
                                          </span>
                                      </div>
                                      <div>
                                          <h4 class="mb-0 fw-bold text-success">{{ $successRate7d }}%</h4>
                                          <small class="text-muted">Success Rate (24 Jam Terakhir)</small>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-12">
                               <h5 class="card-title mb-3">Traffic & Errors (7 Hari Terakhir)</h5>
                               <div id="trafficChart"></div>
                          </div>
                      </div>
                </div>

                <!-- Consumers Tab (Admin Only) -->
                @if(auth()->user()->hasRole('admin'))
                <div class="tab-pane fade" id="navs-consumers" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Perangkat Daerah</th>
                                    <th>Total Hits</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topConsumers as $consumer)
                                @php
                                    $agencyName = '-';
                                    if ($consumer->client && !empty($consumer->client->mapping_config['skpd_code'])) {
                                        $code = $consumer->client->mapping_config['skpd_code'];
                                        $mappedAgency = \App\Models\Agency::where('code', $code)->first();
                                        if ($mappedAgency) {
                                            $agencyName = $mappedAgency->name . ' (Mapped)';
                                        }
                                    }

                                    if ($agencyName === '-' && $consumer->user && $consumer->user->agency) {
                                        $agencyName = $consumer->user->agency->name;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ $consumer->user ? substr($consumer->user->name, 0, 2) : '?' }}
                                                </span>
                                            </div>
                                            <span class="fw-medium">{{ $consumer->user ? $consumer->user->name : 'Unknown User' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $agencyName }}</td>
                                    <td><span class="badge bg-label-info">{{ number_format($consumer->total) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">Belum ada data konsumen.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Logs Tab -->
                <div class="tab-pane fade" id="navs-logs" role="tabpanel">
                    <!-- Filters -->
                    <div class="card-header p-0 mb-3 d-flex flex-wrap gap-3">
                         <select id="filter-endpoint" class="form-select w-auto">
                            <option value="">Semua Endpoint</option>
                         </select>
                         <select id="filter-status" class="form-select w-auto">
                            <option value="">Semua Status</option>
                            <option value="2xx">2xx (Success)</option>
                            <option value="3xx">3xx (Redirection)</option>
                            <option value="4xx">4xx (Client Error)</option>
                            <option value="5xx">5xx (Server Error)</option>
                         </select>
                         <button id="btn-refresh" class="btn btn-icon btn-label-secondary"><i class="ti ti-refresh"></i></button>
                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="datatables-logs table border-top w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Endpoint</th>
                                    <th>Status</th>
                                    <th>Latency</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
             </div>
        </div>
    </div>
</div>

<!-- Modal Log Detail (Improved v2) -->
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-api-logs">
        <div class="modal-content">
            <div class="modal-header bg-lighter py-2 border-bottom">
                <h5 class="modal-title"><i class="ti ti-activity me-2"></i>Inspector Log API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 d-flex">
                    <!-- Sidebar List -->
                    <div class="nav flex-column nav-pills w-25 log-sidebar h-100 log-scroll-area" role="tablist" style="overflow-y: auto;">
                        <button class="nav-link active text-start mb-0" data-bs-toggle="pill" data-bs-target="#insp-payload">
                            <i class="ti ti-file-code me-2"></i> Request Payload
                        </button>
                        <button class="nav-link text-start mb-0" data-bs-toggle="pill" data-bs-target="#insp-headers">
                            <i class="ti ti-list-details me-2"></i> Headers
                        </button>
                        <button class="nav-link text-start mb-0" data-bs-toggle="pill" data-bs-target="#insp-response">
                            <i class="ti ti-server me-2"></i> Response Body
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="tab-content w-75 p-0 m-0 log-content-wrapper position-relative text-white">
                         <div class="position-absolute top-0 end-0 p-3" style="z-index: 10;">
                             <button class="btn btn-sm btn-outline-light opacity-75 hover-opacity-100" onclick="copyLogContent()">
                                 <i class="ti ti-copy me-1"></i> Copy
                             </button>
                         </div>

                         <div class="tab-pane fade show active h-100" id="insp-payload">
                             <div class="h-100 log-scroll-area p-4" style="overflow-y: auto;">
                                 <pre class="m-0" id="detailPayload"></pre>
                             </div>
                         </div>
                         <div class="tab-pane fade h-100" id="insp-headers">
                             <div class="h-100 log-scroll-area p-4" style="overflow-y: auto;">
                                  <pre class="m-0" id="detailHeaders"></pre>
                             </div>
                         </div>
                         <div class="tab-pane fade h-100" id="insp-response">
                             <div class="h-100 log-scroll-area p-4" style="overflow-y: auto;">
                                  <pre class="m-0" id="detailResponse"></pre>
                             </div>
                         </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script type="module">
    $(function () {
        const catalogSlug = "{{ $catalog->slug }}";
        const chartData = @json($chartData);

        // Check for Dark Mode
        const isDark = document.documentElement.classList.contains('dark-style');

        // 1. Initialize Chart
        const options = {
            series: [{
                name: 'Hits',
                data: chartData.hits
            }, {
                name: 'Errors',
                data: chartData.errors
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                foreColor: isDark ? '#d0d2d6' : '#6f6b7d' // Dynamic Font Color
            },
            colors: ['#7367f0', '#ea5455'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            grid: {
                borderColor: isDark ? '#444' : '#e7e7e7', // Optional: Dynamic Grid
            },
            xaxis: {
                categories: chartData.categories,
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

        const chart = new ApexCharts(document.querySelector("#trafficChart"), options);
        chart.render();

        // 2. Fetch Endpoints for Filter
        $.get("{{ route('api-logs.endpoints', $catalog->id) }}", function(endpoints) { // Endpoints route still uses ID or we update it too? Let's check controller. Controller uses ID. So keep ID here.
            endpoints.forEach(ep => {
                $('#filter-endpoint').append(`<option value="${ep.path}">${ep.method} ${ep.path}</option>`);
            });
        });

        // 3. DataTable
        var table = $('.datatables-logs').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api-logs.show', $catalog->slug) }}", // Use Slug here
                data: function(d) {
                    d.endpoint = $('#filter-endpoint').val();
                    d.status_group = $('#filter-status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'user', name: 'user' },
                { data: 'endpoint', name: 'endpoint' },
                { data: 'status_code', name: 'status_code' },
                { data: 'duration', name: 'duration' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        });

        // Filter Events
        $('#filter-endpoint, #filter-status').on('change', function() { table.draw(); });
        $('#btn-refresh').on('click', function() { table.ajax.reload(); });

        // 4. Handle Modal View
        $('.datatables-logs tbody').on('click', '.btn-detail', function () {
            var data = $(this).data();

            // Recursive JSON Parser to handle double encoded strings
            function tryParseJSON(jsonString) {
                try {
                    var o = JSON.parse(jsonString);
                    if (o && typeof o === "object") {
                        return o;
                    }
                    // If it parsed but is still a string (double encoded), try again
                    if (typeof o === "string") {
                        return tryParseJSON(o);
                    }
                }
                catch (e) { }
                return jsonString;
            };

            function syntaxHighlight(json) {
                // First, ensure we have a clean object
                if (typeof json === 'string') {
                    json = tryParseJSON(json);
                }

                // Then stringify it prettily
                if (typeof json !== 'string') {
                    json = JSON.stringify(json, null, 4);
                }

                json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    var cls = 'json-number';
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = 'json-key';
                        } else {
                            cls = 'json-string';
                        }
                    } else if (/true|false/.test(match)) {
                        cls = 'json-boolean';
                    } else if (/null/.test(match)) {
                        cls = 'json-null';
                    }
                    return '<span class="' + cls + '">' + match + '</span>';
                });
            }

            $('#detailPayload').html(syntaxHighlight(data.payload));
            $('#detailHeaders').html(syntaxHighlight(data.headers));
            $('#detailResponse').html(syntaxHighlight(data.response));
            $('#logDetailModal').modal('show');
        });

        // Copy Helper
        window.copyLogContent = function() {
            const activeTab = document.querySelector('.tab-pane.active pre');
            if(activeTab) {
                const text = activeTab.innerText;
                navigator.clipboard.writeText(text).then(() => {
                    alert('Copied to clipboard!');
                });
            }
        };
    });
</script>
@endsection
