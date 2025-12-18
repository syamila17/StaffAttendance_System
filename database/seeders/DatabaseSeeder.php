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
        // 1. Create Departments
        $depIT = Department::create([
            'department_name' => 'Information Technology',
            'department_code' => 'IT',
            'description' => 'IT Department responsible for system maintenance and development',
            'location' => 'Building A, Floor 3',
            'status' => 'active',
        ]);

        $depHR = Department::create([
            'department_name' => 'Human Resources',
            'department_code' => 'HR',
            'description' => 'HR Department handles recruitment and employee relations',
            'location' => 'Building B, Floor 2',
            'status' => 'active',
        ]);

        $depOps = Department::create([
            'department_name' => 'Operations',
            'department_code' => 'OPS',
            'description' => 'Operations Department manages daily operations',
            'location' => 'Building C, Floor 1',
            'status' => 'active',
        ]);

        // 2. Create Teams
        $teamBackend = Team::create([
            'team_name' => 'Backend Development',
            'team_code' => 'IT-BACKEND',
            'department_id' => $depIT->department_id,
            'description' => 'Backend development team',
            'status' => 'active',
        ]);

        $teamFrontend = Team::create([
            'team_name' => 'Frontend Development',
            'team_code' => 'IT-FRONTEND',
            'department_id' => $depIT->department_id,
            'description' => 'Frontend development team',
            'status' => 'active',
        ]);

        $teamRecruit = Team::create([
            'team_name' => 'Recruitment',
            'team_code' => 'HR-RECRUIT',
            'department_id' => $depHR->department_id,
            'description' => 'Recruitment team',
            'status' => 'active',
        ]);

        // 3. Create test staff members with departments and teams
        $staff1 = Staff::create([
            'staff_name' => 'Ahmad Hassan',
            'staff_email' => 'ahmad@utm.edu.my',
            'staff_password' => Hash::make('password123'),
            'department_id' => $depIT->department_id,
            'team_id' => $teamBackend->team_id,
        ]);

        $staff2 = Staff::create([
            'staff_name' => 'Siti Nurhaliza',
            'staff_email' => 'siti@utm.edu.my',
            'staff_password' => Hash::make('password123'),
            'department_id' => $depIT->department_id,
            'team_id' => $teamFrontend->team_id,
        ]);

        $staff3 = Staff::create([
            'staff_name' => 'Test User',
            'staff_email' => 'test@utm.edu.my',
            'staff_password' => Hash::make('password123'),
            'department_id' => $depHR->department_id,
            'team_id' => $teamRecruit->team_id,
        ]);

        // 4. Set team leads and department managers
        $teamBackend->update(['team_lead_id' => $staff1->staff_id]);
        $teamFrontend->update(['team_lead_id' => $staff2->staff_id]);
        $depIT->update(['manager_id' => $staff1->staff_id]);
        $depHR->update(['manager_id' => $staff3->staff_id]);

        // 5. Create admin user
        Admin::create([
            'admin_name' => 'Admin User',
            'admin_email' => 'admin@utm.edu.my',
            'admin_password' => Hash::make('admin123'),
        ]);
    }
}
