<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_catalogs', function (Blueprint $table) {
            $table->string('base_url')->nullable()->after('status');
            $table->integer('rate_limit')->default(60)->after('base_url'); // Requests per minute
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_catalogs', function (Blueprint $table) {
            $table->dropColumn(['base_url', 'rate_limit']);
        });
    }
};
