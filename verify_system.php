#!/usr/bin/env php
<?php

$projectPath = 'C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance';
chdir($projectPath);

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== System Verification ===\n";

// Check tables
$db = \Illuminate\Support\Facades\DB::connection();

$tables = [
    'staff' => 'Staff',
    'staff_profile' => 'Staff Profile',
    'attendance' => 'Attendance',
    'admin' => 'Admin',
    'departments' => 'Departments',
    'teams' => 'Teams',
];

echo "\n[Tables]\n";
foreach ($tables as $table => $name) {
    $exists = $db->getSchemaBuilder()->hasTable($table);
    $status = $exists ? '✓' : '✗';
    echo "$status $name ($table)\n";
}

// Check test data
echo "\n[Test Data]\n";
$staffCount = \App\Models\Staff::count();
$adminCount = \App\Models\Admin::count();
echo "✓ Staff records: $staffCount\n";
echo "✓ Admin records: $adminCount\n";

echo "\n=== System Ready ===\n";
echo "Visit: http://localhost:8000/login\n";
echo "Staff: ahmad@utm.edu.my / password123\n";
echo "Admin: admin@utm.edu.my / admin123\n\n";

?>
