<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hash all existing staff passwords that aren't already hashed
        $staffMembers = DB::table('staff')->get();
        
        foreach ($staffMembers as $staff) {
            // Check if password looks like it's already hashed (bcrypt hashes start with $2)
            if (!str_starts_with($staff->staff_password, '$2')) {
                DB::table('staff')
                    ->where('staff_id', $staff->staff_id)
                    ->update(['staff_password' => Hash::make($staff->staff_password)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration cannot be safely reversed as we cannot unhash passwords
        // If you need to revert, restore from a backup
    }
};
