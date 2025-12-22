<?php

$host = "127.0.0.1";
$ports = [3306, 3307, 3308];
$user = "root";
$pass = "root";
$db = "staffAttend_data";

echo "Testing MySQL connections...\n\n";

foreach ($ports as $port) {
    echo "Testing port: $port... ";
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$db",
            $user,
            $pass,
            [PDO::ATTR_TIMEOUT => 3]
        );
        echo "✓ SUCCESS!\n";
        echo "Port $port is correct!\n\n";
        
        // Test query
        $result = $pdo->query("SELECT COUNT(*) as admin_count FROM admin");
        $data = $result->fetch();
        echo "Admin count: " . $data['admin_count'] . "\n";
        break;
    } catch (PDOException $e) {
        echo "✗ Failed\n";
    }
}
