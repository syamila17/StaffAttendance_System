<?php
/**
 * Direct conversion script for staff_ids
 * Run: php convert_staff_ids_direct.php from staff_attendance directory
 */

// Setup Laravel
$app = require 'bootstrap/app.php';

try {
    // Get database instance
    $db = $app->make('db');
    
    echo "=== Staff ID Conversion Script ===\n\n";
    
    // Step 1: Disable foreign key checks
    echo "Step 1: Disabling foreign key checks...\n";
    $db::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Step 2: Get all staff
    echo "Step 2: Fetching staff records...\n";
    $staffList = $db::table('staff')->get();
    echo "Found " . $staffList->count() . " staff members\n\n";
    
    if ($staffList->count() === 0) {
        echo "No staff found!\n";
        exit(1);
    }
    
    // Step 3: Create mapping of old to new IDs
    echo "Step 3: Creating ID mapping...\n";
    $mapping = [];
    $counter = 10;
    foreach ($staffList as $staff) {
        $oldId = $staff->staff_id;
        $newId = 'ST1101' . str_pad($counter++, 2, '0', STR_PAD_LEFT);
        $mapping[$oldId] = $newId;
        printf("  %s: %d → %s\n", $staff->staff_name, $oldId, $newId);
    }
    
    // Step 4: Change column type
    echo "\nStep 4: Changing staff_id column type to VARCHAR(20)...\n";
    $db::statement('ALTER TABLE staff MODIFY COLUMN staff_id VARCHAR(20) NOT NULL');
    echo "✓ Column type changed\n";
    
    // Step 5: Update staff table
    echo "\nStep 5: Updating staff table...\n";
    foreach ($mapping as $oldId => $newId) {
        $db::update('UPDATE staff SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
        echo "  ✓ {$oldId} → {$newId}\n";
    }
    
    // Step 6: Update related tables
    echo "\nStep 6: Updating related tables...\n";
    foreach ($mapping as $oldId => $newId) {
        $db::update('UPDATE staff_profile SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
        $db::update('UPDATE attendance SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
        $db::update('UPDATE attendance_report SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
    }
    echo "✓ All related tables updated\n";
    
    // Step 7: Re-enable foreign keys
    echo "\nStep 7: Re-enabling foreign key checks...\n";
    $db::statement('SET FOREIGN_KEY_CHECKS=1');
    echo "✓ Foreign key checks re-enabled\n";
    
    // Step 8: Verify conversion
    echo "\n=== Verification ===\n";
    $converted = $db::table('staff')
        ->select('staff_id', 'staff_name', 'staff_email')
        ->orderBy('staff_id')
        ->get();
    
    foreach ($converted as $c) {
        printf("  %s: %s (%s)\n", $c->staff_id, $c->staff_name, $c->staff_email);
    }
    
    echo "\n✓✓✓ Conversion completed successfully!\n";
    echo "Total staff converted: " . $converted->count() . "\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
