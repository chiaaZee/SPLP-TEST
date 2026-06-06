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
        Schema::table('api_logs', function (Blueprint $table) {
            $table->longText('request_header')->nullable()->after('user_agent');
            $table->longText('request_body')->nullable()->after('request_header');
            $table->longText('response_body')->nullable()->after('request_body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_logs', function (Blueprint $table) {
            $table->dropColumn(['request_header', 'request_body', 'response_body']);
        });
    }
};
