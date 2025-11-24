<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Team;

class DepartmentTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Departments
        $depIT = Department::create([
            'department_name' => 'Information Technology',
            'department_code' => 'IT',
            'description' => 'IT Department responsible for system maintenance and development',
            'location' => 'Building A, Floor 3',
            'manager_id' => null, // Will be set later
            'status' => 'active',
        ]);

        $depHR = Department::create([
            'department_name' => 'Human Resources',
            'department_code' => 'HR',
            'description' => 'HR Department handles recruitment and employee relations',
            'location' => 'Building B, Floor 2',
            'manager_id' => null,
            'status' => 'active',
        ]);

        $depOps = Department::create([
            'department_name' => 'Operations',
            'department_code' => 'OPS',
            'description' => 'Operations Department manages daily operations',
            'location' => 'Building C, Floor 1',
            'manager_id' => null,
            'status' => 'active',
        ]);

        // Create Teams for IT Department
        $teamBackend = Team::create([
            'team_name' => 'Backend Development',
            'team_code' => 'IT-BACKEND',
            'department_id' => $depIT->department_id,
            'team_lead_id' => null,
            'description' => 'Backend development team',
            'status' => 'active',
        ]);

        $teamFrontend = Team::create([
            'team_name' => 'Frontend Development',
            'team_code' => 'IT-FRONTEND',
            'department_id' => $depIT->department_id,
            'team_lead_id' => null,
            'description' => 'Frontend development team',
            'status' => 'active',
        ]);

        // Create Teams for HR Department
        $teamRecruit = Team::create([
            'team_name' => 'Recruitment',
            'team_code' => 'HR-RECRUIT',
            'department_id' => $depHR->department_id,
            'team_lead_id' => null,
            'description' => 'Recruitment team',
            'status' => 'active',
        ]);

        $teamPayroll = Team::create([
            'team_name' => 'Payroll',
            'team_code' => 'HR-PAYROLL',
            'department_id' => $depHR->department_id,
            'team_lead_id' => null,
            'description' => 'Payroll management team',
            'status' => 'active',
        ]);

        // Create Teams for Operations Department
        $teamSupport = Team::create([
            'team_name' => 'Customer Support',
            'team_code' => 'OPS-SUPPORT',
            'department_id' => $depOps->department_id,
            'team_lead_id' => null,
            'description' => 'Customer support team',
            'status' => 'active',
        ]);

        echo "Departments and Teams seeded successfully!\n";
    }
}
