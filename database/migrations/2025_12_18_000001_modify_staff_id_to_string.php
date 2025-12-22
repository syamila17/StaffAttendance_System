<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Change staff_id from auto-increment integer to string format (st001, st002, etc.)
     */
    public function up(): void
    {
        // Step 1: Drop primary key constraint
        Schema::table('staff', function (Blueprint $table) {
            $table->dropPrimary();
        });

        // Step 2: Rename old staff_id column temporarily
        Schema::table('staff', function (Blueprint $table) {
            $table->renameColumn('staff_id', 'old_staff_id');
        });

        // Step 3: Create new staff_id as string with primary key
        Schema::table('staff', function (Blueprint $table) {
            $table->string('staff_id', 10)->primary()->after('id');
        });

        // Step 4: Convert existing staff records to formatted IDs (st001, st002, etc.)
        $staffMembers = DB::table('staff')->orderBy('old_staff_id')->get();
        foreach ($staffMembers as $index => $staff) {
            $newId = 'st' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            DB::table('staff')
                ->where('old_staff_id', $staff->old_staff_id)
                ->update(['staff_id' => $newId]);
        }

        // Step 5: Drop the old numeric column
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('old_staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropPrimary();
            $table->renameColumn('staff_id', 'old_staff_id');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->id('staff_id')->after('id');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('old_staff_id');
        });
    }
};