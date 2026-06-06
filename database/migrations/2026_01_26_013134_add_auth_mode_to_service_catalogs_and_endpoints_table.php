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
        Schema::table('service_catalogs', function (Blueprint $table) {
            // Fix: Ensure is_public exists (it was missing in previous migrations but used in Model)
            if (!Schema::hasColumn('service_catalogs', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('status');
            }

            // Add auth_mode safely
            if (!Schema::hasColumn('service_catalogs', 'auth_mode')) {
                $table->string('auth_mode', 20)->default('required')->after('status'); // required, none
            }
        });

        Schema::table('service_endpoints', function (Blueprint $table) {
            if (!Schema::hasColumn('service_endpoints', 'auth_mode')) {
                $table->string('auth_mode', 20)->default('required')->after('is_public'); // required, none
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_catalogs', function (Blueprint $table) {
            if (Schema::hasColumn('service_catalogs', 'auth_mode')) {
                $table->dropColumn('auth_mode');
            }
            // We do not drop is_public as it might have been intended to be there
        });

        Schema::table('service_endpoints', function (Blueprint $table) {
            if (Schema::hasColumn('service_endpoints', 'auth_mode')) {
                $table->dropColumn('auth_mode');
            }
        });
    }
};
