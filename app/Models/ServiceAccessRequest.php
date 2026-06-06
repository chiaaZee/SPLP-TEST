<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAccessRequest extends Model
{
    protected $fillable = [
        'user_id',
        'service_catalog_id',
        'reason',
        'attachment',
        'status',
        'admin_note',
        'owner_note',
        'owner_approved_at',
        'admin_approved_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceCatalog()
    {
        return $this->belongsTo(ServiceCatalog::class);
    }
}
