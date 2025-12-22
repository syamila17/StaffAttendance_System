<?php

// Setup departments and teams
require 'staff_attendance/vendor/autoload.php';
$app = require 'staff_attendance/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Department;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

try {
    // Create DDA Department
    $dda = Department::updateOrCreate(
        ['department_code' => 'DDA'],
        [
            'department_name' => 'DDA',
            'description' => 'Data & Development Administration',
            'location' => 'Main Office',
            'status' => 'active'
        ]
    );
    echo "✓ DDA Department created/updated\n";

    // Create DDI Department
    $ddi = Department::updateOrCreate(
        ['department_code' => 'DDI'],
        [
            'department_name' => 'DDI',
            'description' => 'Digital Development & Innovation',
            'location' => 'Main Office',
            'status' => 'active'
        ]
    );
    echo "✓ DDI Department created/updated\n";

    // Create DDA Teams
    Team::updateOrCreate(
        ['team_code' => 'NETWORK'],
        [
            'team_name' => 'Network',
            'department_id' => $dda->department_id,
            'status' => 'active',
            'description' => 'Network Team'
        ]
    );
    echo "✓ Network Team created/updated\n";

    Team::updateOrCreate(
        ['team_code' => 'DATACENTER'],
        [
            'team_name' => 'Data Centre',
            'department_id' => $dda->department_id,
            'status' => 'active',
            'description' => 'Data Centre Team'
        ]
    );
    echo "✓ Data Centre Team created/updated\n";

    // Create DDI Teams
    Team::updateOrCreate(
        ['team_code' => 'RESEARCH'],
        [
            'team_name' => 'Research',
            'department_id' => $ddi->department_id,
            'status' => 'active',
            'description' => 'Research Team'
        ]
    );
    echo "✓ Research Team created/updated\n";

    Team::updateOrCreate(
        ['team_code' => 'OPERATION'],
        [
            'team_name' => 'Operation',
            'department_id' => $ddi->department_id,
            'status' => 'active',
            'description' => 'Operation Team'
        ]
    );
    echo "✓ Operation Team created/updated\n";

    Team::updateOrCreate(
        ['team_code' => 'HR'],
        [
            'team_name' => 'Human Resources',
            'department_id' => $ddi->department_id,
            'status' => 'active',
            'description' => 'HR Team'
        ]
    );
    echo "✓ Human Resources Team created/updated\n";

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo "Departments: " . Department::count() . "\n";
    echo "Teams: " . Team::count() . "\n";
    echo "DDA Teams: " . Team::where('department_id', $dda->department_id)->count() . "\n";
    echo "DDI Teams: " . Team::where('department_id', $ddi->department_id)->count() . "\n";
    echo str_repeat("=", 50) . "\n";
    echo "✓ Setup completed successfully!\n";

} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
