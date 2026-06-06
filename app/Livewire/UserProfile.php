<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Models\ApiClient;
use App\Models\ApiLog;

class UserProfile extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $phone;
    public $nip;
    public $jabatan;
    public $photo;
    public $existingPhoto;

    // Password state
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->nip = $user->nip;
        $this->jabatan = $user->jabatan;
        $this->existingPhoto = $user->profile_photo_path;
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:1024', // 1MB Max
        ]);
    }

    public function updateProfile()
    {
        $user = Auth::user();

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20', // WhatsApp
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'jabatan' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($this->photo) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $this->photo->store('profile-photos', 'public');
        }

        $user->forceFill([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nip' => $this->nip,
            'jabatan' => $this->jabatan,
        ])->save();

        // Refresh existing photo to show new upload instantly without page reload issue if photo property reset
        $this->existingPhoto = $user->profile_photo_path;
        $this->photo = null;

        $this->dispatch('swal:toast', type: 'success', message: 'Profil berhasil diperbarui.');

        // Refresh component to update Navbar avatar if possible, or emit event
        $this->dispatch('refresh-navigation-menu');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'string', Password::default(), 'confirmed'],
        ]);

        $user = Auth::user();

        $user->forceFill([
            'password' => Hash::make($this->new_password),
        ])->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch('swal:toast', type: 'success', message: 'Password berhasil diubah.');
    }

    public function getUserActivityProperty()
    {
        $activities = collect();

        // 1. Service Access Requests
        $requests = \App\Models\ServiceAccessRequest::where('user_id', Auth::id())
            ->with('serviceCatalog')
            ->latest()
            ->take(5)
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
            ->take(5)
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
        // Since we don't have a log table for profile updates, we check updated_at of the user
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

        // Merge all
        $merged = $activities->merge($requests)->merge($clients)->sortByDesc('time')->take(10);

        // Add Current Session with Device Info
        $ua = request()->userAgent();
        $device = 'Unknown Device';
        $platform = 'Unknown OS';

        if (preg_match('/windows|win32/i', $ua)) $platform = 'Windows';
        elseif (preg_match('/macintosh|mac os x/i', $ua)) $platform = 'Mac OS';
        elseif (preg_match('/linux/i', $ua)) $platform = 'Linux';
        elseif (preg_match('/android/i', $ua)) $platform = 'Android';
        elseif (preg_match('/iphone|ipad|ipod/i', $ua)) $platform = 'iOS';

        if (preg_match('/MSIE|Trident/i', $ua)) $device = 'Internet Explorer';
        elseif (preg_match('/Firefox/i', $ua)) $device = 'Firefox';
        elseif (preg_match('/Chrome/i', $ua)) $device = 'Chrome';
        elseif (preg_match('/Safari/i', $ua)) $device = 'Safari';
        elseif (preg_match('/Opera|OPR/i', $ua)) $device = 'Opera';

        $merged->prepend([ // Add to top
            'title' => 'Sesi Aktif Saat Ini',
            'description' => "Login di perangkat $platform - $device",
            'time' => now(),
            'icon' => 'ti ti-device-desktop',
            'color' => 'success',
        ]);

        return $merged->take(5); // 5 items including session
    }

    public function render()
    {
        return view('livewire.user-profile')->extends('layouts.layoutMaster')->section('content');
    }
}
