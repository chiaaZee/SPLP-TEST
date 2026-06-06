<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Roles
        $roles = ['admin', 'dinas', 'user'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Migrate existing users from 'role' column to Spatie Roles
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role) {
                // Ensure the role exists directly (should match the created ones)
                // Normalize case if needed, but 'admin'/'dinas'/'user' are lowercase in DB
                $user->assignRole($user->role);
            }
        }

        // 3. Create Permissions
        $permissions = [
            'manage_users',
            'manage_agencies',
            'manage_catalogs',
            'view_catalogs',
            'confirm_registrations',

            // Menu Permissions
            'menu_admin_area',
            'menu_master_data',
            'menu_user_management',
            'menu_dashboard',

            // Feature Permissions
            'manage_announcements',
            'manage_tickets',
            'view_documentation',
            'manage_api_keys',
            'view_api_logs',
            'use_code_generator',
            'manage_landing_page', // Landing Page Settings
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 4. Assign Permissions to Roles

        // Admin: All permissions
        $admin = Role::findByName('admin');
        $admin->givePermissionTo(Permission::all());

        // Dinas: Catalogs + Dashboard
        $dinas = Role::findByName('dinas');
        $dinas->syncPermissions([
            'view_catalogs',
            // 'manage_catalogs', // Removed per user request
            'menu_dashboard',
            // New Permissions
            'view_documentation',
            'manage_api_keys',
            'view_api_logs',
            // 'menu_master_data', // REMOVED: Should not see Master Data
        ]);

        // User: View Catalogs + Dashboard
        $userRole = Role::findByName('user');
        $userRole->syncPermissions([
            'view_catalogs',
            'menu_dashboard',
            // New Permissions
            'view_documentation',
            'manage_api_keys',
            'view_api_logs',
            // 'menu_master_data', // REMOVED: Should not see Master Data
        ]);
    }
}
