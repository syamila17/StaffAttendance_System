<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert all staff_id columns in related tables from integer to VARCHAR(20)
     */
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Helper function to safely get FK name
        $getForeignKeyName = function ($table, $column) {
            try {
                $result = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                    WHERE TABLE_NAME = ? 
                    AND COLUMN_NAME = ? 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ", [$table, $column]);
                
                return !empty($result) ? $result[0]->CONSTRAINT_NAME : null;
            } catch (\Exception $e) {
                return null;
            }
        };

        // Convert attendance table
        if (Schema::hasTable('attendance')) {
            try {
                $fkName = $getForeignKeyName('attendance', 'staff_id');
                if ($fkName) {
                    DB::statement("ALTER TABLE attendance DROP FOREIGN KEY $fkName");
                }
            } catch (\Exception $e) {
                // Silent ignore
            }
            
            DB::statement('ALTER TABLE attendance MODIFY COLUMN staff_id VARCHAR(20) NOT NULL');
            
            try {
                Schema::table('attendance', function (Blueprint $table) {
                    $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // FK already exists
            }
        }

        // Convert leave_requests table
        if (Schema::hasTable('leave_requests')) {
            try {
                $fkName = $getForeignKeyName('leave_requests', 'staff_id');
                if ($fkName) {
                    DB::statement("ALTER TABLE leave_requests DROP FOREIGN KEY $fkName");
                }
            } catch (\Exception $e) {
                // Silent ignore
            }
            
            DB::statement('ALTER TABLE leave_requests MODIFY COLUMN staff_id VARCHAR(20) NOT NULL');
            
            try {
                Schema::table('leave_requests', function (Blueprint $table) {
                    $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // FK already exists
            }
        }

        // Convert staff_profile table
        if (Schema::hasTable('staff_profile')) {
            try {
                $fkName = $getForeignKeyName('staff_profile', 'staff_id');
                if ($fkName) {
                    DB::statement("ALTER TABLE staff_profile DROP FOREIGN KEY $fkName");
                }
            } catch (\Exception $e) {
                // Silent ignore
            }
            
            DB::statement('ALTER TABLE staff_profile MODIFY COLUMN staff_id VARCHAR(20) NOT NULL');
            
            try {
                Schema::table('staff_profile', function (Blueprint $table) {
                    $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // FK already exists
            }
        }

        // Convert attendance_report_details table if it exists
        if (Schema::hasTable('attendance_report_details')) {
            try {
                $fkName = $getForeignKeyName('attendance_report_details', 'staff_id');
                if ($fkName) {
                    DB::statement("ALTER TABLE attendance_report_details DROP FOREIGN KEY $fkName");
                }
            } catch (\Exception $e) {
                // Silent ignore
            }
            
            try {
                DB::statement('ALTER TABLE attendance_report_details MODIFY COLUMN staff_id VARCHAR(20) NOT NULL');

                Schema::table('attendance_report_details', function (Blueprint $table) {
                    $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Column doesn't exist
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
        // Reverting is risky - leaving as is for safety
        // If needed to revert, manually handle the conversion back to BIGINT
    }
};
