<?php
/**
 * Quick test to verify current time formatting
 * 
 * Run this from the project root:
 * php test_time.php
 */

echo "=== Time Format Test ===\n\n";

// Test different time formats
echo "Current Time (using date()): " . date('H:i:s') . "\n";
echo "Current Time (using time()): " . date('H:i:s', time()) . "\n";

// Carbon test
require_once 'staff_attendance/vendor/autoload.php';
use Carbon\Carbon;

echo "Current Time (using Carbon): " . Carbon::now()->format('H:i:s') . "\n";

// Show timezone
echo "\nTimezone: " . date_default_timezone_get() . "\n";

// The difference should be 0 if timezone is correct
echo "\nAll times should match if timezone is configured correctly.\n";
echo "If they differ, the timezone in config/app.php needs to be updated.\n";
?>
