<?php
// This script converts staff_ids from numeric to formatted (ST110110, ST110111, etc.)

require_once 'staff_attendance/bootstrap/autoload.php';
require_once 'staff_attendance/bootstrap/app.php';

$app = require_once 'staff_attendance/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // Step 1: Add a temporary column to store the old staff_id
    DB::statement('ALTER TABLE staff ADD COLUMN old_staff_id BIGINT UNSIGNED NULL');
    echo "✓ Added temporary old_staff_id column\n";

    // Step 2: Back up the existing staff_ids
    DB::statement('UPDATE staff SET old_staff_id = staff_id');
    echo "✓ Backed up existing staff_ids\n";

    // Step 3: Get all staff and generate new formatted IDs
    $staff = DB::table('staff')->orderBy('id')->get();
    $counter = 10;
    
    foreach ($staff as $s) {
        $newStaffId = 'ST1101' . str_pad($counter++, 2, '0', STR_PAD_LEFT);
        DB::table('staff')->where('id', $s->id)->update(['staff_id' => $newStaffId]);
        echo "✓ Staff ID {$s->id}: {$s->staff_id} → {$newStaffId}\n";
    }

    // Step 4: Make staff_id column VARCHAR if it's not already
    // This may fail if it's already been converted, so we suppress the error
    try {
        DB::statement('ALTER TABLE staff MODIFY COLUMN staff_id VARCHAR(20)');
        echo "✓ Modified staff_id column to VARCHAR(20)\n";
    } catch (\Exception $e) {
        echo "ℹ staff_id already VARCHAR, skipping column modification\n";
    }

    // Step 5: Add unique constraint on staff_id
    try {
        DB::statement('ALTER TABLE staff ADD UNIQUE KEY unique_staff_id (staff_id)');
        echo "✓ Added unique constraint on staff_id\n";
    } catch (\Exception $e) {
        echo "ℹ Unique constraint may already exist: " . $e->getMessage() . "\n";
    }

    // Step 6: Drop the temporary column
    DB::statement('ALTER TABLE staff DROP COLUMN old_staff_id');
    echo "✓ Dropped temporary old_staff_id column\n";

    // Verify: Check the converted staff_ids
    $converted = DB::table('staff')->select('id', 'staff_id', 'staff_name', 'staff_email')->orderBy('id')->get();
    echo "\n=== Converted Staff IDs ===\n";
    foreach ($converted as $c) {
        echo "{$c->id}: {$c->staff_id} - {$c->staff_name}\n";
    }

    DB::commit();
    echo "\n✓ Migration completed successfully!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
