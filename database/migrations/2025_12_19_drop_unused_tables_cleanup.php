<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Drop all unused tables - keep only essential tables
     */
    public function up(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop unused/redundant tables (AttendanceReport tables are not used in the system)
        $unusedTables = [
            'attendance_reports',
            'attendance_report_details',
            'users',  // Laravel default, not used
            'password_resets',  // Laravel default, not used
            'failed_jobs',  // Laravel default, not used
            'jobs',  // Laravel default, not used
        ];

        foreach ($unusedTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a cleanup migration - no reverse needed
        // If needed, restore from backup
    }
};
