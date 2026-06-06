<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'api_key',
        'secret_key',
        'status',
        'last_used_at',
        'service_catalog_id',
        'mapping_config',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'mapping_config' => 'array',
    ];

    protected $hidden = [
        'secret_key', // Always hide secret key in JSON responses
    ];

    public static function generateCredentials()
    {
        return [
            'api_key' => 'SPL-' . strtoupper(Str::random(16)),
            'secret_key' => 'SEC-' . Str::random(40) . base64_encode(random_bytes(10)),
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceCatalog()
    {
        return $this->belongsTo(ServiceCatalog::class);
    }
}
