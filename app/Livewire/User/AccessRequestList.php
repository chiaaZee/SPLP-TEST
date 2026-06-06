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
        $requests = ServiceAccessRequest::where('user_id', auth()->id())
            ->with(['serviceCatalog.agency']) // Eager load service and its agency (if relationship exists) or user.agency
            ->latest()
            ->paginate(10);

        return view('livewire.user.access-request-list', [
            'requests' => $requests
        ])->extends('layouts.layoutMaster')->section('content');
    }
}
