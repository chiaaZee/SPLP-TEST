<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ServiceCatalog;

class ServiceStatusUpdatedNotification extends Notification
{
    use Queueable;

    public $service;
    public $status;
    public $reason;

    public function __construct(ServiceCatalog $service, $status, $reason = null)
    {
        $this->service = $service;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $statusText = $this->status === 'active' ? 'DISETUJUI' : 'DITOLAK';
        $message = "Pengajuan layanan '{$this->service->name}' telah {$statusText}.";

        if ($this->status === 'rejected' && $this->reason) {
            $message .= " Alasan: {$this->reason}";
        }

        return [
            'type' => 'service_status',
            'title' => "Status Layanan: {$statusText}",
            'message' => $message,
            'service_id' => $this->service->id,
            'status' => $this->status,
            'url' => route('user.my-services.index'), // Sesuaikan route user
            'icon' => $this->status === 'active' ? 'ti ti-check' : 'ti ti-x'
        ];
    }
}
