<?php

namespace App\Livewire\User;

use App\Models\ServiceAccessRequest;
use Livewire\Component;
use Livewire\WithPagination;

class AccessRequestList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        // Self-healing: Correct legacy/mismatched status values to proper workflows
        try {
            $pendingRequests = ServiceAccessRequest::where('status', 'pending')->get();
            foreach ($pendingRequests as $req) {
                $catalog = $req->serviceCatalog;
                if ($catalog) {
                    $owner = $catalog->user;
                    $hasDinasOwner = $owner && $owner->role !== 'admin';
                    $req->update([
                        'status' => $hasDinasOwner ? 'pending_owner' : 'pending_admin'
                    ]);
                }
            }

            $mismatchedRequests = ServiceAccessRequest::where('status', 'pending_owner')
                ->whereHas('serviceCatalog', function($query) {
                    $query->whereNull('user_id')
                          ->orWhereHas('user', function($q) {
                              $q->where('role', 'admin');
                          });
                })
                ->get();

            foreach ($mismatchedRequests as $req) {
                $req->update(['status' => 'pending_admin']);
            }
        } catch (\Exception $e) {
            // Ignore errors
        }

        $requests = ServiceAccessRequest::where('user_id', auth()->id())
            ->with(['serviceCatalog.agency']) // Eager load service and its agency (if relationship exists) or user.agency
            ->latest()
            ->paginate(10);

        return view('livewire.user.access-request-list', [
            'requests' => $requests
        ])->extends('layouts.layoutMaster')->section('content');
    }
}
