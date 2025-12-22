<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is no longer needed as the conversion has already been done
        // by the 2025_12_17_convert_staff_id migration
        // Keeping this as a no-op to maintain migration history
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert staff_id back to bigInteger (auto-increment)
        Schema::table('staff', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique(['staff_id']);
        });

        // Restore old numeric IDs if possible
        DB::statement('ALTER TABLE staff MODIFY staff_id BIGINT UNSIGNED AUTO_INCREMENT');

        // If you need to restore old values, you'd need to keep track of them
        // This is a destructive operation, so be careful
    }
};
