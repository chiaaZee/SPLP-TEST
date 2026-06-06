<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('service_endpoints', function (Blueprint $table) {
            $table->string('path')->nullable()->after('method')->comment('Gateway path (e.g. /news)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('service_endpoints', function (Blueprint $table) {
            $table->dropColumn('path');
        });
    }
};
