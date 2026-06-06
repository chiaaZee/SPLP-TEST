<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'email',
        'phone',
        'facebook',
        'twitter',
        'instagram',
        'app_version',
        'response_time',
        'work_hours',
        'youtube',
        'google_map',
    ];
}
