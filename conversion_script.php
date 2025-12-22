DB::statement('SET FOREIGN_KEY_CHECKS=0');
$staffList = DB::table('staff')->get();
echo "Found " . $staffList->count() . " staff\n";

// Get mapping of old to new IDs
$mapping = [];
$counter = 10;
foreach ($staffList as $staff) {
    $oldId = $staff->staff_id;
    $newId = 'ST1101' . str_pad($counter++, 2, '0', STR_PAD_LEFT);
    $mapping[$oldId] = $newId;
    echo "{$staff->staff_name}: {$oldId} → {$newId}\n";
}

// Modify column type
DB::statement('ALTER TABLE staff MODIFY COLUMN staff_id VARCHAR(20) NOT NULL');
echo "Column type changed\n";

// Update staff table
foreach ($mapping as $oldId => $newId) {
    DB::update('UPDATE staff SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
    DB::update('UPDATE staff_profile SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
    DB::update('UPDATE attendance SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
    DB::update('UPDATE attendance_report SET staff_id = ? WHERE staff_id = ?', [$newId, $oldId]);
}
echo "Updated all tables\n";

// Re-enable foreign keys
DB::statement('SET FOREIGN_KEY_CHECKS=1');
echo "Foreign keys re-enabled\n";

// Verify
$converted = DB::table('staff')->select('staff_id', 'staff_name')->orderBy('staff_id')->get();
echo "\nConverted Staff IDs:\n";
foreach ($converted as $c) {
    echo $c->staff_id . ": " . $c->staff_name . "\n";
}

echo "\nConversion completed!\n";
