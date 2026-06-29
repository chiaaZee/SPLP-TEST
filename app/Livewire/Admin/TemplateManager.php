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
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

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
        $targetFile = $path . '/UAT_TEMPLATE.docx';

        try {
            if (!file_exists($path)) {
                if (!mkdir($path, 0755, true) && !is_dir($path)) {
                    throw new \Exception("Gagal membuat direktori 'templates'. Periksa izin folder 'public'.");
                }
            }

            if (!is_writable($path)) {
                throw new \Exception("Direktori 'templates' tidak dapat ditulis. Periksa izin folder di server.");
            }

            if (file_exists($targetFile) && !is_writable($targetFile)) {
                throw new \Exception("File template yang ada tidak dapat ditimpa. Periksa izin file 'UAT_TEMPLATE.docx' di server.");
            }

            // Alternatif pemindahan file yang lebih aman (copy + unlink) jika move() gagal
            $tempPath = $this->templateFile->getRealPath();
            if (copy($tempPath, $targetFile)) {
                unlink($tempPath);
            } else {
                // Fallback menggunakan method move internal bawaan Laravel/Symfony jika copy gagal
                $this->templateFile->move($path, 'UAT_TEMPLATE.docx');
            }

            $this->updateLastModified();
            $this->reset('templateFile');

            $this->dispatch('show-toast', type: 'success', message: 'Template UAT berhasil diperbarui!');
        } catch (\Throwable $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Gagal memperbarui template: ' . $e->getMessage());
        }
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
