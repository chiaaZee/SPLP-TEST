<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'rejected' to the enum values
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'pending', 'inactive', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values (careful as this might truncate 'rejected' data)
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'pending', 'inactive') DEFAULT 'pending'");
    }
};
