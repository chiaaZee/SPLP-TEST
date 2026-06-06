<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_access_requests', function (Blueprint $table) {
            $table->text('owner_note')->nullable()->after('admin_note');
            $table->timestamp('owner_approved_at')->nullable()->after('updated_at');
            $table->timestamp('admin_approved_at')->nullable()->after('owner_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_access_requests', function (Blueprint $table) {
            $table->dropColumn(['owner_note', 'owner_approved_at', 'admin_approved_at']);
        });
    }
};
