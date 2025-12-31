<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convert staff_id to string type using raw SQL for better compatibility
        if (DB::getSchemaBuilder()->hasTable('leave_requests')) {
            // Try to drop the foreign key if it exists
            try {
                DB::statement('ALTER TABLE leave_requests DROP FOREIGN KEY leave_requests_staff_id_foreign');
            } catch (\Exception $e) {
                // Foreign key doesn't exist, continue
            }

            // Change column type using raw SQL
            DB::statement('ALTER TABLE leave_requests MODIFY staff_id VARCHAR(10)');

            // Re-add the foreign key
            try {
                DB::statement('ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_staff_id_foreign FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE');
            } catch (\Exception $e) {
                // Constraint already exists, continue
            }
        }
    }

    public function down(): void
    {
        if (DB::getSchemaBuilder()->hasTable('leave_requests')) {
            DB::statement('ALTER TABLE leave_requests DROP FOREIGN KEY leave_requests_staff_id_foreign');
            DB::statement('ALTER TABLE leave_requests MODIFY staff_id BIGINT UNSIGNED');
            DB::statement('ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_staff_id_foreign FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE');
        }
    }
};
