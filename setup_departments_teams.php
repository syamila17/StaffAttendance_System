<?php

use App\Models\Department;
use App\Models\Team;

// Get the application instance
$app = require __DIR__ . '/bootstrap/app.php';

// Bind the kernel
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Create a request
$request = \Illuminate\Http\Request::capture();

try {
    echo "Setting up Departments and Teams...\n";
    echo str_repeat("=", 50) . "\n";

    // Create DDA Department
    $dda = Department::updateOrCreate(
        ['department_code' => 'DDA'],
        [
            'department_name' => 'DDA',
            'description' => 'Data & Development Administration',
            'status' => 'active'
        ]
    );
    echo "✓ DDA Department\n";

    // Create DDI Department
    $ddi = Department::updateOrCreate(
        ['department_code' => 'DDI'],
        [
            'department_name' => 'DDI',
            'description' => 'Digital Development & Innovation',
            'status' => 'active'
        ]
    );
    echo "✓ DDI Department\n";

    // DDA Teams
    Team::updateOrCreate(
        ['team_code' => 'NETWORK'],
        [
            'team_name' => 'Network',
            'department_id' => $dda->department_id,
            'status' => 'active'
        ]
    );
    echo "✓ Network Team (DDA)\n";

    Team::updateOrCreate(
        ['team_code' => 'DATACENTER'],
        [
            'team_name' => 'Data Centre',
            'department_id' => $dda->department_id,
            'status' => 'active'
        ]
    );
    echo "✓ Data Centre Team (DDA)\n";

    // DDI Teams
    Team::updateOrCreate(
        ['team_code' => 'RESEARCH'],
        [
            'team_name' => 'Research',
            'department_id' => $ddi->department_id,
            'status' => 'active'
        ]
    );
    echo "✓ Research Team (DDI)\n";

    Team::updateOrCreate(
        ['team_code' => 'OPERATION'],
        [
            'team_name' => 'Operation',
            'department_id' => $ddi->department_id,
            'status' => 'active'
        ]
    );
    echo "✓ Operation Team (DDI)\n";

    Team::updateOrCreate(
        ['team_code' => 'HR'],
        [
            'team_name' => 'Human Resources',
            'department_id' => $ddi->department_id,
            'status' => 'active'
        ]
    );
    echo "✓ Human Resources Team (DDI)\n";

    echo str_repeat("=", 50) . "\n";
    echo "Summary:\n";
    echo "Total Departments: " . Department::count() . "\n";
    echo "Total Teams: " . Team::count() . "\n";
    echo "DDA Teams: " . Team::where('department_id', $dda->department_id)->count() . "\n";
    echo "DDI Teams: " . Team::where('department_id', $ddi->department_id)->count() . "\n";
    echo "✓ Setup completed successfully!\n";

} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
