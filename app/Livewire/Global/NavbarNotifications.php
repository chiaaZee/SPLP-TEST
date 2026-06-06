<?php

namespace App\Livewire\Global;

use Livewire\Component;
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\ServiceAccessRequest;
use Illuminate\Support\Facades\Auth;

class NavbarNotifications extends Component
{
    // Listen for updates
    protected $listeners = ['update-notifications' => '$refresh'];

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return view('livewire.global.navbar-notifications', ['pendingCount' => 0]);
        }

        // 1. Badge Count: Only Unread System Notifications
        // This satisfies "Mark all as read makes count disappear"
        $pendingCount = $user->unreadNotifications()->count();

        // 2. Fetch Data Sources
        $notifications = $user->notifications()->latest()->take(10)->get();

        $merged = collect($notifications);

        if ($user->hasRole('admin')) {
            // Fetch Pending Tasks
            $pendingUsers = User::where('status', 'pending')
                ->with('agency')
                ->latest()
                ->take(10)
                ->get();

            $openTickets = SupportTicket::where('status', 'open')
                ->with('user')
                ->latest()
                ->take(10)
                ->get();

            $pendingRequests = ServiceAccessRequest::where('status', 'pending')
                ->with(['user', 'serviceCatalog'])
                ->latest()
                ->take(10)
                ->get();

            // Merge Admin Tasks
            $merged = $merged->merge($pendingUsers)
                             ->merge($openTickets)
                             ->merge($pendingRequests);
        } else {
             // For non-admin, include their generic Access Requests status updates if needed
             // But usually those come via DatabaseNotifications now.
             // If there's a separate list needed, add here.
             // Current logic uses Notifications for User Access Requests updates.
        }

        // 3. Sort Chronologically (Newest First)
        $allNotifications = $merged->sortByDesc('created_at')->values()->take(20);

        return view('livewire.global.navbar-notifications', [
            'pendingCount' => $pendingCount,
            'allNotifications' => $allNotifications
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('update-notifications'); // Local update

            // Optional: Redirect if needed, or just stay to act as "mark read"
            // For logic consistency with "pindah halaman" request:
            if(isset($notification->data['catalog_slug'])) {
                 // If specific action, redirect? User said "klik ke halaman notif saja".
                 // So maybe just redirect to index?
                 return redirect()->route('notifications.index');
            }
            return redirect()->route('notifications.index');
        }
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('update-notifications');
    }
}
