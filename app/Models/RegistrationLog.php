<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'action',
        'user_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'user_data' => 'array',
    ];
}
