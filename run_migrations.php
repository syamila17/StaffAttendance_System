#!/usr/bin/env php
<?php

$projectPath = 'C:\Users\syami\Desktop\StaffAttendance_System\staff_attendance';
chdir($projectPath);

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== Running Migrations ===\n";

$exitCode = $kernel->handle(
    $input = new \Symfony\Component\Console\Input\ArrayInput(['command' => 'migrate']),
    new \Symfony\Component\Console\Output\ConsoleOutput()
);

if ($exitCode === 0) {
    echo "\n[✓] Migrations completed successfully!\n";
} else {
    echo "\n[!] Migrations completed with status: $exitCode\n";
}

echo "\n";
?>
