<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Converts staff_id from auto-increment integers to string format (st001, st002, st003)
     * WITHOUT deleting any data. All existing staff records are preserved and converted.
     */
    public function up(): void
    {
        // Only proceed if the staff table exists
        if (!Schema::hasTable('staff')) {
            return; // Table doesn't exist, nothing to migrate
        }

        // Check if staff_id is already a string (migration already applied)
        $columns = DB::select("SHOW COLUMNS FROM staff WHERE Field = 'staff_id'");
        if (!empty($columns) && strpos($columns[0]->Type, 'varchar') !== false) {
            return; // Already migrated to string format
        }

        // Step 1: Get the current state of the staff table
        $existingStaff = DB::table('staff')
            ->orderBy('id')
            ->select('id', 'staff_id')
            ->get();

        // Step 2: Drop foreign key constraints safely
        try {
            if (Schema::hasTable('attendance')) {
                DB::statement('ALTER TABLE attendance DROP FOREIGN KEY attendance_staff_id_foreign');
            }
        } catch (\Exception $e) {}

        try {
            if (Schema::hasTable('leave_requests')) {
                DB::statement('ALTER TABLE leave_requests DROP FOREIGN KEY leave_requests_staff_id_foreign');
            }
        } catch (\Exception $e) {}

        try {
            if (Schema::hasTable('staff_profile')) {
                DB::statement('ALTER TABLE staff_profile DROP FOREIGN KEY staff_profile_staff_id_foreign');
            }
        } catch (\Exception $e) {}

        // Step 3: Drop the primary key and unique constraints
        DB::statement('ALTER TABLE staff DROP PRIMARY KEY');
        
        try {
            DB::statement('ALTER TABLE staff DROP INDEX staff_id');
        } catch (\Exception $e) {}

        // Step 4: Rename the old staff_id column to old_staff_id
        Schema::table('staff', function (Blueprint $table) {
            $table->renameColumn('staff_id', 'old_staff_id');
        });

        // Step 5: Create a new staff_id column as VARCHAR(50) string
        Schema::table('staff', function (Blueprint $table) {
            $table->string('staff_id', 50)->nullable()->after('id');
        });

        // Step 6: Convert existing staff IDs to formatted string IDs (st001, st002, st003, etc.)
        foreach ($existingStaff as $index => $staff) {
            $newStaffId = 'st' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            DB::table('staff')
                ->where('id', $staff->id)
                ->update(['staff_id' => $newStaffId]);
        }

        // Step 7: Make staff_id NOT NULL and set as primary key
        Schema::table('staff', function (Blueprint $table) {
            $table->string('staff_id', 50)->change();
        });

        // Step 8: Set staff_id as the primary key
        DB::statement('ALTER TABLE staff ADD PRIMARY KEY (staff_id)');

        // Step 9: Drop the old_staff_id column
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('old_staff_id');
        });

        // Step 10: Restore foreign key constraints
        try {
            if (Schema::hasTable('attendance')) {
                DB::statement('ALTER TABLE attendance ADD CONSTRAINT attendance_staff_id_foreign FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE');
            }
        } catch (\Exception $e) {}

        try {
            if (Schema::hasTable('leave_requests')) {
                DB::statement('ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_staff_id_foreign FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE');
            }
        } catch (\Exception $e) {}

        try {
            if (Schema::hasTable('staff_profile')) {
                DB::statement('ALTER TABLE staff_profile ADD CONSTRAINT staff_profile_staff_id_foreign FOREIGN KEY (staff_id) REFERENCES staff(staff_id) ON DELETE CASCADE');
            }
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     * 
     * Reverts the staff_id back to auto-increment integer format
     * NOTE: This will lose the formatted string IDs, but keeps original data
     */
    public function down(): void
    {
        // Step 1: Get the current formatted staff IDs and their IDs
        $staffMembers = DB::table('staff')
            ->orderBy('id')
            ->get();

        // Step 2: Drop foreign key constraints to allow modifications
        Schema::table('attendance', function (Blueprint $table) {
            if (Schema::hasTable('attendance')) {
                try {
                    $table->dropForeign(['staff_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            }
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            if (Schema::hasTable('leave_requests')) {
                try {
                    $table->dropForeign(['staff_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            }
        });

        Schema::table('staff_profile', function (Blueprint $table) {
            if (Schema::hasTable('staff_profile')) {
                try {
                    $table->dropForeign(['staff_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            }
        });

        // Step 3: Drop the primary key
        DB::statement('ALTER TABLE staff DROP PRIMARY KEY');

        // Step 4: Rename staff_id to old_staff_id temporarily
        Schema::table('staff', function (Blueprint $table) {
            $table->renameColumn('staff_id', 'old_staff_id');
        });

        // Step 5: Create a new staff_id column as auto-increment integer
        Schema::table('staff', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->after('id');
        });

        // Step 6: Restore original numeric IDs (1, 2, 3, etc.)
        $counter = 1;
        foreach ($staffMembers as $staff) {
            DB::table('staff')
                ->where('id', $staff->id)
                ->update(['staff_id' => $counter]);
            $counter++;
        }

        // Step 7: Set staff_id as primary key with auto-increment
        DB::statement('ALTER TABLE staff ADD PRIMARY KEY (staff_id) AUTO_INCREMENT');

        // Step 8: Drop the temporary old_staff_id column
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('old_staff_id');
        });

        // Step 9: Restore foreign key constraints
        Schema::table('attendance', function (Blueprint $table) {
            if (Schema::hasTable('attendance')) {
                try {
                    $table->foreign('staff_id')
                        ->references('staff_id')
                        ->on('staff')
                        ->cascadeOnDelete();
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            if (Schema::hasTable('leave_requests')) {
                try {
                    $table->foreign('staff_id')
                        ->references('staff_id')
                        ->on('staff')
                        ->cascadeOnDelete();
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
        });

        Schema::table('staff_profile', function (Blueprint $table) {
            if (Schema::hasTable('staff_profile')) {
                try {
                    $table->foreign('staff_id')
                        ->references('staff_id')
                        ->on('staff')
                        ->cascadeOnDelete();
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
        });
    }
};
