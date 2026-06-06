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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('endpoint_url');
            $table->enum('method', ['GET', 'POST', 'PUT', 'DELETE'])->default('GET');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Combination of agency and slug must be unique for routing logic
            // e.g. /api/dinsos/cek-data (agency=dinsos, service=cek-data)
            $table->unique(['agency_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
