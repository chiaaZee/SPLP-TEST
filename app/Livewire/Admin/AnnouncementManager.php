<?php

namespace App\Livewire\Admin;

use App\Models\Announcement;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementManager extends Component
{
    use WithPagination;

    public $search = '';
    public $announcementId;
    public $title;
    public $content;
    public $type = 'info';
    public $placement = 'modal';
    public $start_date;
    public $end_date;
    public $is_active = true;

    public function mount()
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }
    }

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required',
        'type' => 'required|in:info,warning,danger,success',
        'placement' => 'required|in:modal,banner',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'is_active' => 'boolean'
    ];

    protected $listeners = [
        'deleteConfirmed' => 'deleteAnnouncement'
    ];

    public function render()
    {
        $announcements = Announcement::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.announcement-manager', [
            'announcements' => $announcements
        ])->extends('layouts.contentNavbarLayout')
          ->section('content');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->dispatch('open-modal');
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->announcementId = $announcement->id;
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->type = $announcement->type;
        $this->placement = $announcement->placement;
        $this->start_date = $announcement->start_date ? $announcement->start_date->format('Y-m-d\TH:i') : null;
        $this->end_date = $announcement->end_date ? $announcement->end_date->format('Y-m-d\TH:i') : null;
        $this->is_active = (bool) $announcement->is_active;

        $this->dispatch('open-modal');
    }

    public function store()
    {
        $this->validate();

        Announcement::create([
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'placement' => $this->placement,
            'start_date' => $this->start_date ? \Carbon\Carbon::parse($this->start_date) : null,
            'end_date' => $this->end_date ? \Carbon\Carbon::parse($this->end_date) : null,
            'is_active' => $this->is_active ? 1 : 0,
        ]);

        $this->dispatch('close-modal');
        $this->dispatch('swal:toast', type: 'success', message: 'Pengumuman berhasil dibuat.');
        $this->resetInputFields();
    }

    public function update()
    {
        $this->validate();

        if ($this->announcementId) {
            $announcement = Announcement::find($this->announcementId);
            $announcement->update([
                'title' => $this->title,
                'content' => $this->content,
                'type' => $this->type,
                'placement' => $this->placement,
                'start_date' => $this->start_date ? \Carbon\Carbon::parse($this->start_date) : null,
                'end_date' => $this->end_date ? \Carbon\Carbon::parse($this->end_date) : null,
                'is_active' => $this->is_active ? 1 : 0,
            ]);

            $this->dispatch('close-modal');
            $this->dispatch('swal:toast', type: 'success', message: 'Pengumuman berhasil diperbarui.');
            $this->resetInputFields();
        }
    }

    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm',
            type: 'warning',
            title: 'Hapus Pengumuman?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            id: $id,
            method: 'deleteConfirmed'
        );
    }

    public function deleteAnnouncement($id)
    {
        if($id['id'] ?? false) $id = $id['id']; // Handle array wrapping if happens

        Announcement::find($id)->delete();
        $this->dispatch('swal:toast', type: 'success', message: 'Pengumuman berhasil dihapus.');
    }

    private function resetInputFields()
    {
        $this->title = '';
        $this->content = '';
        $this->type = 'info';
        $this->placement = 'modal';
        $this->start_date = null;
        $this->end_date = null;
        $this->is_active = true;
        $this->announcementId = null;
    }
}
