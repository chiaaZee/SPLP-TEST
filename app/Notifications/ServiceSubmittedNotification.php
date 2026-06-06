<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ServiceCatalog;

class ServiceSubmittedNotification extends Notification
{
    use Queueable;

    public $service;

    public function __construct(ServiceCatalog $service)
    {
        $this->service = $service;
    }

    public function via($notifiable)
    {
        return ['database']; // Fokus ke Database dulu sesuai request
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'service_submission',
            'title' => 'Pengajuan Layanan Baru',
            'message' => "Layanan '{$this->service->name}' diajukan oleh {$this->service->user->name} ({$this->service->agency->name}).",
            'service_id' => $this->service->id,
            'url' => route('admin.service-verification'), // Sesuaikan route admin
            'icon' => 'ti ti-file-plus' // Icon untuk UI
        ];
    }
}
