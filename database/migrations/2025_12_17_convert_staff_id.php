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
        // Disable FK checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Get existing staff BEFORE any modifications
        $staffList = DB::table('staff')->get();
        
        // Create mapping
        $mapping = [];
        $counter = 10;
        foreach ($staffList as $staff) {
            $oldId = $staff->staff_id;
            $newId = 'ST1101' . str_pad($counter++, 2, '0', STR_PAD_LEFT);
            $mapping[$oldId] = $newId;
        }
        
        // Get ALL foreign keys from INFORMATION_SCHEMA
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_NAME = 'staff'
            AND TABLE_SCHEMA = DATABASE()
        ");
        
        // Drop all foreign keys that reference staff table
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Ignore errors
            }
        }
        
        // Also convert any columns that reference staff_id to VARCHAR
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` MODIFY COLUMN `{$fk->COLUMN_NAME}` VARCHAR(20) NULL");
            } catch (\Exception $e) {
                // Ignore errors
            }
        }
        
        // Now change the staff_id column type
        DB::statement('ALTER TABLE staff MODIFY COLUMN staff_id VARCHAR(20) NOT NULL');
        
        // Update all staff_id values first
        DB::statement('UPDATE staff SET staff_id = NULL WHERE staff_id IS NULL');
        foreach ($mapping as $oldId => $newId) {
            DB::statement("UPDATE staff SET staff_id = ? WHERE id = (SELECT id FROM staff WHERE staff_id = ?)", [$newId, $oldId]);
        }
        
        // Update all referencing tables
        foreach ($foreignKeys as $fk) {
            foreach ($mapping as $oldId => $newId) {
                try {
                    DB::statement("UPDATE `{$fk->TABLE_NAME}` SET `{$fk->COLUMN_NAME}` = ? WHERE `{$fk->COLUMN_NAME}` = ?", [$newId, $oldId]);
                } catch (\Exception $e) {
                    // Ignore errors
                }
            }
        }
        
        // Recreate ALL foreign keys
        foreach ($foreignKeys as $fk) {
            try {
                $cascadeRule = ($fk->TABLE_NAME === 'teams' && $fk->COLUMN_NAME === 'team_lead_id') ? 'ON DELETE SET NULL' : 'ON DELETE CASCADE';
                DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` ADD CONSTRAINT `{$fk->CONSTRAINT_NAME}` FOREIGN KEY (`{$fk->COLUMN_NAME}`) REFERENCES `{$fk->REFERENCED_TABLE_NAME}`(`{$fk->REFERENCED_COLUMN_NAME}`) {$cascadeRule}");
            } catch (\Exception $e) {
                // Ignore errors
            }
        }
        
        // Re-enable FK checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Can't easily revert this migration
    }
};
