<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$db = $app->make('db');

try {
    $pdo = $db->connection()->getPdo();
    echo "SUCCESS: Database connected without errors!\n";
    echo "PDO Status: OK\n";
    
    // Try a simple query
    $result = $db->select('SELECT 1 as test');
    echo "Query Test: OK\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
