<?php
// Health Check Endpoint with Database Connectivity
header('Content-Type: application/json');

// Get RDS connection info from environment variables
$rds_hostname = $_SERVER['RDS_HOSTNAME'] ?? 'localhost';
$rds_port = $_SERVER['RDS_PORT'] ?? '3306';
$rds_db_name = $_SERVER['RDS_DB_NAME'] ?? 'lampdb';
$rds_username = $_SERVER['RDS_USERNAME'] ?? 'admin';
$rds_password = $_SERVER['RDS_PASSWORD'] ?? '';

$health_status = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'instance_id' => @file_get_contents('http://169.254.169.254/latest/meta-data/instance-id') ?: 'unknown',
    'availability_zone' => @file_get_contents('http://169.254.169.254/latest/meta-data/placement/availability-zone') ?: 'unknown',
    'instance_type' => @file_get_contents('http://169.254.169.254/latest/meta-data/instance-type') ?: 'unknown',
    'hostname' => gethostname(),
    'php_version' => PHP_VERSION,
    'checks' => []
];

// Check database connectivity
try {
    $dsn = "mysql:host=$rds_hostname;port=$rds_port;dbname=$rds_db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $rds_username, $rds_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    // Test query
    $stmt = $pdo->query("SELECT 1");
    $stmt->fetch();
    
    $health_status['checks']['database'] = [
        'status' => 'healthy',
        'message' => 'Database connection successful',
        'hostname' => $rds_hostname,
        'database' => $rds_db_name
    ];
    
} catch (PDOException $e) {
    $health_status['status'] = 'unhealthy';
    $health_status['checks']['database'] = [
        'status' => 'unhealthy',
        'message' => 'Database connection failed',
        'error' => $e->getMessage(),
        'hostname' => $rds_hostname,
        'database' => $rds_db_name
    ];
}

// Check basic PHP functionality
$health_status['checks']['php'] = [
    'status' => 'healthy',
    'version' => PHP_VERSION,
    'extensions' => [
        'pdo' => extension_loaded('pdo') ? 'loaded' : 'missing',
        'pdo_mysql' => extension_loaded('pdo_mysql') ? 'loaded' : 'missing',
        'mysqli' => extension_loaded('mysqli') ? 'loaded' : 'missing'
    ]
];

// Check if any required extensions are missing
$required_extensions = ['pdo', 'pdo_mysql'];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $health_status['status'] = 'unhealthy';
        $health_status['checks']['php']['status'] = 'unhealthy';
        break;
    }
}

// Set HTTP status code based on health
http_response_code($health_status['status'] === 'healthy' ? 200 : 503);

// Output JSON response
echo json_encode($health_status, JSON_PRETTY_PRINT);
?>
