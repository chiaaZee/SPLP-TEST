<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function services()
    {
        return $this->hasMany(ServiceCatalog::class, 'category_id');
    }
}
