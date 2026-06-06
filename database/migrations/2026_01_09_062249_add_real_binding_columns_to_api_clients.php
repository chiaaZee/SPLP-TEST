<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->foreignId('service_catalog_id')->nullable()->after('status')->constrained('service_catalogs')->onDelete('set null');
            $table->json('mapping_config')->nullable()->after('service_catalog_id');
        });
    }

    public function down()
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->dropForeign(['service_catalog_id']);
            $table->dropColumn(['service_catalog_id', 'mapping_config']);
        });
    }
};
