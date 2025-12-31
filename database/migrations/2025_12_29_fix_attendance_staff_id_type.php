<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convert staff_id to string type using raw SQL for better compatibility
        if (DB::getSchemaBuilder()->hasTable('attendance')) {
            // Try to drop the foreign key if it exists
            try {
                DB::statement('ALTER TABLE attendance DROP FOREIGN KEY attendance_staff_id_foreign');
            } catch (\Exception $e) {
                // Foreign key doesn't exist, continue
            }

            // Change column type using raw SQL
            DB::statement('ALTER TABLE attendance MODIFY staff_id VARCHAR(10)');

            // Re-add the foreign key
            try {
                DB::statement('ALTER TABLE attendance ADD CONSTRAINT attendance_staff_id_foreign FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE');
            } catch (\Exception $e) {
                // Constraint already exists, continue
            }
        }
    }

    public function down(): void
    {
        if (DB::getSchemaBuilder()->hasTable('attendance')) {
            DB::statement('ALTER TABLE attendance DROP FOREIGN KEY attendance_staff_id_foreign');
            DB::statement('ALTER TABLE attendance MODIFY staff_id BIGINT UNSIGNED');
            DB::statement('ALTER TABLE attendance ADD CONSTRAINT attendance_staff_id_foreign FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE');
        }
    }
};

