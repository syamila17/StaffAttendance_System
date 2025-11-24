<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add foreign key constraints to staff table for department and team
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // Add department_id column
            $table->unsignedBigInteger('department_id')->nullable()->after('team_id');
            
            // Add foreign keys
            $table->foreign('team_id')->references('team_id')->on('teams')->onDelete('set null');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
