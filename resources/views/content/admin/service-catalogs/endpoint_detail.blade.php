@extends('layouts/layoutMaster')

@section('title', 'Endpoint Detail - ' . $endpoint->name)

@section('vendor-style')
    {{-- CDN since vendor files not in public folder --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
@endsection

@section('page-style')
    <style>
        .method-badge {
            font-size: 0.9rem;
            font-weight: 700;
            padding: 0.35rem 0.6rem;
            border-radius: 4px;
            text-transform: uppercase;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        .method-GET {
            background-color: #e8f3ff;
            color: #0064e6;
            border: 1px solid #cce4ff;
        }

        .method-POST {
            background-color: #e6fffa;
            color: #00966d;
            border: 1px solid #ccfce7;
        }

        .method-PUT {
            background-color: #fff8e6;
            color: #d97706;
            border: 1px solid #ffe8cc;
        }

        .method-DELETE {
            background-color: #ffe6e6;
            color: #d92626;
            border: 1px solid #ffcccc;
        }

        .code-window {
            background: #1e1e1e;
            color: #d4d4d4;
            border-radius: 8px;
            font-family: 'Fira Code', 'Consolas', monospace;
            font-size: 0.85rem;
        }

        .code-header {
            background: #252526;
            padding: 0.5rem 1rem;
            border-radius: 8px 8px 0 0;
            border-bottom: 1px solid #333;
        }

        .code-body {
            padding: 1rem;
            overflow-x: auto;
        }

        .nav-code-tabs .nav-link {
            color: #888;
            padding: 0.2rem 0.8rem;
            font-size: 0.8rem;
        }

        .nav-code-tabs .nav-link.active {
            color: #fff;
            background: transparent;
            border-bottom: 2px solid #007bff;
        }
    </style>
@endsection

@section('content')

    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="col-md-8 z-1">
                            <div class="text-white opacity-75 mb-1 text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.8rem;">
                                <i class="ti ti-layout-grid me-1"></i> {{ $endpoint->catalog->name }}
                            </div>
                            <h3 class="text-white fw-bold mb-1">{{ $endpoint->name }}</h3>
                            <p class="text-white opacity-75 mb-0">
                                <span class="badge bg-white text-primary me-2">{{ $endpoint->method }}</span>
                                {{ url('/') }}/api/{{ $endpoint->catalog->slug }}{{ Str::start($endpoint->path, '/') }}
                            </p>
                        </div>
                        <div class="z-1">
                            <div class="d-flex gap-2">
                                <button class="btn btn-secondary text-white" onclick="history.back()">
                                    <i class="ti ti-arrow-left me-1"></i> Kembali
                                </button>
                            </div>
                        </div>
                    </div>
                    <i class="ti ti-api position-absolute text-white opacity-25"
                        style="font-size: 8rem; right: 1rem; bottom: -2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content (Docs) -->
        <div class="col-xl-5 col-lg-6 mb-4">
            <!-- Documentation Panel -->
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Dokumentasi</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="mb-4">
                        <label class="form-label text-muted text-uppercase small fw-bold">Gateway URL (Public)</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti ti-link"></i></span>
                            <input type="text" class="form-control"
                                value="{{ url('/') }}/api/{{ $endpoint->catalog->slug }}{{ Str::start($endpoint->path, '/') }}"
                                readonly id="gatewayUrl">
                            <button class="btn btn-outline-primary" type="button"
                                onclick="copyToClipboard('#gatewayUrl')"><i class="ti ti-copy"></i></button>
                        </div>
                    </div>

                    @if(auth()->user()->can('manage_catalogs'))
                        <hr class="my-4" style="border-style: dashed;">
                        <div class="mb-4">
                            <label class="form-label text-danger text-uppercase small fw-bold"><i
                                    class="ti ti-lock me-1"></i>Admin Area: Target Backend</label>

                            <div class="mb-3">
                                <label class="text-muted small">Target URL (Asli)</label>
                                <div class="alert alert-secondary d-flex align-items-center p-2 mb-0" role="alert">
                                    <i class="ti ti-server me-2"></i>
                                    <div class="small font-monospace text-truncate">{{ $endpoint->url }}</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Target Bearer Token</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control font-monospace small text-muted"
                                        value="{{ Str::limit($endpoint->catalog->target_token, 20) }}..." readonly>
                                    <span class="input-group-text"><i class="ti ti-key"></i></span>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4" style="border-style: dashed;">
                    @endif

                    <div class="mb-4">
                        <label class="form-label text-muted text-uppercase small fw-bold">Deskripsi</label>
                        <p class="mb-0">{{ $endpoint->description ?: 'Tidak ada deskripsi.' }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        @if(auth()->user()->can('manage_catalogs'))
                            <h6 class="fw-bold mb-2">Headers Required (Direct to Backend)</h6>
                            <ul class="list-group list-group-flush border rounded">
                                <li class="list-group-item d-flex justify-content-between small">
                                    <code>Authorization</code> <span class="text-muted">Bearer &lt;Target Token&gt;</span>
                                </li>
                            </ul>
                        @else
                            <h6 class="fw-bold mb-2">Headers Required (Via Gateway)</h6>
                            <ul class="list-group list-group-flush border rounded">
                                <li class="list-group-item d-flex justify-content-between small">
                                    <code>X-SPLP-Client-ID</code> <span class="text-muted">&lt;your_client_id&gt;</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between small">
                                    <code>X-SPLP-Timestamp</code> <span class="text-muted">Unix Timestamp (Seconds)</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between small">
                                    <code>X-SPLP-Signature</code> <span class="text-muted">HMAC-SHA256 (Hex)</span>
                                </li>
                            </ul>
                        @endif
                    </div>

                    @if($endpoint->request_body)
                        <div class="mb-3">
                            <h6 class="fw-bold mb-2">Request Body (Example)</h6>
                            <pre class="bg-light p-3 rounded small font-monospace"
                                style="max-height: 200px; overflow-y: auto;">{{ $endpoint->request_body }}</pre>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Testing Console & Code Tabs -->
        <div class="col-xl-7 col-lg-6 mb-4">
             <div class="nav-align-top h-100">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-console"><i
                                class="ti ti-terminal me-1"></i> Interactive Console</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-code"><i
                                class="ti ti-code me-1"></i> Code Snippets</button>
                    </li>
                </ul>
                <div class="tab-content h-100 p-0">

                    <!-- Console Tab -->
                    <div class="tab-pane fade show active h-100" id="tab-console">
                        <div class="card h-100 border-top-0 rounded-0">
                            <div class="card-body">
                                <form id="consoleForm" onsubmit="return false">
                                    <!-- Test As Client (Impersonation) -->
                                    @if(isset($clients) && count($clients) > 0)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-primary">
                                            <i class="ti ti-users me-1"></i> Test As Client (Impersonation)
                                        </label>
                                        <select id="impersonate_client" name="impersonate_client_id" class="form-select">
                                            <option value="" selected>-- Use Default Config --</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" data-key="{{ $client->api_key }}">
                                                    {{ $client->name }}
                                                    @if(isset($client->mapping_config))
                                                        (Mapped: {{ json_encode($client->mapping_config) }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Simulasikan request sebagai Client tertentu untuk memverifikasi filter SKPD.</div>
                                    </div>
                                    @endif

                                    <!-- Auth Token (Admin Only) -->
                                    @if(auth()->user()->can('manage_catalogs'))
                                    <div class="mb-3">
                                         <label class="form-label d-flex justify-content-between align-items-center">
                                            <span>Authorization Token</span>
                                            @if($endpoint->catalog->target_token)
                                                <span class="badge bg-label-success" id="token_status_badge">
                                                    <i class="ti ti-lock me-1"></i> Configured
                                                </span>
                                            @else
                                                <span class="badge bg-label-warning small">Bearer</span>
                                            @endif
                                         </label>

                                         @if($endpoint->catalog->target_token)
                                             <div class="input-group">
                                                 <input type="text" class="form-control bg-light" value="Using Configured Target Token" readonly disabled>
                                                 <button class="btn btn-outline-secondary" type="button" onclick="toggleTokenInput(this)">
                                                     <i class="ti ti-pencil"></i> Override
                                                 </button>
                                             </div>

                                             <!-- Hidden Real Input -->
                                             <div id="custom_token_container" class="mt-2 d-none">
                                                 <input type="text" name="auth_token" id="auth_token_input" class="form-control font-monospace" placeholder="Paste bearer token..." value="{{ $endpoint->catalog->target_token }}">
                                                 <div class="form-text text-warning">Mengubah ini akan mengabaikan token default katalog.</div>
                                             </div>
                                         @else
                                             <input type="text" name="auth_token" id="auth_token_input" class="form-control font-monospace" placeholder="Paste bearer token for admin access..." value="">
                                         @endif
                                         <div id="auth_token_help" class="form-text">
                                             @if($endpoint->catalog->target_token)
                                                Token default catalog digunakan secara otomatis.
                                             @else
                                                Token Admin (Super User) untuk akses ke Backend Target.
                                             @endif
                                         </div>
                                    </div>

                                    <script>
                                    function toggleTokenInput(btn) {
                                        const container = document.getElementById('custom_token_container');
                                        if (container.classList.contains('d-none')) {
                                            container.classList.remove('d-none');
                                            btn.innerHTML = '<i class="ti ti-x"></i> Cancel';
                                        } else {
                                            container.classList.add('d-none');
                                            btn.innerHTML = '<i class="ti ti-pencil"></i> Override';
                                            // Reset value
                                            document.getElementById('auth_token_input').value = "{{ $endpoint->catalog->target_token }}";
                                        }
                                    }
                                    </script>
                                    @elseif($endpoint->catalog->target_token)
                                        <input type="hidden" name="auth_token" value="{{ $endpoint->catalog->target_token }}">
                                    @else
                                        <input type="hidden" name="auth_token" value="">
                                    @endif

                                    <!-- Path Suffix (For IDs etc) -->
                                    <div class="mb-3">
                                        <label class="form-label">Path Suffix / ID (Optional)</label>
                                        <input type="text" name="path_suffix" class="form-control font-monospace" placeholder="/123  or  /slug-data">
                                        <small class="text-muted d-block mt-1">Gunakan ini untuk tes endpoint detail. Akan ditempelkan di akhir URL.</small>
                                    </div>

                                    <!-- Query Params (Available for All) -->
                                    <div class="mb-3">
                                        <label class="form-label">Query Params (Optional - Simulation)</label>
                                        <input type="text" name="query_params" class="form-control font-monospace" placeholder="key=value&page=1&per_page=10">
                                        <small class="text-muted d-block mt-1">Format: <code>key=value&page=1&per_page=10</code></small>
                                    </div>

                                    <!-- Request Body -->
                                    <div class="mb-3">
                                        <label class="form-label">Request Body (JSON)</label>
                                        <textarea name="body" class="form-control font-monospace" rows="6">{{ $endpoint->request_body }}</textarea>

                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                         <div>
                                            <small class="text-muted">Method: <strong class="text-uppercase">{{ $endpoint->method }}</strong></small>
                                         </div>
                                         <button type="button" class="btn btn-primary" id="btnSendRequest">
                                            <i class="ti ti-send me-1"></i> Send Request
                                         </button>
                                    </div>
                                </form>

                                <!-- Response Area -->
                                <div class="mt-4">
                                    <label class="form-label">Response</label>
                                    <div class="code-window rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                        <pre id="resBody" class="m-0 text-white small font-monospace">Waiting for response...</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Code Snippets Tab -->
                    <div class="tab-pane fade h-100" id="tab-code">
                        <div class="code-window h-100">
                            <div class="code-header d-flex border-bottom border-secondary px-3"
                                style="background: #252526;">
                                <div class="nav nav-code-tabs" role="tablist">
                                    <button class="nav-link active" data-bs-toggle="tab"
                                        data-bs-target="#snip-curl">cURL</button>
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#snip-php">PHP</button>
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#snip-js">NodeJS</button>
                                    <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#snip-python">Python</button>
                                </div>
                            </div>
                            <div class="code-body p-0">
                                <div class="tab-content p-3">

                                    @if(auth()->user()->can('manage_catalogs'))
                                        {{-- ADMIN VIEW: TARGET URL + BEARER --}}
                                        <div class="tab-pane fade show active" id="snip-curl">
<pre class="text-white m-0 small font-monospace">curl -X {{ $endpoint->method }} "{{ $endpoint->url }}" \
  -H "Authorization: Bearer {{ $endpoint->catalog->target_token ?: '<token>' }}" \
  -H "Content-Type: application/json"{{ $endpoint->request_body ? ' \\' . PHP_EOL . '  -d \'' . addslashes($endpoint->request_body) . '\'' : '' }}</pre>
                                        </div>
                                        <div class="tab-pane fade" id="snip-php">
<pre class="text-white m-0 small font-monospace">$response = Http::withToken('{{ $endpoint->catalog->target_token ?: '<token>' }}')
  ->withHeaders(['Content-Type' => 'application/json'])
  ->{{ strtolower($endpoint->method) }}('{{ $endpoint->url }}'{{ $endpoint->request_body ? ', ' . json_encode(json_decode($endpoint->request_body)) . '' : '' }});

return $response->json();</pre>
                                        </div>
                                        <div class="tab-pane fade" id="snip-js">
<pre class="text-white m-0 small font-monospace">fetch("{{ $endpoint->url }}", {
  method: "{{ $endpoint->method }}",
  headers: {
    "Authorization": "Bearer {{ $endpoint->catalog->target_token ?: '<token>' }}",
    "Content-Type": "application/json"
  }{{ $endpoint->request_body ? ',' . PHP_EOL . '  body: JSON.stringify(' . $endpoint->request_body . ')' : '' }}
});</pre>
                                        </div>
                                        <div class="tab-pane fade" id="snip-python">
<pre class="text-white m-0 small font-monospace">import requests
import json

url = "{{ $endpoint->url }}"
headers = {
    "Authorization": "Bearer {{ $endpoint->catalog->target_token ?: '<token>' }}",
    "Content-Type": "application/json"
}
{{ $endpoint->request_body ? "data = " . $endpoint->request_body : "data = {}" }}

response = requests.{{ strtolower($endpoint->method) }}(url, headers=headers, json=data)
print(response.json())</pre>
                                        </div>
                                    @else
                                        {{-- PUBLIC/DINAS VIEW: GATEWAY URL + HMAC --}}
                                        <div class="tab-pane fade show active" id="snip-curl">
<pre class="text-white m-0 small font-monospace"># Konfigurasi
METHOD="{{ $endpoint->method }}"
URI="/api/{{ $endpoint->catalog->slug }}{{ Str::start($endpoint->path, '/') }}"
TIMESTAMP=$(date +%s)
BODY='{{ $endpoint->request_body }}'
SECRET="<your_secret_key>"
CLIENT_ID="<your_client_id>"

# 1. Buat String to Sign
STRING_TO_SIGN="${METHOD}${URI}${TIMESTAMP}${BODY}"

# 2. Buat Signature (HMAC-SHA256 Hex)
# Membutuhkan OpenSSL
SIGNATURE=$(echo -n "${STRING_TO_SIGN}" | openssl dgst -sha256 -hmac "${SECRET}" -hex | sed 's/^.* //')

# 3. Kirim Request
curl -X {{ $endpoint->method }} "{{ url('/') }}${URI}" \
  -H "X-SPLP-Client-ID: ${CLIENT_ID}" \
  -H "X-SPLP-Timestamp: ${TIMESTAMP}" \
  -H "X-SPLP-Signature: ${SIGNATURE}" \
  -H "Content-Type: application/json"{{ $endpoint->request_body ? ' \\' . PHP_EOL . '  -d \'${BODY}\'' : '' }}</pre>
                                        </div>
                                        <div class="tab-pane fade" id="snip-php">
<pre class="text-white m-0 small font-monospace">$clientId = 'YOUR_CLIENT_ID';
$secret = 'YOUR_SECRET_KEY';

$method = '{{ $endpoint->method }}';
$uri = '/api/{{ $endpoint->catalog->slug }}{{ Str::start($endpoint->path, '/') }}';
$timestamp = time();
$body = {{ $endpoint->request_body ? "'" . $endpoint->request_body . "'" : "''" }};

// 1. Buat String to Sign
$stringToSign = strtoupper($method) . $uri . $timestamp . $body;

// 2. Buat Signature (HMAC-SHA256 Hex)
$signature = hash_hmac('sha256', $stringToSign, $secret);

// 3. Kirim Request
$response = Http::withHeaders([
    'X-SPLP-Client-ID' => $clientId,
    'X-SPLP-Timestamp' => $timestamp,
    'X-SPLP-Signature' => $signature,
    'Content-Type' => 'application/json'
])->{{ strtolower($endpoint->method) }}('{{ url('/') }}' . $uri{{ $endpoint->request_body ? ', json_decode($body, true)' : '' }});

return $response->json();</pre>
                                        </div>
                                        <div class="tab-pane fade" id="snip-js">
<pre class="text-white m-0 small font-monospace">const crypto = require('crypto');
const axios = require('axios');

const clientId = 'YOUR_CLIENT_ID';
const secret = 'YOUR_SECRET_KEY';

const method = '{{ $endpoint->method }}';
const uri = '/api/{{ $endpoint->catalog->slug }}{{ Str::start($endpoint->path, '/') }}';
const timestamp = Math.floor(Date.now() / 1000); // Unix Seconds
const body = {{ $endpoint->request_body ? "JSON.stringify(" . $endpoint->request_body . ")" : "''" }};

// 1. Buat String to Sign
const stringToSign = method.toUpperCase() + uri + timestamp + body;

// 2. Buat Signature
const signature = crypto.createHmac('sha256', secret)
    .update(stringToSign)
    .digest('hex');

// 3. Kirim Request
axios({
  method: method,
  url: '{{ url('/') }}' + uri,
  headers: {
    'X-SPLP-Client-ID': clientId,
    'X-SPLP-Timestamp': timestamp,
    'X-SPLP-Signature': signature,
    'Content-Type': 'application/json'
  },
  data: body
}).then(res => { /* Handle response */ });</pre>
                                        </div>
                                        <div class="tab-pane fade" id="snip-python">
<pre class="text-white m-0 small font-monospace">import hmac
import hashlib
import time
import requests
import json

client_id = "YOUR_CLIENT_ID"
secret = "YOUR_SECRET_KEY"

method = "{{ $endpoint->method }}"
uri = "/api/{{ $endpoint->catalog->slug }}{{ Str::start($endpoint->path, '/') }}"
timestamp = str(int(time.time()))
{{ $endpoint->request_body ? "body_dict = " . $endpoint->request_body . PHP_EOL . "body = json.dumps(body_dict, separators=(',', ':'))" : "body = ''" }}

# 1. Buat String to Sign
string_to_sign = f"{method.upper()}{uri}{timestamp}{body}"

# 2. Buat Signature
signature = hmac.new(
    secret.encode(),
    string_to_sign.encode(),
    hashlib.sha256
).hexdigest()

# 3. Kirim Request
headers = {
    "Content-Type": "application/json",
    "X-SPLP-Client-ID": client_id,
    "X-SPLP-Timestamp": timestamp,
    "X-SPLP-Signature": signature
}

# 3. Kirim Request
url = "{{ url('/') }}" + uri
response = requests.{{ strtolower($endpoint->method) }}(url, data=body, headers=headers)
print(response.text)</pre>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    {{-- CDN since vendor files not in public folder --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-script')
    <script>
        function copyToClipboard(selector) {
            const copyText = document.querySelector(selector);
            copyText.select();
            navigator.clipboard.writeText(copyText.value);
            showToast('Copied to clipboard!');
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'alert alert-success position-fixed';
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
            toast.innerHTML = '<i class="ti ti-check me-1"></i> ' + message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Send Request Handler
            const btnSendRequest = document.getElementById('btnSendRequest');
            if (btnSendRequest) {
                // Impersonation Handler
                const impersonateSelect = document.getElementById('impersonate_client');
                if (impersonateSelect) {
                    impersonateSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const helpText = document.getElementById('auth_token_help');

                        if (this.value) {
                            // Client Selected
                            if(helpText) helpText.innerHTML = '<span class="text-primary"><i class="ti ti-user-check me-1"></i> <strong>Simulation Active:</strong> Injecting params from this client (e.g. SKPD ID). Auth remains as Admin.</span>';
                        } else {
                            // Revert
                            if(helpText) helpText.innerHTML = 'Token Admin (Super User) untuk akses ke Backend Target.';
                        }
                    });
                }

                btnSendRequest.addEventListener('click', function() {
                    const btn = this;
                    const form = document.getElementById('consoleForm');
                    const authToken = form.querySelector('input[name="auth_token"]')?.value || '';
                    const body = form.querySelector('textarea[name="body"]')?.value || '';
                    const queryParams = form.querySelector('input[name="query_params"]')?.value || '';
                    const pathSuffix = form.querySelector('input[name="path_suffix"]')?.value || '';
                    const impersonateClientId = form.querySelector('select[name="impersonate_client_id"]')?.value || ''; // Get selected client ID

                    btn.disabled = true;
                    btn.innerHTML = '<i class="ti ti-loader animate-spin me-1"></i> Sending...';
                    document.getElementById('resBody').textContent = 'Waiting for response...';

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('auth_token', authToken);
                    formData.append('body', body);
                    formData.append('query_params', queryParams);
                    formData.append('path_suffix', pathSuffix);
                    formData.append('method', '{{ $endpoint->method }}');
                    if(impersonateClientId) {
                        formData.append('impersonate_client_id', impersonateClientId);
                    }

                    fetch("{{ route('service-catalogs.endpoint.test', ['catalog' => $endpoint->catalog->slug, 'id' => $endpoint->slug ?? $endpoint->id]) }}", {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        let bodyContent = res.body;
                        if (typeof bodyContent === 'object') bodyContent = JSON.stringify(bodyContent, null, 2);
                        document.getElementById('resBody').textContent = bodyContent;
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ti ti-send me-1"></i> Send Request';
                    })
                    .catch(err => {
                        document.getElementById('resBody').textContent = 'Error: ' + err.message;
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ti ti-send me-1"></i> Send Request';
                    });
                });
            }

            // Edit Endpoint Handler
            document.querySelectorAll('.edit-endpoint').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const method = this.dataset.method;
                    const path = this.dataset.path;
                    const url = this.dataset.url || '';
                    const body = this.dataset.body || '';

                    Swal.fire({
                        title: 'Edit Endpoint',
                        html: `
                            <div class="text-start">
                                <div class="mb-3">
                                    <label class="form-label">Nama Endpoint</label>
                                    <input type="text" id="swal-name" class="form-control" value="${name}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Method</label>
                                    <select id="swal-method" class="form-select">
                                        <option value="GET" ${method === 'GET' ? 'selected' : ''}>GET</option>
                                        <option value="POST" ${method === 'POST' ? 'selected' : ''}>POST</option>
                                        <option value="PUT" ${method === 'PUT' ? 'selected' : ''}>PUT</option>
                                        <option value="DELETE" ${method === 'DELETE' ? 'selected' : ''}>DELETE</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Path</label>
                                    <input type="text" id="swal-path" class="form-control" value="${path}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Target URL</label>
                                    <input type="text" id="swal-url" class="form-control" value="${url}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Request Body (JSON)</label>
                                    <textarea id="swal-body" class="form-control" rows="4">${body}</textarea>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '<i class="ti ti-check me-1"></i> Simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#7367f0',
                        preConfirm: () => {
                            return {
                                name: document.getElementById('swal-name').value,
                                method: document.getElementById('swal-method').value,
                                path: document.getElementById('swal-path').value,
                                url: document.getElementById('swal-url').value,
                                request_body: document.getElementById('swal-body').value
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData();
                            formData.append('_token', '{{ csrf_token() }}');
                            formData.append('_method', 'PUT');
                            formData.append('name', result.value.name);
                            formData.append('method', result.value.method);
                            formData.append('path', result.value.path);
                            formData.append('url', result.value.url);
                            formData.append('request_body', result.value.request_body);

                            fetch("{{ url('admin/service-catalogs/endpoint') }}/" + id, {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(res => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Endpoint berhasil diperbarui.',
                                    confirmButtonColor: '#7367f0'
                                }).then(() => location.reload());
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat memperbarui endpoint.',
                                    confirmButtonColor: '#7367f0'
                                });
                            });
                        }
                    });
                });
            });

            // Delete Endpoint Handler
            document.querySelectorAll('.delete-endpoint').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    Swal.fire({
                        title: 'Hapus Endpoint?',
                        text: 'Anda yakin ingin menghapus endpoint ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="ti ti-trash me-1"></i> Hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData();
                            formData.append('_token', '{{ csrf_token() }}');
                            formData.append('_method', 'DELETE');

                            fetch("{{ url('admin/service-catalogs/endpoint') }}/" + id, {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(res => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: 'Endpoint berhasil dihapus.',
                                    confirmButtonColor: '#7367f0'
                                }).then(() => {
                                    window.location.href = "{{ route('service-catalogs.show', $endpoint->catalog->slug) }}";
                                });
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus endpoint.',
                                    confirmButtonColor: '#7367f0'
                                });
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection
