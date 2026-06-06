<?php

namespace App\Livewire\User;

use App\Models\ServiceAccessRequest;
use App\Models\ServiceCatalog;
use Livewire\Component;
use Livewire\WithPagination;

class IncomingRequestTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $rejectingId = null;
    public $rejectionReason = '';

    public function render()
    {
        $userId = auth()->id();

        // Get IDs of services owned by this user
        $myServiceIds = ServiceCatalog::where('user_id', $userId)->pluck('id');

        $requests = ServiceAccessRequest::whereIn('service_catalog_id', $myServiceIds)
            ->where('status', 'pending_owner')
            ->with(['user.agency', 'serviceCatalog'])
            ->latest()
            ->paginate(10);

        return view('livewire.user.incoming-request-table', [
            'requests' => $requests
        ])->extends('layouts.layoutMaster')->section('content');
    }

    public function approve($id)
    {
        $request = ServiceAccessRequest::find($id);

        if ($request && $request->serviceCatalog->user_id == auth()->id()) {
            $request->update([
                'status' => 'pending_admin', // Forward to SPLPD Admin
                'owner_approved_at' => now()
            ]);

            $this->dispatch('swal:toast', type: 'success', message: 'Permintaan disetujui, menunggu verifikasi Admin SPLPD.');
        }
    }

    public function confirmReject($id)
    {
        $this->rejectingId = $id;
        $this->dispatch('open-reject-modal');
    }

    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|min:5'
        ]);

        $request = ServiceAccessRequest::find($this->rejectingId);

        if ($request && $request->serviceCatalog->user_id == auth()->id()) {
            $request->update([
                'status' => 'rejected',
                'owner_note' => $this->rejectionReason
            ]);

            // Notify Requester (Optional, simplified for now)
            // $request->user->notify(...);

            $this->dispatch('close-reject-modal');
            $this->dispatch('swal:toast', type: 'success', message: 'Permintaan ditolak.');
            $this->reset(['rejectingId', 'rejectionReason']);
        }
    }
}
