<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        // Group permissions by category if possible, for now just list all
        // Or assume naming convention 'manage_users', 'menu_dashboard'
        $permissions = Permission::all();

        // Group permissions explicitly for clearer UI
        $groupedPermissions = $permissions->groupBy(function ($item) {
            if (str_starts_with($item->name, 'menu_'))
                return 'Menu Access';
            if (str_starts_with($item->name, 'manage_'))
                return 'Management';
            if (str_starts_with($item->name, 'view_'))
                return 'View Access';
            if (str_starts_with($item->name, 'confirm_'))
                return 'Workflow';
            return 'Other';
        });

        return view('content.admin.roles.index', compact('roles', 'groupedPermissions'));
    }

    public function update(Request $request)
    {
        $input = $request->except(['_token']);
        $roles = Role::all();

        foreach ($roles as $role) {
            // permissions[role_name] = [perm1, perm2]
            // Input format: name="permissions[admin][]"

            $perms = $request->input('permissions.' . $role->name, []);
            $role->syncPermissions($perms);
        }

        return redirect()->route('roles-permissions.index')->with('success', 'Hak akses berhasil diperbarui!');
    }
}
