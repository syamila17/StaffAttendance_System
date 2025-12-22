<?php

require 'staff_attendance/vendor/autoload.php';
$app = require_once 'staff_attendance/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

try {
    $unusedTables = [
        'alert', 'alert_configuration', 'alert_configuration_history', 'alert_image', 
        'alert_instance', 'alert_notification', 'alert_notification_state', 'alert_rule', 
        'alert_rule_tag', 'alert_rule_version', 'annotation', 'annotation_tag', 'api_key', 
        'builtin_role', 'cache', 'cache_data', 'cache_locks', 'correlation', 'dashboard', 
        'dashboard_acl', 'dashboard_provisioning', 'dashboard_public', 'dashboard_snapshot', 
        'dashboard_tag', 'dashboard_version', 'data_keys', 'data_source', 'entity_event', 
        'failed_jobs', 'file', 'file_meta', 'job_batches', 'jobs', 'kv_store', 
        'library_element', 'library_element_connection', 'login_attempt', 'migration_log', 
        'migrations', 'ngalert_configuration', 'org', 'org_user', 'password_reset_tokens', 
        'permission', 'playlist', 'playlist_item', 'plugin_setting', 'preferences', 
        'provenance_type', 'query_history', 'query_history_details', 'query_history_star', 
        'quota', 'role', 'secrets', 'seed_assignment', 'server_lock', 'session', 
        'short_url', 'star', 'tag', 'team', 'team_member', 'team_role', 'temp_user', 
        'test_data', 'user', 'user_auth', 'user_auth_token', 'user_role', 'users'
    ];
    
    echo "\n" . str_repeat('=', 70) . "\n";
    echo "DROPPING UNUSED TABLES FROM DATABASE\n";
    echo str_repeat('=', 70) . "\n\n";
    
    $droppedCount = 0;
    $errorCount = 0;
    
    // Disable foreign key checks temporarily
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    foreach ($unusedTables as $table) {
        try {
            DB::statement("DROP TABLE IF EXISTS `{$table}`");
            echo "✓ Dropped: {$table}\n";
            $droppedCount++;
        } catch (Exception $e) {
            echo "✗ Error dropping {$table}: " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }
    
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    echo "\n" . str_repeat('=', 70) . "\n";
    echo "SUMMARY:\n";
    echo "✓ Tables dropped: {$droppedCount}\n";
    echo "✗ Errors: {$errorCount}\n";
    echo str_repeat('=', 70) . "\n";
    
    // Show remaining tables
    echo "\n\nREMAINING TABLES IN DATABASE:\n";
    echo str_repeat('-', 70) . "\n";
    
    $tables = DB::select('SHOW TABLES FROM staffAttend_data');
    $allTables = [];
    foreach ($tables as $table) {
        foreach ($table as $tableName) {
            $allTables[] = $tableName;
        }
    }
    
    foreach ($allTables as $table) {
        $rowCount = DB::table($table)->count();
        echo "✓ {$table}: {$rowCount} rows\n";
    }
    
    echo "\n" . str_repeat('=', 70) . "\n";
    echo "TOTAL TABLES NOW: " . count($allTables) . "\n";
    echo str_repeat('=', 70) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
