#!/usr/bin/env php
<?php

$projectPath = 'C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance';
chdir($projectPath);

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if staff exists
$staffCount = \App\Models\Staff::count();
$adminCount = \App\Models\Admin::count();

echo "\n=== Database Status ===\n";
echo "Staff records: $staffCount\n";
echo "Admin records: $adminCount\n";

if ($staffCount === 0 || $adminCount === 0) {
    echo "\n[!] Database is empty. Seeding now...\n";
    $exitCode = $kernel->handle(
        $input = new \Symfony\Component\Console\Input\ArrayInput(['command' => 'db:seed']),
        new \Symfony\Component\Console\Output\ConsoleOutput()
    );
    
    if ($exitCode === 0) {
        echo "\n[✓] Database seeded successfully!\n";
        
        // Verify
        $staffCount = \App\Models\Staff::count();
        $adminCount = \App\Models\Admin::count();
        echo "Staff records now: $staffCount\n";
        echo "Admin records now: $adminCount\n";
        
        echo "\n=== Credentials ===\n";
        echo "Staff: ahmad@utm.edu.my / password123\n";
        echo "Admin: admin@utm.edu.my / admin123\n";
    } else {
        echo "\n[ERROR] Seeding failed!\n";
    }
} else {
    echo "\n[✓] Database has data!\n";
    echo "\n=== Available Credentials ===\n";
    
    $staff = \App\Models\Staff::pluck('staff_email');
    echo "Staff emails:\n";
    foreach ($staff as $email) {
        echo "  - $email\n";
    }
    
    $admins = \App\Models\Admin::pluck('admin_email');
    echo "Admin emails:\n";
    foreach ($admins as $email) {
        echo "  - $email\n";
    }
}

echo "\n";
?>
