<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\Admin;
use App\Models\Department;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the seeders in order
        $this->call(AdminSeeder::class);
        $this->call(DepartmentTeamSeeder::class);
    }

}

