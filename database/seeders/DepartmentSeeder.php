<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create departments
        Department::firstOrCreate(
            ['department_code' => 'IT'],
            [
                'department_name' => 'Information Technology',
                'description' => 'IT Department responsible for system maintenance and development',
                'location' => 'Building A, Floor 3',
                'status' => 'active',
            ]
        );

        Department::firstOrCreate(
            ['department_code' => 'HR'],
            [
                'department_name' => 'Human Resources',
                'description' => 'HR Department handles recruitment and employee relations',
                'location' => 'Building B, Floor 2',
                'status' => 'active',
            ]
        );

        Department::firstOrCreate(
            ['department_code' => 'OPS'],
            [
                'department_name' => 'Operations',
                'description' => 'Operations Department manages daily operations',
                'location' => 'Building C, Floor 1',
                'status' => 'active',
            ]
        );

        $this->command->info('Departments created/recovered successfully!');
        $this->command->info('- Information Technology (IT)');
        $this->command->info('- Human Resources (HR)');
        $this->command->info('- Operations (OPS)');
    }
}
