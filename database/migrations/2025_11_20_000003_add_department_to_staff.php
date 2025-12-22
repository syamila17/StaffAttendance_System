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
            // Add department_id column only if it doesn't exist
            if (!Schema::hasColumn('staff', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('team_id');
            }
            
            // Add foreign keys
            if (!Schema::hasColumn('staff', 'team_id')) {
                $table->foreign('team_id')->references('team_id')->on('teams')->onDelete('set null');
            }
            if (!Schema::hasColumn('staff', 'department_id')) {
                $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // Safely drop foreign keys
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableForeignKeys('staff');
            
            foreach ($indexes as $index) {
                if ($index->getLocalColumns() == ['team_id'] || $index->getLocalColumns() == ['department_id']) {
                    $table->dropForeign($index->getName());
                }
            }
            
            // Drop column if it exists
            if (Schema::hasColumn('staff', 'department_id')) {
                $table->dropColumn('department_id');
            }
        });
    }
};
