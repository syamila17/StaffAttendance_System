<?php
/**
 * Direct MySQL conversion for staff_ids
 */

ob_start(); // Start output buffering

// Database connection
$host = '127.0.0.1';
$port = 3307;
$user = 'root';
$pass = 'root';
$db = 'staffAttend_data';

try {
    echo "=== Staff ID Conversion Script ===\n\n";
    ob_flush();
    flush();
    
    echo "Connecting to MySQL at $host:$port...\n";
    ob_flush();
    flush();
    
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✓ Connected\n\n";
    ob_flush();
    flush();
    
    // Step 1: Disable foreign key checks
    echo "Step 1: Disabling foreign key checks...\n";
    ob_flush();
    flush();
    
    $conn->exec('SET FOREIGN_KEY_CHECKS=0');
    echo "✓ Done\n\n";
    ob_flush();
    flush();
    
    // Step 2: Get all staff
    echo "Step 2: Fetching staff records...\n";
    ob_flush();
    flush();
    
    $stmt = $conn->query('SELECT staff_id, staff_name FROM staff ORDER BY staff_id');
    $staffList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($staffList) . " staff members\n\n";
    ob_flush();
    flush();
    
    // Step 3: Create mapping
    echo "Step 3: Creating ID mapping...\n";
    $mapping = [];
    $counter = 10;
    foreach ($staffList as $staff) {
        $oldId = $staff['staff_id'];
        $newId = 'ST1101' . str_pad($counter++, 2, '0', STR_PAD_LEFT);
        $mapping[$oldId] = $newId;
        printf("  %s: %d → %s\n", $staff['staff_name'], $oldId, $newId);
    }
    
    // Step 4: Change column type
    echo "\nStep 4: Changing staff_id column type...\n";
    $conn->exec('ALTER TABLE staff MODIFY COLUMN staff_id VARCHAR(20) NOT NULL');
    echo "✓ Column type changed\n";
    
    // Step 5: Update staff table
    echo "\nStep 5: Updating staff table...\n";
    $updateStaff = $conn->prepare('UPDATE staff SET staff_id = ? WHERE staff_id = ?');
    foreach ($mapping as $oldId => $newId) {
        $updateStaff->execute([$newId, $oldId]);
        echo "  ✓ {$oldId} → {$newId}\n";
    }
    
    // Step 6: Update related tables
    echo "\nStep 6: Updating related tables...\n";
    $updateProfile = $conn->prepare('UPDATE staff_profile SET staff_id = ? WHERE staff_id = ?');
    $updateAttend = $conn->prepare('UPDATE attendance SET staff_id = ? WHERE staff_id = ?');
    $updateReport = $conn->prepare('UPDATE attendance_report SET staff_id = ? WHERE staff_id = ?');
    
    foreach ($mapping as $oldId => $newId) {
        try { $updateProfile->execute([$newId, $oldId]); } catch (Exception $e) {}
        try { $updateAttend->execute([$newId, $oldId]); } catch (Exception $e) {}
        try { $updateReport->execute([$newId, $oldId]); } catch (Exception $e) {}
    }
    echo "✓ All related tables updated\n";
    
    // Step 7: Re-enable foreign keys
    echo "\nStep 7: Re-enabling foreign key checks...\n";
    $conn->exec('SET FOREIGN_KEY_CHECKS=1');
    echo "✓ Foreign key checks re-enabled\n";
    
    // Step 8: Verify
    echo "\n=== Verification ===\n";
    $stmt = $conn->query('SELECT staff_id, staff_name, staff_email FROM staff ORDER BY staff_id');
    $converted = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($converted as $c) {
        printf("  %s: %s (%s)\n", $c['staff_id'], $c['staff_name'], $c['staff_email']);
    }
    
    echo "\n✓✓✓ Conversion completed successfully!\n";
    echo "Total staff converted: " . count($converted) . "\n";
    
} catch (PDOException $e) {
    echo "\n✗ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
