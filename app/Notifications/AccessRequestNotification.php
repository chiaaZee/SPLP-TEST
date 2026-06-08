<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ServiceAccessRequest;

class AccessRequestNotification extends Notification
{
    use Queueable;

    public $accessRequest;
    public $type; // 'new_request', 'owner_approved', 'final_approval', 'rejected'

    public function __construct(ServiceAccessRequest $accessRequest, $type)
    {
        $this->accessRequest = $accessRequest;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $serviceName = $this->accessRequest->serviceCatalog->name;
        $requesterName = $this->accessRequest->user->name;
        $agencyName = $this->accessRequest->user->agency->name ?? 'Instansi Umum';

        switch ($this->type) {
            case 'new_request':
                // To Owner
                return [
                    'type' => 'access_request',
                    'title' => 'Permintaan Akses Baru',
                    'message' => "{$requesterName} ({$agencyName}) meminta akses ke layanan '{$serviceName}'.",
                    'url' => route('user.my-services.show', $this->accessRequest->serviceCatalog->slug), // Link to Service Detail
                    'icon' => 'ti ti-user-plus'
                ];
            case 'new_request_admin':
                // To Admin directly
                return [
                    'type' => 'access_request_admin',
                    'title' => 'Permintaan Akses Baru',
                    'message' => "{$requesterName} ({$agencyName}) meminta akses ke layanan '{$serviceName}'.",
                    'url' => route('admin.access-requests.index'), // Link to Admin List
                    'icon' => 'ti ti-user-plus'
                ];
            case 'owner_approved':
                // To Admin
                return [
                    'type' => 'access_request_admin',
                    'title' => 'Akses Disetujui Pemilik',
                    'message' => "Pemilik layanan menyetujui akses '{$serviceName}' untuk {$requesterName}. Menunggu persetujuan Admin.",
                    'url' => route('admin.access-requests.index'), // Link to Admin List
                    'icon' => 'ti ti-checkbox'
                ];
            case 'final_approval':
                // To Requester
                return [
                    'type' => 'access_approved',
                    'title' => 'Akses Diterima',
                    'message' => "Permintaan akses Anda untuk layanan '{$serviceName}' telah disetujui sepenuhnya.",
                    'url' => route('service-catalogs.show', $this->accessRequest->serviceCatalog->slug),
                    'icon' => 'ti ti-check'
                ];
             case 'rejected':
                // To Requester
                return [
                    'type' => 'access_rejected',
                    'title' => 'Akses Ditolak',
                    'message' => "Permintaan akses Anda untuk layanan '{$serviceName}' ditolak.",
                    'url' => route('service-catalogs.show', $this->accessRequest->serviceCatalog->slug),
                    'icon' => 'ti ti-x'
                ];
        }
    }
}
