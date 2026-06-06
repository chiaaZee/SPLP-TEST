<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'api_client_id',
        'service_catalog_id',
        'method',
        'endpoint',
        'status_code',
        'ip_address',
        'duration_ms',
        'user_agent',
        'request_header',
        'request_body',
        'response_body'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(ApiClient::class, 'api_client_id');
    }

    public function catalog()
    {
        return $this->belongsTo(ServiceCatalog::class, 'service_catalog_id');
    }
}
