<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\ServiceAccessRequest;

class TaskTabs extends Component
{
    // Listeners
    protected $listeners = ['update-notifications' => '$refresh'];

    public function render()
    {
        // Counts
        $pendingUsersCount = User::where('status', 'pending')->count();
        $openTicketsCount = SupportTicket::where('status', 'open')->count();
        $pendingRequestsCount = ServiceAccessRequest::where('status', 'pending')->count();

        return view('livewire.admin.task-tabs', [
            'pendingUsersCount' => $pendingUsersCount,
            'openTicketsCount' => $openTicketsCount,
            'pendingRequestsCount' => $pendingRequestsCount
        ]);
    }
}
