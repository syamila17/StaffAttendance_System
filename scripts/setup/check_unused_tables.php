<?php

require 'staff_attendance/vendor/autoload.php';
$app = require_once 'staff_attendance/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

try {
    $tables = DB::select('SHOW TABLES FROM staffAttend_data');
    $dbName = 'staffAttend_data';
    
    echo "\n" . str_repeat('=', 70) . "\n";
    echo "DATABASE TABLES IN: {$dbName}\n";
    echo str_repeat('=', 70) . "\n\n";
    
    $allTables = [];
    foreach ($tables as $table) {
        foreach ($table as $tableName) {
            $allTables[] = $tableName;
        }
    }
    
    $used = [
        'staff', 'admin', 'departments', 'teams', 'staff_profile',
        'attendance', 'attendance_reports', 'attendance_report_details',
        'leave_requests', 'sessions'
    ];
    
    echo "USED TABLES:\n";
    echo str_repeat('-', 70) . "\n";
    foreach ($allTables as $table) {
        if (in_array($table, $used)) {
            $rowCount = DB::table($table)->count();
            echo "✓ {$table}: {$rowCount} rows\n";
        }
    }
    
    echo "\n\nUNUSED TABLES (Safe to drop):\n";
    echo str_repeat('-', 70) . "\n";
    $unusedTables = [];
    foreach ($allTables as $table) {
        if (!in_array($table, $used)) {
            $rowCount = DB::table($table)->count();
            echo "⚠ {$table}: {$rowCount} rows\n";
            $unusedTables[] = $table;
        }
    }
    
    echo "\n" . str_repeat('=', 70) . "\n";
    echo "SUMMARY:\n";
    echo "Total tables: " . count($allTables) . "\n";
    echo "Used tables: " . count(array_intersect($allTables, $used)) . "\n";
    echo "Unused tables: " . count($unusedTables) . "\n";
    echo str_repeat('=', 70) . "\n";
    
    if (count($unusedTables) > 0) {
        echo "\nTo drop unused tables, run:\n";
        foreach ($unusedTables as $table) {
            echo "php artisan tinker\n";
            echo ">>> DB::statement('DROP TABLE IF EXISTS {$table}');\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
