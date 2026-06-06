<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessRequestUpdated extends Notification
{
    use Queueable;

    public $accessRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct($accessRequest)
    {
        $this->accessRequest = $accessRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->accessRequest->id,
            'status' => $this->accessRequest->status,
            'catalog_name' => $this->accessRequest->serviceCatalog->name ?? 'Unknown Service',
            'catalog_slug' => $this->accessRequest->serviceCatalog->slug ?? '#',
            'message' => 'Permintaan akses Anda telah ' . ($this->accessRequest->status == 'approved' ? 'disetujui' : 'ditolak') . '.',
        ];
    }
}
