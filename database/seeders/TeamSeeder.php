<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Department;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get department IDs
        $depIT = Department::where('department_code', 'IT')->first();
        $depHR = Department::where('department_code', 'HR')->first();

        if (!$depIT || !$depHR) {
            $this->command->error('Departments not found. Please run DepartmentSeeder first!');
            return;
        }

        // Create teams for IT Department
        Team::firstOrCreate(
            ['team_code' => 'IT-BACKEND'],
            [
                'team_name' => 'Backend Development',
                'department_id' => $depIT->department_id,
                'description' => 'Backend development team',
                'status' => 'active',
            ]
        );

        Team::firstOrCreate(
            ['team_code' => 'IT-FRONTEND'],
            [
                'team_name' => 'Frontend Development',
                'department_id' => $depIT->department_id,
                'description' => 'Frontend development team',
                'status' => 'active',
            ]
        );

        // Create teams for HR Department
        Team::firstOrCreate(
            ['team_code' => 'HR-RECRUIT'],
            [
                'team_name' => 'Recruitment',
                'department_id' => $depHR->department_id,
                'description' => 'Recruitment team',
                'status' => 'active',
            ]
        );

        $this->command->info('Teams created/recovered successfully!');
        $this->command->info('- Backend Development (IT-BACKEND)');
        $this->command->info('- Frontend Development (IT-FRONTEND)');
        $this->command->info('- Recruitment (HR-RECRUIT)');
    }
}
