<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Announcement;

class AnnouncementTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Modal Form Data
    public $announcementId;
    public $title;
    public $content;
    public $type = 'info';
    public $is_active = true;
    public $isEdit = false;

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,danger,success',
            'is_active' => 'boolean',
        ];
    }

    public function create()
    {
        $this->reset(['announcementId', 'title', 'content', 'type', 'is_active', 'isEdit']);
        $this->type = 'info'; // Default
        $this->is_active = true;
        $this->dispatch('open-announcement-modal');
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->announcementId = $id;
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->type = $announcement->type;
        $this->is_active = $announcement->is_active;
        $this->isEdit = true;

        $this->dispatch('open-announcement-modal');
    }

    public function store()
    {
        $this->validate();

        Announcement::updateOrCreate(
            ['id' => $this->announcementId],
            [
                'title' => $this->title,
                'content' => $this->content,
                'type' => $this->type,
                'is_active' => $this->is_active
            ]
        );

        $this->dispatch('close-announcement-modal');
        $this->dispatch('swal:toast', type: 'success', message: 'Pengumuman berhasil disimpan.');
        $this->reset(['announcementId', 'title', 'content', 'type', 'is_active', 'isEdit']);
    }

    public function delete($id)
    {
        Announcement::find($id)?->delete();
        $this->dispatch('swal:toast', type: 'success', message: 'Pengumuman berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $announcement = Announcement::find($id);
        if ($announcement) {
            $announcement->update(['is_active' => !$announcement->is_active]);
            $this->dispatch('swal:toast', type: 'success', message: 'Status pengumuman diperbarui.');
        }
    }

    public function render()
    {
        $announcements = Announcement::query()
            ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.announcement-table', [
            'announcements' => $announcements
        ]);
    }
}
