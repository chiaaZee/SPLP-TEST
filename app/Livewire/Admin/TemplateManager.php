<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class TemplateManager extends Component
{
    use WithFileUploads;

    public $templateFile;
    public $lastUpdated;

    public function mount()
    {
        $this->updateLastModified();
    }

    public function updateLastModified()
    {
        $path = public_path('templates/UAT_TEMPLATE.docx');
        if (file_exists($path)) {
            $this->lastUpdated = date('d M Y H:i', filemtime($path));
        } else {
            $this->lastUpdated = 'Belum ada file';
        }
    }

    public function save()
    {
        $this->validate([
            'templateFile' => 'required|file|mimes:doc,docx,pdf|max:10240', // 10MB
        ]);

        $path = public_path('templates');

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        // Overwrite existing file
        $this->templateFile->move($path, 'UAT_TEMPLATE.docx');

        $this->updateLastModified();
        $this->reset('templateFile');

        $this->dispatch('show-toast', type: 'success', message: 'Template UAT berhasil diperbarui!');
    }

    public function downloadCurrent()
    {
        $path = public_path('templates/UAT_TEMPLATE.docx');
        if (file_exists($path)) {
            return response()->download($path);
        }
        $this->dispatch('show-toast', type: 'error', message: 'File template tidak ditemukan.');
    }

    public function render()
    {
        return view('livewire.admin.template-manager');
    }
}
