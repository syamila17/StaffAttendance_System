#!/bin/bash

# Setup departments and teams script
cd /c/Users/syami/Desktop/StaffAttendance_System/staff_attendance

echo "Setting up departments and teams..."

php artisan tinker <<EOF
use App\Models\Department;
use App\Models\Team;

// Create DDA Department
\$dda = Department::firstOrCreate(
    ['department_code' => 'DDA'],
    [
        'department_name' => 'DDA',
        'description' => 'Data & Development Administration',
        'location' => 'Main Office',
        'status' => 'active'
    ]
);

// Create DDI Department
\$ddi = Department::firstOrCreate(
    ['department_code' => 'DDI'],
    [
        'department_name' => 'DDI',
        'description' => 'Digital Development & Innovation',
        'location' => 'Main Office',
        'status' => 'active'
    ]
);

// Create DDA Teams
Team::firstOrCreate(
    ['team_code' => 'NETWORK'],
    [
        'team_name' => 'Network',
        'department_id' => \$dda->department_id,
        'status' => 'active'
    ]
);

Team::firstOrCreate(
    ['team_code' => 'DATACENTER'],
    [
        'team_name' => 'Data Centre',
        'department_id' => \$dda->department_id,
        'status' => 'active'
    ]
);

// Create DDI Teams
Team::firstOrCreate(
    ['team_code' => 'RESEARCH'],
    [
        'team_name' => 'Research',
        'department_id' => \$ddi->department_id,
        'status' => 'active'
    ]
);

Team::firstOrCreate(
    ['team_code' => 'OPERATION'],
    [
        'team_name' => 'Operation',
        'department_id' => \$ddi->department_id,
        'status' => 'active'
    ]
);

Team::firstOrCreate(
    ['team_code' => 'HR'],
    [
        'team_name' => 'Human Resources',
        'department_id' => \$ddi->department_id,
        'status' => 'active'
    ]
);

echo "Departments: " . Department::count() . "\n";
echo "Teams: " . Team::count() . "\n";
EOF

echo "Done!"
