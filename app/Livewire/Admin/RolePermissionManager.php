<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionManager extends Component
{
    public $permissions = []; // Helper to store available perms
    public $roles = [];      // Helper to store roles
    public $selectedPermissions = []; // State: [role_id => [perm_name, perm_name]]

    public function mount()
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->roles = Role::with('permissions')->get();
        $this->permissions = Permission::all();

        // Initialize state
        foreach ($this->roles as $role) {
            $this->selectedPermissions[$role->name] = $role->permissions->pluck('name')->toArray();
        }
    }

    public function getGroupedPermissionsProperty()
    {
        return $this->permissions->groupBy(function ($item) {
            if (str_starts_with($item->name, 'menu_')) return 'Menu Access';
            if (str_starts_with($item->name, 'manage_')) return 'Management';
            if (str_starts_with($item->name, 'view_')) return 'View Access';
            if (str_starts_with($item->name, 'confirm_')) return 'Workflow';
            if (str_starts_with($item->name, 'use_')) return 'Utility';
            return 'Other';
        });
    }

    public function save()
    {
        try {
            // Re-fetch roles to ensure we have the latest models and avoid hydration issues
            // This is safer than relying on $this->roles property if hydration fails
            $roles = Role::all();

            foreach ($roles as $role) {
                // Get selected perms for this role from state, default to empty
                // Note: selectedPermissions key is role name
                $perms = $this->selectedPermissions[$role->name] ?? [];
                $role->syncPermissions($perms);
            }

            // Standard SweetAlert Toast
            $this->dispatch('swal:toast', [
                'type' => 'success',
                'message' => 'Hak akses berhasil diperbarui.',
            ]);

            // Optional: Re-hydrate properties to reflect changes if needed (though sync doesn't change role list)
            $this->loadData();

        } catch (\Exception $e) {
            $this->dispatch('swal:toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.role-permission-manager', [
            'groupedPermissions' => $this->groupedPermissions
        ])
        ->extends('layouts.layoutMaster')
        ->section('content');
    }
}
