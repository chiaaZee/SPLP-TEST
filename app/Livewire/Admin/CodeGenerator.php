<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Str;

class CodeGenerator extends Component
{
    public $generatedCode = '';

    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    public function mount()
    {
        if (!auth()->user()->can('use_code_generator')) {
             abort(403, 'Unauthorized. Permission "use_code_generator" is required.');
        }
    }

    public function generate()
    {
        // Format: SPLPD- + SHA256 Hash
        // Total Length: 6 (SPLPD-) + 64 (SHA256) = 70 characters
        $hash = hash('sha256', Str::random(60) . time());
        $this->generatedCode = 'SPLPD-' . $hash;
    }

    public function render()
    {
        return view('livewire.admin.code-generator')
            ->extends('layouts.layoutMaster')
            ->section('content');
    }
}
