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
            $table->foreignId('user_id')->nullable()->after('agency_id')->constrained('users')->onDelete('set null');
            // 'active' is default for backward compatibility, but UI logic will use 'pending' for proposals.
            // Existing ones remain 'active'.
            // Note: 'status' column ALREADY EXISTS in create_service_catalogs_and_endpoints_table.php (enum: active, inactive, draft).
            // We need to modify the Enum to include 'pending' & 'rejected'.
            // Laravel doesn't support changing ENUM values easily in migration without raw SQL or Doctrine.
            // Since this is dev, we can potentially raw modify or just add the missing values.

            // Re-defining the column to add new enum values using raw statement for MySQL
            // Or simpler in dev: just drop and re-add if data allows. But let's try safely.
            // Actually, we can just use DB::statement for enum modification.

            $table->text('rejection_reason')->nullable()->after('status');
        });

        // Update Enum values
        if (\DB::getDriverName() !== 'sqlite') {
            \DB::statement("ALTER TABLE service_catalogs MODIFY COLUMN status ENUM('active', 'inactive', 'draft', 'pending', 'rejected') DEFAULT 'active'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_catalogs', function (Blueprint $table) {
             $table->dropForeign(['user_id']);
             $table->dropColumn('user_id');
             $table->dropColumn('rejection_reason');
        });

        // Revert Enum is tricky if data exists with new values, but for down:
        if (\DB::getDriverName() !== 'sqlite') {
            \DB::statement("ALTER TABLE service_catalogs MODIFY COLUMN status ENUM('active', 'inactive', 'draft') DEFAULT 'active'");
        }
    }
};
