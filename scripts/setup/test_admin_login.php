<?php

// Test admin login
require 'staff_attendance/vendor/autoload.php';
$app = require 'staff_attendance/bootstrap/app.php';

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    // Check if admin exists
    $admin = Admin::where('admin_email', 'admin@utm.edu.my')->first();
    
    if ($admin) {
        echo "✓ Admin found!\n";
        echo "  Email: " . $admin->admin_email . "\n";
        echo "  Name: " . $admin->admin_name . "\n";
        echo "\n";
        
        // Test password
        if (Hash::check('admin123', $admin->admin_password)) {
            echo "✓ Password is correct!\n";
        } else {
            echo "✗ Password is incorrect!\n";
        }
    } else {
        echo "✗ Admin not found in database!\n";
        echo "\nCreating admin user...\n";
        
        $newAdmin = Admin::create([
            'admin_name' => 'Admin User',
            'admin_email' => 'admin@utm.edu.my',
            'admin_password' => Hash::make('admin123'),
        ]);
        
        echo "✓ Admin created successfully!\n";
        echo "  ID: " . $newAdmin->admin_id . "\n";
        echo "  Email: " . $newAdmin->admin_email . "\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Login Details:\n";
    echo "Email: admin@utm.edu.my\n";
    echo "Password: admin123\n";
    echo str_repeat("=", 50) . "\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
