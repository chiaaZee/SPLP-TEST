<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'user_id',
        'category_id',
        'name',
        'slug',
        'description',
        'cover_image',
        'status',
        'rejection_reason',
        'uat_document_path',
        'requires_mapping',
        'mapping_api_url',
        'mapping_field',
        'is_public',
        'base_url',
        'target_token',
        'target_token',
        'rate_limit',
        'auth_mode',
    ];

    protected $casts = [
        'status' => 'string',
        'requires_mapping' => 'boolean',
        'is_public' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function endpoints()
    {
        return $this->hasMany(ServiceEndpoint::class);
    }

    public function apiLogs()
    {
        return $this->hasMany(ApiLog::class);
    }

    public function accessRequests()
    {
        return $this->hasMany(ServiceAccessRequest::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Owner / PIC
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getConnectedAgenciesCountAttribute()
    {
        // 1. From Logs (Actual usage users)
        $userIds = \App\Models\ApiLog::where('service_catalog_id', $this->id)
            ->whereBetween('status_code', [200, 299])
            ->distinct()
            ->pluck('user_id');

        $connectedAgencyIds = \App\Models\User::whereIn('id', $userIds)
            ->whereNotNull('agency_id')
            ->distinct()
            ->pluck('agency_id')
            ->toArray();

        // 2. From ApiClients (via Logs)
        $clientIds = \App\Models\ApiLog::where('service_catalog_id', $this->id)
            ->whereBetween('status_code', [200, 299])
            ->whereNotNull('api_client_id')
            ->distinct()
            ->pluck('api_client_id');

        if ($clientIds->isNotEmpty()) {
            $clients = \App\Models\ApiClient::whereIn('id', $clientIds)->get();
            $agencyCodeMap = \App\Models\Agency::where('status', 'active')->pluck('id', 'code')->toArray();

            foreach ($clients as $client) {
                if (!empty($client->mapping_config['skpd_code'])) {
                    $code = $client->mapping_config['skpd_code'];
                    if (isset($agencyCodeMap[$code])) {
                         $connectedAgencyIds[] = $agencyCodeMap[$code];
                    }
                }
            }
        }

        return count(array_unique($connectedAgencyIds));
    }

    public function getHealthStatsAttribute()
    {
        $query = $this->apiLogs()->where('created_at', '>=', now()->subHours(24));

        $total = $query->count();
        $errors = $query->clone()->where('status_code', '>=', 400)->count(); // Clone to avoid mutation if shared, though here new query

        // Optimization: Single query with conditional count?
        // $stats = $this->apiLogs()
        //     ->where('created_at', '>=', now()->subHours(24))
        //     ->selectRaw('count(*) as total, sum(case when status_code >= 400 then 1 else 0 end) as errors')
        //     ->first();
        // $total = $stats->total; $errors = $stats->errors;

        // Using relationship might be cached if loaded, but here we need fresh 24h.
        // Let's stick to clean queries for now or optimize if heavy.
        // Re-using the query builder logic for clarity.

        $errorRate = $total > 0 ? ($errors / $total) * 100 : 0;
        $successRate = 100 - $errorRate;

        $status = 'Excellent';
        $color = 'success';
        $icon = 'activity-heartbeat';

        // Logic re-verified: warning replaced by Issues.

        $avgLatency = round($query->clone()->avg('duration_ms') ?? 0);

        if ($total == 0) {
            $status = 'no data'; // Lowercase as per user request and previous style
            $color = 'secondary';
            $icon = 'help-circle'; // Or any neutral icon
        } else {
            if ($errorRate > 50) {
                $status = 'Critical';
                $color = 'danger';
                $icon = 'alert-triangle';
            } elseif ($errorRate > 10) {
                $status = 'Issues';
                $color = 'warning';
                $icon = 'alert-circle';
            }
        }

        return [
            'total_hits' => $total,
            'error_count' => $errors,
            'error_rate' => round($errorRate, 1),
            'success_rate' => round($successRate, 1),
            'avg_latency' => $avgLatency,
            'status' => $status,
            'color' => $color,
            'icon' => $icon
        ];
    }

    // Scope for filtering services owned by user
    public function scopeOwned($query)
    {
        if(auth()->check() && !auth()->user()->hasRole('admin')){
            return $query->where('user_id', auth()->id())
                         ->orWhere('agency_id', auth()->user()->agency_id);
        }
        return $query;
    }
}
