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
            $table->boolean('can_customize_mapping')->default(false)->after('status')->comment('Approval-time permission for Diskominfo to set manual mapping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_access_requests', function (Blueprint $table) {
            //
        });
    }
};
