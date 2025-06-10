<?php
try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    echo "MySQL connection successful\n";
    $pdo->exec('CREATE DATABASE IF NOT EXISTS lampapp');
    echo "Database lampapp created/exists\n";

    // Test connection to the specific database
    $pdo = new PDO('mysql:host=localhost;dbname=lampapp;charset=utf8mb4', 'root', '');
    echo "Connection to lampapp database successful\n";
    // Test the problematic query
    $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as db_name, USER() as user, CURRENT_TIMESTAMP as server_time");
    $result = $stmt->fetch();
    echo "Query successful: " . json_encode($result) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
