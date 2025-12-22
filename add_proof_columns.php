<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/');
$kernel->handle($request);

try {
    // Check if columns exist before adding
    $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='leave_requests' AND COLUMN_NAME IN ('proof_file', 'proof_file_path', 'proof_uploaded_at')");
    
    $existingColumns = array_column($columns, 'COLUMN_NAME');
    
    if (!in_array('proof_file', $existingColumns)) {
        DB::statement('ALTER TABLE leave_requests ADD COLUMN proof_file VARCHAR(255) NULL');
        echo "✓ Added proof_file column\n";
    }
    
    if (!in_array('proof_file_path', $existingColumns)) {
        DB::statement('ALTER TABLE leave_requests ADD COLUMN proof_file_path VARCHAR(255) NULL');
        echo "✓ Added proof_file_path column\n";
    }
    
    if (!in_array('proof_uploaded_at', $existingColumns)) {
        DB::statement('ALTER TABLE leave_requests ADD COLUMN proof_uploaded_at TIMESTAMP NULL');
        echo "✓ Added proof_uploaded_at column\n";
    }
    
    echo "\n✓ All proof file columns added successfully!\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
