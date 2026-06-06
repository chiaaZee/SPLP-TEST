<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEndpoint extends Model
{
    use HasFactory;

    protected $fillable = ['service_catalog_id', 'method', 'path', 'url', 'name', 'slug', 'description', 'request_body', 'is_public', 'auth_mode'];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function catalog()
    {
        return $this->belongsTo(ServiceCatalog::class, 'service_catalog_id');
    }
}
