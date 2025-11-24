#!/usr/bin/env php
<?php
/**
 * Test script to verify Staff Attendance System authentication
 */

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Staff;
use App\Models\Admin;

echo "\n========== STAFF ATTENDANCE SYSTEM - LOGIN DEBUG TEST ==========\n\n";

// Test 1: Check database connection
echo "1. Testing Database Connection...\n";
try {
    $count = DB::table('staff')->count();
    echo "   ✓ Database connected. Staff records: $count\n\n";
} catch (\Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Check staff data
echo "2. Checking Staff Data...\n";
$staff = Staff::all();
if ($staff->isEmpty()) {
    echo "   ✗ No staff records found!\n\n";
} else {
    echo "   ✓ Found " . $staff->count() . " staff records:\n";
    foreach ($staff as $s) {
        echo "     - {$s->staff_name} ({$s->staff_email})\n";
    }
    echo "\n";
}

// Test 3: Check password hashing for test@utm.edu.my
echo "3. Testing Staff Login (test@utm.edu.my)...\n";
$testStaff = Staff::where('staff_email', 'test@utm.edu.my')->first();
if (!$testStaff) {
    echo "   ✗ Test staff not found\n\n";
} else {
    echo "   ✓ Test staff found: {$testStaff->staff_name}\n";
    echo "   - Password hash: " . substr($testStaff->staff_password, 0, 20) . "...\n";
    
    $passwordCheck = Hash::check('password123', $testStaff->staff_password);
    echo "   - Password check (password123): " . ($passwordCheck ? "✓ PASS" : "✗ FAIL") . "\n\n";
    
    if (!$passwordCheck) {
        echo "   ISSUE: Password doesn't match! The password might be stored incorrectly.\n\n";
    }
}

// Test 4: Check admin data
echo "4. Testing Admin Login (admin@utm.edu.my)...\n";
$admin = Admin::where('admin_email', 'admin@utm.edu.my')->first();
if (!$admin) {
    echo "   ✗ Admin not found\n\n";
} else {
    echo "   ✓ Admin found: {$admin->admin_name}\n";
    echo "   - Password hash: " . substr($admin->admin_password, 0, 20) . "...\n";
    
    $adminPasswordCheck = Hash::check('admin123', $admin->admin_password);
    echo "   - Password check (admin123): " . ($adminPasswordCheck ? "✓ PASS" : "✗ FAIL") . "\n\n";
}

// Test 5: Check sessions table
echo "5. Checking Sessions Table...\n";
if (Schema::hasTable('sessions')) {
    echo "   ✓ Sessions table exists\n";
    $sessionCount = DB::table('sessions')->count();
    echo "   - Current sessions: $sessionCount\n\n";
} else {
    echo "   ✗ Sessions table does NOT exist!\n\n";
}

// Test 6: Summary
echo "========== SUMMARY ==========\n";
if ($testStaff && Hash::check('password123', $testStaff->staff_password)) {
    echo "✓ Staff login should work with:\n";
    echo "  Email: test@utm.edu.my\n";
    echo "  Password: password123\n\n";
} else {
    echo "✗ Staff login is broken. See issues above.\n\n";
}

if ($admin && Hash::check('admin123', $admin->admin_password)) {
    echo "✓ Admin login should work with:\n";
    echo "  Email: admin@utm.edu.my\n";
    echo "  Password: admin123\n\n";
} else {
    echo "✗ Admin login is broken. See issues above.\n\n";
}
