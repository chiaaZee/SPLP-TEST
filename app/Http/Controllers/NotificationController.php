<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\SupportTicket;
use App\Models\ServiceAccessRequest;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            // Admin View: Task Dashboard
            $pendingUsers = User::where('status', 'pending')->with('agency')->latest()->get();
            $openTickets = SupportTicket::where('status', 'open')->with('user')->latest()->get();
            $pendingRequests = ServiceAccessRequest::where('status', 'pending_admin')->with(['user', 'serviceCatalog'])->latest()->get();

            return view('content.notifications.index_admin', compact('pendingUsers', 'openTickets', 'pendingRequests'));
        } else {
            // User View: Notification History
            $notifications = $user->notifications()->paginate(10);
            return view('content.notifications.index_user', compact('notifications'));
        }
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();

            // Redirect to Notifications Index as requested
            return redirect()->route('notifications.index');
        }

        return back();
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}
