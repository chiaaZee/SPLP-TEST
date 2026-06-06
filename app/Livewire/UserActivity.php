<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use App\Models\ApiClient;

class UserActivity extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function getUserActivityProperty()
    {
        $activities = collect();

        // 1. Service Access Requests
        $requests = \App\Models\ServiceAccessRequest::where('user_id', Auth::id())
            ->with('serviceCatalog')
            ->latest()
            ->take(100)
            ->get()
            ->map(function ($item) {
                return [
                    'title' => 'Mengajukan Akses',
                    'description' => 'Permohonan akses ke layanan: ' . ($item->serviceCatalog->name ?? 'Unknown'),
                    'time' => $item->created_at,
                    'icon' => 'ti ti-file-description',
                    'color' => 'primary',
                ];
            });

        // 2. API Clients
        $clients = ApiClient::where('user_id', Auth::id())
            ->latest()
            ->take(100)
            ->get()
            ->map(function ($item) {
                return [
                    'title' => 'Kelola API',
                    'description' => 'Membuat/Update API Client: ' . $item->name,
                    'time' => $item->created_at, // or updated_at
                    'icon' => 'ti ti-code',
                    'color' => 'info',
                ];
            });

        // 3. User Profile Update (Generic)
        $user = Auth::user();
        if ($user->updated_at->diffInDays(now()) < 30) {
             $activities->push([
                'title' => 'Update Profil',
                'description' => 'Memperbarui data profil akun.',
                'time' => $user->updated_at,
                'icon' => 'ti ti-user-edit',
                'color' => 'warning',
             ]);
        }

        // Merge and Sort
        $merged = $activities->merge($requests)->merge($clients)->sortByDesc('time');

        // Manual Pagination
        $perPage = 10;
        $page = Paginator::resolveCurrentPage() ?: 1;
        $items = $merged->forPage($page, $perPage);

        return new LengthAwarePaginator(
            $items,
            $merged->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    public function render()
    {
        return view('livewire.user-activity', [
            'activities' => $this->userActivity
        ])->extends('layouts.layoutMaster')->section('content');
    }
}
