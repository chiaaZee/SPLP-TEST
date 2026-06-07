<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'syafiah@mail.com'],
            [
                'name' => 'Administrator',
                'password' => 'anjay123', // Will be hashed by model cast
                'role' => 'admin',
            ]
        );

        $this->call([
            RolePermissionSeeder::class,
            AgencySeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
