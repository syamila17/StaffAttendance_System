<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin account
        DB::table('admin')->insertOrIgnore([
            'admin_name' => 'System Administrator',
            'admin_email' => 'admin@utm.my',
            'admin_password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Admin account created successfully!');
        $this->command->info('Email: admin@utm.my');
        $this->command->info('Password: admin123');
    }
}
