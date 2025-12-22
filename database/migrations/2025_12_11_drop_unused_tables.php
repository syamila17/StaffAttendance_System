<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - DROP UNUSED TABLES
     * These tables are no longer used in the current system
     */
    public function up(): void
    {
        // Disable foreign key checks to allow table drops
        Schema::disableForeignKeyConstraints();
        
        // Drop child tables first (before parent tables)
        Schema::dropIfExists('attendance_report_details');
        Schema::dropIfExists('attendance_reports');
        
        // Drop other tables
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        // Note: These tables cannot be restored from this rollback
        // You would need to create them manually if needed
    }
};
