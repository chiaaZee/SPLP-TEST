<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'priority',
        'status',
        'admin_reply',
        'replied_at'
    ];

    protected $casts = [
        'replied_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPriorityBadgeAttribute()
    {
        return match ($this->priority) {
            'low' => '<span class="badge bg-label-secondary">Rendah</span>',
            'medium' => '<span class="badge bg-label-warning">Sedang</span>',
            'high' => '<span class="badge bg-label-danger">Tinggi</span>',
            default => '<span class="badge bg-label-secondary">-</span>'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'open' => '<span class="badge bg-label-primary">Baru</span>',
            'in_progress' => '<span class="badge bg-label-info">Diproses</span>',
            'resolved' => '<span class="badge bg-label-success">Selesai</span>',
            'closed' => '<span class="badge bg-label-dark">Ditutup</span>',
            default => '<span class="badge bg-label-secondary">-</span>'
        };
    }
}
