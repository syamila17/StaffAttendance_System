<?php
echo "Hello World\n";
echo "Test 1\n";

try {
    echo "Attempting connection...\n";
    $conn = new PDO("mysql:host=127.0.0.1;port=3307;dbname=staffAttend_data;charset=utf8mb4", "root", "root");
    echo "Connected!\n";
    $result = $conn->query('SELECT COUNT(*) as cnt FROM staff');
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "Staff count: " . $row['cnt'] . "\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
