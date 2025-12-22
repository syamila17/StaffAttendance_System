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
        // Create the 3 Departments: DDA, DDS, DDI
        $depDDA = Department::updateOrCreate(
            ['department_code' => 'DDA'],
            [
                'department_name' => 'DDA',
                'description' => 'Department DDA',
                'location' => 'Building A',
                'manager_id' => null,
                'status' => 'active',
            ]
        );

        $depDDS = Department::updateOrCreate(
            ['department_code' => 'DDS'],
            [
                'department_name' => 'DDS',
                'description' => 'Department DDS',
                'location' => 'Building B',
                'manager_id' => null,
                'status' => 'active',
            ]
        );

        $depDDI = Department::updateOrCreate(
            ['department_code' => 'DDI'],
            [
                'department_name' => 'DDI',
                'description' => 'Department DDI',
                'location' => 'Building C',
                'manager_id' => null,
                'status' => 'active',
            ]
        );

        // DDA Teams (4 teams)
        Team::updateOrCreate(
            ['team_code' => 'DDA-NETWORK'],
            [
                'team_name' => 'Network',
                'department_id' => $depDDA->department_id,
                'team_lead_id' => null,
                'description' => 'Network Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDA-CARE'],
            [
                'team_name' => 'Digital Care',
                'department_id' => $depDDA->department_id,
                'team_lead_id' => null,
                'description' => 'Digital Care Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDA-DATA'],
            [
                'team_name' => 'Digital Data',
                'department_id' => $depDDA->department_id,
                'team_lead_id' => null,
                'description' => 'Digital Data Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDA-CENTRE'],
            [
                'team_name' => 'Data Centre',
                'department_id' => $depDDA->department_id,
                'team_lead_id' => null,
                'description' => 'Data Centre Team',
                'status' => 'active',
            ]
        );

        // DDS Teams (5 teams)
        Team::updateOrCreate(
            ['team_code' => 'DDS-STRATEGY'],
            [
                'team_name' => 'Digital Strategy & PMO',
                'department_id' => $depDDS->department_id,
                'team_lead_id' => null,
                'description' => 'Digital Strategy & PMO Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDS-RISK'],
            [
                'team_name' => 'Risk & Quality Management',
                'department_id' => $depDDS->department_id,
                'team_lead_id' => null,
                'description' => 'Risk & Quality Management Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDS-COMPETENCY'],
            [
                'team_name' => 'Digital Competency',
                'department_id' => $depDDS->department_id,
                'team_lead_id' => null,
                'description' => 'Digital Competency Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDS-CYBER'],
            [
                'team_name' => 'Cyber Security',
                'department_id' => $depDDS->department_id,
                'team_lead_id' => null,
                'description' => 'Cyber Security Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDS-WEB'],
            [
                'team_name' => 'Web Management',
                'department_id' => $depDDS->department_id,
                'team_lead_id' => null,
                'description' => 'Web Management Team',
                'status' => 'active',
            ]
        );

        // DDI Teams (5 teams)
        Team::updateOrCreate(
            ['team_code' => 'DDI-ACADEMIC'],
            [
                'team_name' => 'Academic',
                'department_id' => $depDDI->department_id,
                'team_lead_id' => null,
                'description' => 'Academic Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDI-SUPPORT'],
            [
                'team_name' => 'Support System',
                'department_id' => $depDDI->department_id,
                'team_lead_id' => null,
                'description' => 'Support System Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDI-MOBILE'],
            [
                'team_name' => 'Mobile Apps Research Computing',
                'department_id' => $depDDI->department_id,
                'team_lead_id' => null,
                'description' => 'Mobile Apps Research Computing Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDI-FINANCE'],
            [
                'team_name' => 'Finance',
                'department_id' => $depDDI->department_id,
                'team_lead_id' => null,
                'description' => 'Finance Team',
                'status' => 'active',
            ]
        );

        Team::updateOrCreate(
            ['team_code' => 'DDI-HR'],
            [
                'team_name' => 'Human Resource',
                'department_id' => $depDDI->department_id,
                'team_lead_id' => null,
                'description' => 'Human Resource Team',
                'status' => 'active',
            ]
        );

        echo "Departments (DDA, DDS, DDI) and Teams seeded successfully!\n";
    }
}
