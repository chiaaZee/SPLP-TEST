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
        Schema::table('service_catalogs', function (Blueprint $table) {
            $table->boolean('requires_mapping')->default(false)->after('target_token');
            $table->string('mapping_api_url')->nullable()->after('requires_mapping');
            $table->string('mapping_field')->nullable()->default('skpd_id')->after('mapping_api_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('service_catalogs', function (Blueprint $table) {
            $table->dropColumn(['requires_mapping', 'mapping_api_url', 'mapping_field']);
        });
    }
};
