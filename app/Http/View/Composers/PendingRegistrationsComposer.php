<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\ServiceAccessRequest;

class PendingRegistrationsComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Initialize
        $pendingRegistrationCount = 0;
        $recentPendingUsers = [];
        $openTicketsCount = 0;
        $recentOpenTickets = [];
        $pendingAccessRequestsCount = 0;
        $recentAccessRequests = [];

        $user = auth()->user();

        if ($user && $user->hasRole('admin')) {
             // 1. Pending Registrations (Admin Only)
            $pendingRegistrationCount = User::where('status', 'pending')->count();
            if ($pendingRegistrationCount > 0) {
                $recentPendingUsers = User::where('status', 'pending')
                    ->with('agency')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            }

            // 2. Open Tickets (Admin Only)
            $openTicketsCount = SupportTicket::where('status', 'open')->count();
            if ($openTicketsCount > 0) {
                $recentOpenTickets = SupportTicket::where('status', 'open')
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            }

            // 3. Pending Access Requests (Admin Only)
            $pendingAccessRequestsCount = ServiceAccessRequest::where('status', 'pending')->count();
            if ($pendingAccessRequestsCount > 0) {
                $recentAccessRequests = ServiceAccessRequest::where('status', 'pending')
                    ->with(['user', 'serviceCatalog'])
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            }

        } elseif ($user) {
            // 4. User Notifications (Database Notifications)
            // Use unreadNotifications to show badge count

            // We'll store notifications in $recentAccessRequests for view compatibility variable name
            // But value is DatabaseNotification Collection
            $recentAccessRequests = $user->unreadNotifications()->take(5)->get();
            $pendingAccessRequestsCount = $user->unreadNotifications()->count();
        }

        // Total notification count
        $pendingCount = $pendingRegistrationCount + $openTicketsCount + $pendingAccessRequestsCount;

        $view->with('pendingCount', $pendingCount);
        $view->with('pendingRegistrationCount', $pendingRegistrationCount);
        $view->with('recentPendingUsers', $recentPendingUsers);
        $view->with('openTicketsCount', $openTicketsCount);
        $view->with('recentOpenTickets', $recentOpenTickets);
        $view->with('pendingAccessRequestsCount', $pendingAccessRequestsCount);
        $view->with('recentAccessRequests', $recentAccessRequests);
    }
}
