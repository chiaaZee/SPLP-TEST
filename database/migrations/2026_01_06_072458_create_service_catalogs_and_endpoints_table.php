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
        // Drop old table if exists
        Schema::dropIfExists('services');

        Schema::create('service_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->timestamps();

            $table->unique(['agency_id', 'slug']);
        });

        Schema::create('service_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_catalog_id')->constrained('service_catalogs')->onDelete('cascade');
            $table->string('name'); // e.g. "Get User List"
            $table->enum('method', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'])->default('GET');
            $table->string('url'); // Real endpoint
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_endpoints');
        Schema::dropIfExists('service_catalogs');
    }
};
