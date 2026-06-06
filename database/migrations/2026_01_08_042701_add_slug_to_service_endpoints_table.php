<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\ServiceEndpoint;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_endpoints', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name')->comment('Unique slug for friendly URL');
        });

        // Generate slugs for existing endpoints
        // Using 'name' or 'method-path' to generate slug.
        $endpoints = ServiceEndpoint::all();
        foreach ($endpoints as $endpoint) {
            $slug = Str::slug($endpoint->name);
            // Ensure unique
            $originalSlug = $slug;
            $count = 1;
            while (ServiceEndpoint::where('slug', $slug)->where('id', '!=', $endpoint->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $endpoint->slug = $slug;
            $endpoint->save();
        }

        // Now make it not nullable and unique
        Schema::table('service_endpoints', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_endpoints', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
