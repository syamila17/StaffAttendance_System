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
        // For MySQL, we need to modify the enum to include 'half day'
        DB::statement("ALTER TABLE `attendance` CHANGE `status` `status` ENUM('present', 'absent', 'late', 'leave', 'half day') DEFAULT 'absent'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum without 'half day'
        DB::statement("ALTER TABLE `attendance` CHANGE `status` `status` ENUM('present', 'absent', 'late', 'leave') DEFAULT 'absent'");
    }
};
