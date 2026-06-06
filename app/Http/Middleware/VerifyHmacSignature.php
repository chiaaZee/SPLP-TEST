<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHmacSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientId = $request->header('X-Client-ID');
        $timestamp = $request->header('X-Timestamp');
        $signature = $request->header('X-Signature');

        if (!$clientId || !$timestamp || !$signature) {
            return response()->json([
                'message' => 'Missing Authentication Headers (X-Client-ID, X-Timestamp, X-Signature)'
            ], 401);
        }

        // Prevent Replay Attack (5 minutes tolerance)
        if (abs(time() - $timestamp) > 300) {
            return response()->json(['message' => 'Request Expired (Check your system clock)'], 401);
        }

        $client = \App\Models\ApiClient::where('api_key', $clientId)->where('status', 'active')->first();

        if (!$client) {
            return response()->json(['message' => 'Invalid Client ID'], 401);
        }

        // Reconstruct Signature
        $method = $request->method();
        $uri = $request->getRequestUri(); // Includes Query String
        $body = $request->getContent(); // Raw Body

        // Standard: Method \n URI \n Timestamp \n Body
        $stringToSign = "$method\n$uri\n$timestamp\n$body";

        $expectedSignature = base64_encode(hash_hmac('sha256', $stringToSign, $client->secret_key, true));

        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json([
                'message' => 'Invalid Signature',
                'debug' => config('app.debug') ? ['string_to_sign' => $stringToSign] : null
            ], 401);
        }

        // Log the usage
        $client->update(['last_used_at' => now()]);

        // Login the user (Service Account)
        auth()->login($client->user);

        // Detect Service Catalog from URL (Assuming /api/v1/{slug}/...)
        $pathSegments = explode('/', $request->path());
        $catalogId = null;

        // Try to find a slug in the path segments
        // This is a heuristic: check if any segment matches a ServiceCatalog slug
        // Optimization: In production, route parameters should be used.
        if (count($pathSegments) > 0) {
            foreach ($pathSegments as $segment) {
                // Ignore common prefixes
                if (in_array($segment, ['api', 'v1', 'v2']))
                    continue;

                $catalog = \App\Models\ServiceCatalog::where('slug', $segment)->first();
                if ($catalog) {
                    $catalogId = $catalog->id;
                    break;
                }
            }
        }

        // Store client ID and Catalog ID for logging in terminate
        $request->attributes->add([
            'api_client_id' => $client->id,
            'auth_user_id' => $client->user_id,
            'service_catalog_id' => $catalogId
        ]);

        return $next($request);
    }

    public function terminate($request, $response)
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $request->server('REQUEST_TIME_FLOAT');
        $duration = round((microtime(true) - $startTime) * 1000);

        \App\Models\ApiLog::create([
            'user_id' => $request->attributes->get('auth_user_id'),
            'api_client_id' => $request->attributes->get('api_client_id'),
            'service_catalog_id' => $request->attributes->get('service_catalog_id'),
            'method' => $request->method(),
            'endpoint' => $request->path(), // or $request->getRequestUri()
            'status_code' => $response->getStatusCode(),
            'ip_address' => $request->ip(),
            'duration_ms' => $duration,
            'user_agent' => $request->header('User-Agent'),
        ]);
    }
}
