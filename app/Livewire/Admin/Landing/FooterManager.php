<?php

namespace App\Livewire\Admin\Landing;

use Livewire\Component;
use App\Models\Footer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FooterManager extends Component
{
    use AuthorizesRequests;

    public $address;
    public $email;
    public $phone;
    public $facebook;
    public $twitter;
    public $instagram;
    public $app_version;
    public $response_time;
    public $work_hours;
    public $youtube;
    public $google_map;

    public function mount()
    {
        // Ensure user has permission
        if (!auth()->user()->can('manage_landing_page')) {
            abort(403, 'Unauthorized. Permission "manage_landing_page" is required.');
        }

        $footer = Footer::first();
        if ($footer) {
            $this->address = $footer->address;
            $this->email = $footer->email;
            $this->phone = $footer->phone;
            $this->facebook = $footer->facebook;
            $this->twitter = $footer->twitter;
            $this->instagram = $footer->instagram;
            $this->app_version = $footer->app_version;
            $this->response_time = $footer->response_time;
            $this->work_hours = $footer->work_hours;
            $this->youtube = $footer->youtube;
            $this->google_map = $footer->google_map;
        }
    }

    public function save()
    {
        $this->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'app_version' => 'nullable|string|max:10',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'response_time' => 'nullable|string',
            'work_hours' => 'nullable|string',
            'youtube' => 'nullable|string',
            'google_map' => 'nullable|string',
        ]);

        try {
            $footer = Footer::first();

            $data = [
                'address' => $this->address,
                'email' => $this->email,
                'phone' => $this->phone,
                'facebook' => $this->facebook,
                'twitter' => $this->twitter,
                'instagram' => $this->instagram,
                'app_version' => $this->app_version,
                'response_time' => $this->response_time,
                'work_hours' => $this->work_hours,
                'youtube' => $this->youtube,
                'google_map' => $this->google_map,
            ];

            if ($footer) {
                $footer->update($data);
            } else {
                Footer::create($data);
            }

            $this->dispatch('swal:toast', [
                'type' => 'success',
                'message' => 'Pengaturan Footer berhasil disimpan.',
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal:toast', [
                'type' => 'error',
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.landing.footer-manager')
            ->extends('layouts.layoutMaster')
            ->section('content');
    }
}
