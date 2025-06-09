<?php
// Database configuration test page
header('Content-Type: application/json');

$response = array();

// Get environment variables
$dbhost = getenv('RDS_HOSTNAME');
$dbport = getenv('RDS_PORT');
$dbname = getenv('RDS_DB_NAME');
$username = getenv('RDS_USERNAME');
$password = getenv('RDS_PASSWORD');

$response['environment_variables'] = array(
    'RDS_HOSTNAME' => $dbhost ?: 'Not set',
    'RDS_PORT' => $dbport ?: 'Not set',
    'RDS_DB_NAME' => $dbname ?: 'Not set',
    'RDS_USERNAME' => $username ?: 'Not set',
    'RDS_PASSWORD' => $password ? 'Set' : 'Not set'
);

// Use hardcoded values if environment variables are not set
if (!$dbhost) {
    $dbhost = 'lamp-database.chtjp1ydehds.us-east-1.rds.amazonaws.com';
    $dbport = '3306';
    $dbname = 'lampdb';
    $username = 'admin';
    $password = 'SecurePass123!';
}

if ($dbhost && $dbport && $dbname && $username && $password) {
    try {
        $pdo = new PDO("mysql:host=$dbhost;port=$dbport;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $response['database_connection'] = array(
            'status' => 'success',
            'message' => 'Database connection successful'
        );
        
        // Get MySQL version
        $stmt = $pdo->query('SELECT VERSION() as version');
        $version = $stmt->fetch();
        $response['mysql_version'] = $version['version'];
        
        // Test table operations
        $pdo->exec("CREATE TABLE IF NOT EXISTS health_check (
            id INT AUTO_INCREMENT PRIMARY KEY,
            check_time DATETIME,
            status VARCHAR(20)
        )");
        
        $stmt = $pdo->prepare("INSERT INTO health_check (check_time, status) VALUES (NOW(), 'OK')");
        $stmt->execute();
        
        $response['table_operations'] = 'success';
        
    } catch(PDOException $e) {
        $response['database_connection'] = array(
            'status' => 'error',
            'message' => $e->getMessage()
        );
    }
} else {
    $response['database_connection'] = array(
        'status' => 'error',
        'message' => 'Database environment variables not configured'
    );
}

$response['server_info'] = array(
    'php_version' => phpversion(),
    'server_name' => $_SERVER['SERVER_NAME'],
    'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'N/A',
    'timestamp' => date('Y-m-d H:i:s')
);

echo json_encode($response, JSON_PRETTY_PRINT);
?>
