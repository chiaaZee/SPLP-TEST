<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Force update enum to include 'suspended'
        // Using raw statement to bypass any Schema builder limitations
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'pending', 'inactive', 'rejected', 'suspended') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum list (removing 'suspended')
        // WARNING: This could truncate data if any user is currently suspended.
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'pending', 'inactive', 'rejected') DEFAULT 'pending'");
    }
};
