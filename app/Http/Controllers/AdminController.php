<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agency;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function confirmRegistrations()
    {
        // Fetch users with pending status
        $pendingUsers = User::where('status', 'pending')->with('agency')->get();
        return view('content.admin.confirm-registrations', compact('pendingUsers'));
    }

    public function approveUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        // If user belongs to a pending agency, approve it too (or handle separately)
        if ($user->agency && $user->agency->status === 'pending') {
            $user->agency->status = 'active';
            $user->agency->save();
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User berhasil disetujui.']);
        }

        return redirect()->back()->with('success', 'User approved successfully.');
    }

    public function rejectUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Log the rejection
        \App\Models\RegistrationLog::create([
            'email' => $user->email,
            'name' => $user->name,
            'action' => 'rejected',
            'user_data' => $user->load('agency')->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Set status to rejected so user can see notification on login
        $user->status = 'rejected';
        $user->save();


        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User ditolak. Status diubah menjadi rejected.']);
        }

        return redirect()->back()->with('success', 'User rejected.');
    }

    public function rejectedRegistrations()
    {
        return view('content.admin.rejected-registrations');
    }
    public function serviceVerification()
    {
        return view('content.admin.service-verification.index');
    }

    public function templateManager()
    {
        return view('content.admin.template-manager.index');
    }
}
