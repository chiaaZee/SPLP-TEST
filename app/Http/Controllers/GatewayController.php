<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use App\Models\ApiLog;
use App\Models\ServiceCatalog;
use App\Models\ServiceEndpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GatewayController extends Controller
{
    /**
     * Handle incoming API requests and forward them to the backend service.
     *
     * @param Request $request
     * @param string $slug Service Catalog Slug
     * @param string|null $path Endpoint Path
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, $slug, $path = null)
    {
        $startTime = microtime(true);
        $path = $path ?? '/'; // Default to root if null
        $method = $request->method();


        // [TEST-CONNECTION DUMMY ENDPOINT INTERCEPTION]
        if ($slug === 'test-connection' && $path === 'dummy') {
             // 1. Enforce HMAC Headers existence
             $clientId = $request->header('X-SPLP-Client-ID');
             $timestamp = $request->header('X-SPLP-Timestamp');
             $signature = $request->header('X-SPLP-Signature');

             if (!$clientId || !$timestamp || !$signature) {
                 return \App\Helpers\ApiResponse::error(
                     'Akses Ditolak',
                     'Header Wajib HMAC (Client-ID, Timestamp, Signature) tidak lengkap.',
                     401,
                     'AUTH_INVALID_TOKEN'
                 );
             }

             // 2. Validate Timestamp (Rewrite logic slightly for reusability if possible, but duplication is safer for now to avoid breaking main flow)
             if (!is_numeric($timestamp) || abs(time() - (int)$timestamp) > 300) {
                 return \App\Helpers\ApiResponse::error(
                     'Token Expired',
                     'Timestamp request sudah kadaluarsa (maksimal 5 menit).',
                     401,
                     'AUTH_INVALID_TOKEN'
                 );
             }

             // 3. Find Client
             $client = ApiClient::where('api_key', $clientId)->where('status', 'active')->first();
             if (!$client) {
                 return \App\Helpers\ApiResponse::error(
                     'Client Tidak Valid',
                     'Client ID (API Key) tidak ditemukan atau tidak aktif.',
                     401,
                     'AUTH_INVALID_TOKEN'
                 );
             }

             // 4. Verify Signature
             $body = $request->getContent();
             // Important: For dummy endpoint, we use the EXACT path requested which is /api/test-connection/dummy
             $requestUri = $request->getRequestUri();
             $stringToSign = strtoupper($method) . $requestUri . $timestamp . $body;

             $expectedSignature = hash_hmac('sha256', $stringToSign, $client->secret_key);

             if (!hash_equals($expectedSignature, $signature)) {
                 return \App\Helpers\ApiResponse::error(
                     'Signature Salah',
                     'HMAC Signature tidak cocok. Pastikan Secret Key dan rumus String to Sign benar.',
                     401,
                     'AUTH_INVALID_TOKEN'
                 );
             }

             // 5. Success Response (No DB Logging)
             return \App\Helpers\ApiResponse::success(
                 [
                     'status' => 'connected',
                     'dummy_content' => 'Lorem ipsum dolor sit amet',
                     'your_data' => $request->all()
                 ],
                 'Koneksi Berhasil! Signature Anda valid.',
                 200,
                 [
                     'timestamp' => time(),
                     'client' => $client->name,
                     'mode' => 'test-connection'
                 ]
             );
        }

        // 1. Resolve Service Catalog (Moved Up)
        $catalog = ServiceCatalog::where('slug', $slug)->where('status', 'active')->first();
        if (!$catalog) {
            return response()->json(['message' => 'Service not found or inactive'], 404);
        }

        // 2. Resolve Service Endpoint & Determine Auth Mode
        // Try to find the specific endpoint config to check for Auth Mode overrides
        // Match logic: Exact match or Prefix match (longest first) logic could be better, but for now exact path
        // To support "friendly" paths, we might need to handle trailing slashes
        $searchPath = Str::start($path, '/');

        $endpoint = ServiceEndpoint::where('service_catalog_id', $catalog->id)
            ->where(function($q) use ($searchPath) {
                $q->where('path', $searchPath)
                  ->orWhere('path', trim($searchPath, '/'));
            })
            ->first();

        // Determine Effective Auth Mode
        // Priority: Endpoint > Catalog > Default (Required)
        $authMode = $endpoint->auth_mode ?? $catalog->auth_mode ?? 'required';

        // 3. Authentication Logic
        $client = null;

        if ($authMode === 'required') {
            // [LOCKED] Enforce HMAC Authentication

            // Consumed by Apps -> SPLP Gateway
            $clientId = $request->header('X-SPLP-Client-ID');
            $timestamp = $request->header('X-SPLP-Timestamp');
            $signature = $request->header('X-SPLP-Signature');

            if (!$clientId || !$timestamp || !$signature) {
                return response()->json([
                    'message' => 'Unauthorized: Missing HMAC Headers (X-SPLP-Client-ID, X-SPLP-Timestamp, X-SPLP-Signature)'
                ], 401);
            }

            // Verify Timestamp (Prevent Replay Attack, +/- 5 minutes)
            if (!is_numeric($timestamp) || abs(time() - (int)$timestamp) > 300) {
                return response()->json(['message' => 'Unauthorized: Request Timestamp Expired'], 401);
            }

            // Find Client
            $client = ApiClient::where('api_key', $clientId)->where('status', 'active')->first();
            if (!$client) {
                return response()->json(['message' => 'Unauthorized: Invalid Client ID'], 401);
            }

            // [SECURITY] Check if User Account is Suspended
            if ($client->user && in_array($client->user->status, ['suspended', 'inactive', 'rejected'])) {
                return response()->json(['message' => 'Unauthorized: User Account is ' . ucfirst($client->user->status)], 401);
            }

            // Verify Signature
            $body = $request->getContent();
            $stringToSign = strtoupper($method) . $request->getRequestUri() . $timestamp . $body;

            // Using SHA256 HEX
            $expectedSignature = hash_hmac('sha256', $stringToSign, $client->secret_key);

            if (!hash_equals($expectedSignature, $signature)) {
                return response()->json(['message' => 'Unauthorized: Invalid Signature'], 401);
            }
        }
        else {
            // [OPEN] Public Access - No Auth Required
            // We just proceed. $client remains null.
        }

        // 4. Rate Limiter Logic
        // If public, we limit by IP. If auth, by Client ID.
        if ($catalog->rate_limit > 0) {
            $limiterKey = $client
                ? 'gateway:catalog:' . $catalog->id . ':client:' . $client->id
                : 'gateway:catalog:' . $catalog->id . ':ip:' . $request->ip();

            if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($limiterKey, $catalog->rate_limit)) {
                $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($limiterKey);
                return response()->json([
                    'message' => 'Too Many Requests. Rate limit exceeded.',
                    'retry_after' => $seconds
                ], 429);
            }

            \Illuminate\Support\Facades\RateLimiter::hit($limiterKey);
        }

        // 5. Resolve Target URL
        // If specific endpoint found, use its URL?
        // Current Logic: Base URL + Request Path.
        // Usually, Catalogs have a Base URL (e.g., http://target.com/api) and we append path (e.g., /users).
        // If Endpoint record exists, it might define a SPECIFIC Target URL override?
        // Currently ServiceEndpoint has 'url' field which is "Real endpoint".
        // Let's use the explicit endpoint URL if available and it differs significantly?
        // OR just stick to Base URL + Path logic for consistency unless Endpoint says otherwise?
        // The ServiceEndpoint::url field seems to be the FULL target URL for that endpoint.
        // Let's use it if we found an endpoint match!

        if ($endpoint && $endpoint->url) {
            $targetUrl = $endpoint->url;
        } else {
            $targetUrl = rtrim($catalog->base_url, '/') . $searchPath;
        }

        $qs = $request->getQueryString();

        // 6. Token Binding & Mapping Injection (Only for Authenticated Users)
        if ($client) {
            // Enforce Service Binding
            if ($client->service_catalog_id && $client->service_catalog_id != $catalog->id) {
                return response()->json([
                    'message' => 'Unauthorized: This API Key is not bound to the requested service.'
                ], 403);
            }

            // Inject Mapping Config (Only if catalog requires mapping)
            $mappingConfig = $client->mapping_config ?? [];

            if (!empty($mappingConfig) && $catalog->requires_mapping) {
                 $currentQuery = $request->query();
                 foreach ($mappingConfig as $key => $value) {
                     if (empty($value)) continue;
                     
                     // Map 'skpd_code' to catalog's custom mapping_field if present
                     $paramKey = ($key === 'skpd_code' && !empty($catalog->mapping_field)) 
                         ? $catalog->mapping_field 
                         : $key;
                         
                     $currentQuery[$paramKey] = $value;
                 }
                 $qs = http_build_query($currentQuery);
            }
        }

        // Forward Query String
        if ($qs) {
            // Handle if target url already has query params check?
             if (str_contains($targetUrl, '?')) {
                $targetUrl .= '&' . $qs;
            } else {
                $targetUrl .= '?' . $qs;
            }
        }

        // 7. Forward Request (SPLP -> Target App)
        try {
            $headers = collect($request->header())
                ->except(['host', 'content-length', 'x-splp-client-id', 'x-splp-timestamp', 'x-splp-signature'])
                ->map(fn($val) => is_array($val) ? implode(', ', $val) : $val)
                ->toArray();

            // Inject Target Token
            if ($catalog->target_token) {
                $headers['Authorization'] = 'Bearer ' . $catalog->target_token;
            }

            // Inject X-API-KEY if targeting Lumajang Satu Data portal
            if (str_contains($targetUrl, 'satudata.lumajangkab.go.id')) {
                $headers['X-API-KEY'] = $catalog->target_token ?: 'sata_lmj';
            }

            // Log
            \Illuminate\Support\Facades\Log::info("Gateway Forwarding", [
                'target' => $targetUrl,
                'auth_mode' => $authMode,
                'client' => $client ? $client->name : 'PUBLIC_GUEST'
            ]);

            $http = Http::withHeaders($headers)
                ->withOptions(['verify' => false, 'timeout' => 30]);

            $body = $request->getContent();

            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                if ($request->isJson()) {
                    $response = $http->send($method, $targetUrl, ['json' => $request->json()->all()]);
                } else {
                    $response = $http->send($method, $targetUrl, ['body' => $body]);
                }
            } else {
                $response = $http->send($method, $targetUrl);
            }

            // 8. Logging
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            ApiLog::create([
                'user_id' => $client ? $client->user_id : null,
                'api_client_id' => $client ? $client->id : null,
                'service_catalog_id' => $catalog->id,
                'endpoint' => $searchPath,
                'method' => $method,
                'status_code' => $response->status(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'duration_ms' => $duration,
                'request_header' => json_encode($request->header()),
                'request_body' => $body,
                'response_body' => $response->body()
            ]);

            // 9. Forward Response
            $upstreamHeaders = $response->headers();
            $blockedHeaders = ['transfer-encoding', 'connection', 'content-length', 'host'];
            $cleanHeaders = [];

            foreach ($upstreamHeaders as $key => $values) {
                if (!in_array(strtolower($key), $blockedHeaders)) {
                    $cleanHeaders[$key] = $values;
                }
            }

            return response($response->body(), $response->status())
                ->withHeaders($cleanHeaders);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gateway Error: ' . $e->getMessage()], 502);
        }
    }
}
