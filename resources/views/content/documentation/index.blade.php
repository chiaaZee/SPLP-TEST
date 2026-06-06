@extends('layouts/layoutMaster')

@section('title', 'Panduan Integrasi API')

@section('page-style')
    <style>
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
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Panduan Integrasi API</h3>
                            <p class="text-white opacity-75 mb-0">Spesifikasi Teknis & Standar Keamanan (HMAC-SHA256)</p>
                        </div>
                        <i class="ti ti-book text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- URL Structure -->
                    <h5 class="mb-3">Struktur URL</h5>
                    <div class="alert alert-primary d-flex align-items-center mb-4" role="alert">
                        <i class="ti ti-link me-2"></i>
                        <span class="fw-bold font-monospace">{{ url('/') }}/api/{nama_layanan}/{endpoint_path}</span>
                    </div>
                    <p>Sebelum memanggil API, pastikan Anda memahami struktur URL yang digunakan:</p>
                    <ul class="list-group list-group-flush border rounded mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">Base URL</span>
                                <div class="small text-muted">Alamat utama server SPLP.</div>
                            </div>
                            <code class="text-primary">{{ url('/') }}</code>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">Prefix</span>
                                <div class="small text-muted">Penanda bahwa ini adalah akses API.</div>
                            </div>
                            <code>/api</code>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">{nama_layanan}</span>
                                <div class="small text-muted">Kode unik (Slug) dari Katalog Layanan (misal:
                                    <code>dukcapil</code>).
                                </div>
                            </div>
                            <span class="badge bg-label-secondary">Lihat di Menu Katalog</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">{endpoint_path}</span>
                                <div class="small text-muted">Path spesifik fungsi (misal: <code>/cek-nik</code>).</div>
                            </div>
                            <span class="badge bg-label-secondary">Lihat di Detail Endpoint</span>
                        </li>
                    </ul>

                    <p>
                        Contoh lengkap: <code>{{ url('/') }}/api/dukcapil/cek-nik</code>
                    </p>

                    <hr class="my-4">

                    <h5 class="mb-3">Standar Keamanan (HMAC)</h5>
                    <p>
                        Untuk menjaga keamanan, setiap request ke API Gateway wajib menggunakan mekanisme otentikasi
                        <strong>HMAC Signature</strong>.
                        Anda harus menyertakan kredensial berikut pada <strong>HTTP Header</strong>:
                    </p>

                    <!-- Table Headers -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Header</th>
                                    <th>Contoh Nilai</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><code>X-SPLP-Client-ID</code></td>
                                    <td><code>SPL-DEMO-123</code></td>
                                    <td>Identitas Client (API Key) Anda.</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><code>X-SPLP-Timestamp</code></td>
                                    <td><code>1700000000</code></td>
                                    <td>Waktu saat request dibuat (Unix Timestamp, Seconds).</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><code>X-SPLP-Signature</code></td>
                                    <td><code>a1b2c3d4...</code></td>
                                    <td>Hasil generate signature (Hexadecimal).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-1"></i>
                        <strong>Penting:</strong> Jangan pernah mengirimkan <code>Secret Key</code> Anda dalam Header atau
                        URL.
                        Secret Key hanya digunakan di sisi server Anda untuk membuat Signature.
                    </div>

                    <hr class="my-4">

                    <!-- Signature Logic -->
                    <h5 class="mb-3">Cara Membuat X-SPLP-Signature</h5>
                    <p>
                        Signature dibuat dengan algoritma <strong>HMAC-SHA256</strong>. Input dari algoritma ini adalah:
                        <br>
                        1. <strong>Key</strong>: Secret Key Anda.
                        <br>
                        2. <strong>Message</strong>: Gabungan string dari Method, URL, Timestamp, dan Body.
                    </p>

                    <div class="bg-label-secondary p-4 rounded mb-4">
                        <h6>Rumus String to Sign (Message):</h6>
                        <p>Gabungkan data berikut tanpa pemisah (separator):</p>
                        <pre class="fw-bold fs-5 text-dark">UPPER(Method) + EndpointURI + Timestamp + RequestBody</pre>

                        <ul class="mb-0 small text-muted">
                            <li><code>UPPER(Method)</code>: HTTP Method huruf kapital (GET, POST, PUT).</li>
                            <li><code>EndpointURI</code>: Path lengkap mulai dari /api (contoh:
                                <code>/api/dukcapil/nik/123</code>).
                            </li>
                            <li>
                                <code>Timestamp</code>: Waktu inisiasi request dalam detik (Unix Epoch).
                                <br>
                                <small class="text-muted">
                                    <i class="ti ti-info-circle"></i> Angka ini didapat dari fungsi waktu sistem:
                                    <span class="badge bg-label-primary">PHP: time()</span>
                                    <span class="badge bg-label-warning">JS: Date.now() / 1000</span>
                                </small>
                            </li>
                            <li><code>RequestBody</code>: String JSON mentah (Raw Body). Kosongkan jika method GET tanpa
                                body.</li>
                        </ul>
                    </div>

                    <h6>Contoh Perhitungan:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="150"><strong>Method</strong></td>
                                    <td>: <code>POST</code></td>
                                </tr>
                                <tr>
                                    <td><strong>URI</strong></td>
                                    <td>: <code>/api/demo/user</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Timestamp</strong></td>
                                    <td>: <code>1600000000</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Body</strong></td>
                                    <td>: <code>{"id":1}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Secret Key</strong></td>
                                    <td>: <code>SECRET123</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <p class="mb-1"><strong>1. String to Sign:</strong></p>
                        <div class="bg-dark text-white p-2 rounded mb-3 font-monospace small">
                            POST/api/demo/user1600000000{"id":1}
                        </div>

                        <p class="mb-1"><strong>2. Generate HMAC-SHA256 (Hex):</strong></p>
                        <div class="bg-label-primary p-2 rounded font-monospace small">
                            hmac_sha256("POST/api/demo/user1600000000{\"id\":1}", "SECRET123")
                            <br>
                            Result: <strong>7c9f3....</strong> (Simpan ini ke header X-SPLP-Signature)
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Code Examples Tabs -->
                    <h5 class="mb-3">Contoh Coding (Copy-Paste Ready)</h5>
                    <div class="code-window">
                        <div class="code-header d-flex border-bottom border-secondary px-3" style="background: #252526;">
                            <div class="nav nav-code-tabs" role="tablist">
                                <button class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#tab-curl">cURL</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-php">PHP</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-js">NodeJS</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-python">Python</button>
                            </div>
                        </div>
                        <div class="code-body p-0">
                            <div class="tab-content p-3">
                                <!-- cURL -->
                                <div class="tab-pane fade show active" id="tab-curl">
                                    <pre class="text-white m-0 small font-monospace"># Konfigurasi
    METHOD="POST"
    URI="/api/dukcapil/ceknik"
    TIMESTAMP=$(date +%s)
    BODY='{"nik":"350..."}'
    SECRET="SECRET-KEY..."
    CLIENT_ID="SPL-DEMO..."

    # 1. Buat String to Sign
    STRING_TO_SIGN="${METHOD}${URI}${TIMESTAMP}${BODY}"

    # 2. Buat Signature (HMAC-SHA256 Hex)
    # Membutuhkan OpenSSL
    SIGNATURE=$(echo -n "${STRING_TO_SIGN}" | openssl dgst -sha256 -hmac "${SECRET}" -hex | sed 's/^.* //')

    # 3. Kirim Request
    curl -X ${METHOD} "https://splp.lumajangkab.go.id${URI}" \
      -H "X-SPLP-Client-ID: ${CLIENT_ID}" \
      -H "X-SPLP-Timestamp: ${TIMESTAMP}" \
      -H "X-SPLP-Signature: ${SIGNATURE}" \
      -H "Content-Type: application/json" \
      -d "${BODY}"</pre>
                                </div>

                                <!-- PHP -->
                                <div class="tab-pane fade" id="tab-php">
                                    <pre class="text-white m-0 small font-monospace">$clientId = "SPL-DEMO...";
    $secret = "SECRET-KEY...";

    $method = "POST";
    $uri = "/api/dukcapil/ceknik";
    $timestamp = time();
    $body = '{"nik":"350..."}'; // Raw JSON String

    // 1. Buat String to Sign
    $stringToSign = strtoupper($method) . $uri . $timestamp . $body;

    // 2. Buat Signature (HMAC-SHA256 Hex)
    $signature = hash_hmac('sha256', $stringToSign, $secret);

    // 3. Kirim Request
    $response = Http::withHeaders([
        "X-SPLP-Client-ID" => $clientId,
        "X-SPLP-Timestamp" => $timestamp,
        "X-SPLP-Signature" => $signature,
        "Content-Type" => "application/json"
    ])->post("https://splp.lumajangkab.go.id" . $uri, json_decode($body, true));

    echo $response->body();</pre>
                                </div>

                                <!-- NodeJS -->
                                <div class="tab-pane fade" id="tab-js">
                                    <pre class="text-white m-0 small font-monospace">const crypto = require('crypto');
    const axios = require('axios');

    const clientId = "SPL-DEMO...";
    const secret = "SECRET-KEY...";

    const method = "POST";
    const uri = "/api/dukcapil/ceknik";
    const timestamp = Math.floor(Date.now() / 1000); // Unix Seconds
    const body = JSON.stringify({ nik: "350..." });

    // 1. Buat String to Sign
    const stringToSign = method.toUpperCase() + uri + timestamp + body;

    // 2. Buat Signature
    const signature = crypto.createHmac('sha256', secret)
        .update(stringToSign)
        .digest('hex');

    // 3. Kirim Request
    axios({
        method: method,
        url: "https://splp.lumajangkab.go.id" + uri,
        headers: {
            "Content-Type": "application/json",
            "X-SPLP-Client-ID": clientId,
            "X-SPLP-Timestamp": timestamp,
            "X-SPLP-Signature": signature
        },
        data: body
    }).then(res => console.log(res.data));</pre>
                                </div>

                                <!-- Python -->
                                <div class="tab-pane fade" id="tab-python">
                                    <pre class="text-white m-0 small font-monospace">import hmac
    import hashlib
    import time
    import requests
    import json

    client_id = "SPL-DEMO..."
    secret = "SECRET-KEY..."

    method = "POST"
    uri = "/api/dukcapil/ceknik"
    timestamp = str(int(time.time()))
    body_dict = {"nik": "350..."}
    body = json.dumps(body_dict, separators=(',', ':')) # Compact JSON

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

    url: "https://splp.lumajangkab.go.id" + uri,
    response = requests.post(url, data=body, headers=headers)
    print(response.text)</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">

                    <!-- Testing Guide -->
                    <h5 class="mb-3">Uji Coba Koneksi (Dummy Endpoint)</h5>
                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                        <i class="ti ti-info-circle me-2"></i>
                        <div>
                            Untuk memastikan implementasi HMAC Signature Anda sudah benar tanpa mengganggu data produksi,
                            gunakan endpoint khusus di bawah ini.
                        </div>
                    </div>

                    <ul class="list-group list-group-flush border rounded mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">Endpoint Uji Coba</span>
                                <div class="small text-muted">Akan mengembalikan respons sukses jika signature valid.</div>
                            </div>
                            <code class="text-primary">{{ url('/') }}/api/test-connection/dummy</code>
                        </li>
                        <li class="list-group-item">
                            <span class="fw-bold d-block mb-1">Ketentuan:</span>
                            <ul class="mb-0 ps-3 small text-muted">
                                <li>Wajib menyertakan Header <code>X-SPLP-Client-ID</code>, <code>X-SPLP-Timestamp</code>, dan
                                    <code>X-SPLP-Signature</code>.</li>
                                <li>Isi Body bebas (atau kosong jika method GET).</li>
                                <li>Request ke endpoint ini <strong>TIDAK DICATAT</strong> dalam log aktivitas sistem.</li>
                            </ul>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
@endsection
