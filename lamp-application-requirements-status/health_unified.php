<?php

/**
 * Unified Health Check Endpoint for AWS LAMP Stack
 * Consolidates basic and enhanced health check functionality
 * 
 * Student: Anika Arman
 * Student ID: 14425754
 */

// Load helper functions
require_once 'includes/helpers.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Start timing
$start_time = microtime(true);

// Environment Configuration
$environment = [
    'rds_hostname' => $_SERVER['RDS_HOSTNAME'] ?? 'lamp-app-db.cijnrr1efnu0.us-east-1.rds.amazonaws.com',
    'rds_port' => $_SERVER['RDS_PORT'] ?? '3306',
    'rds_db_name' => $_SERVER['RDS_DB_NAME'] ?? 'lampapp',
    'rds_username' => $_SERVER['RDS_USERNAME'] ?? 'lampdbadmin',
    'rds_password' => $_SERVER['RDS_PASSWORD'] ?? 'SecurePass123!'
];

// Get detail level from query parameter
$detail_level = $_GET['detail'] ?? 'basic';

// Initialize health status
$health_status = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'response_time_ms' => 0,
    'instance_id' => getInstanceMetadata('instance-id') ?: 'unknown',
    'availability_zone' => getInstanceMetadata('placement/availability-zone') ?: 'unknown',
    'instance_type' => getInstanceMetadata('instance-type') ?: 'unknown',
    'hostname' => gethostname(),
    'php_version' => PHP_VERSION,
    'checks' => []
];

// Basic Health Checks
try {
    // Database connectivity check
    $db_start = microtime(true);
    $pdo = getDatabaseConnection($environment);
    $db_response_time = round((microtime(true) - $db_start) * 1000, 2);

    // Test basic database query
    $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as db_name, USER() as user, CURRENT_TIMESTAMP() as current_time");
    $db_info = $stmt->fetch();

    $health_status['checks']['database'] = [
        'status' => 'healthy',
        'response_time_ms' => $db_response_time,
        'message' => 'Database connection successful',
        'hostname' => $environment['rds_hostname'],
        'database' => $environment['rds_db_name'],
        'version' => $db_info['version'],
        'current_time' => $db_info['current_time']
    ];
} catch (PDOException $e) {
    $health_status['status'] = 'unhealthy';
    $health_status['checks']['database'] = [
        'status' => 'unhealthy',
        'message' => 'Database connection failed',
        'error' => $e->getMessage(),
        'hostname' => $environment['rds_hostname'],
        'database' => $environment['rds_db_name']
    ];
}

// Enhanced checks if requested
if ($detail_level === 'enhanced' && $health_status['status'] === 'healthy') {
    try {
        // Performance metrics
        $perf_stmt = $pdo->query("SHOW STATUS WHERE Variable_name IN ('Threads_connected', 'Uptime', 'Questions', 'Slow_queries', 'Connections')");
        $performance = [];
        while ($row = $perf_stmt->fetch()) {
            $performance[$row['Variable_name']] = $row['Value'];
        }
        $health_status['checks']['database']['performance'] = $performance;

        // System checks
        $health_status['checks']['system'] = [
            'status' => 'healthy',
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'memory_peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'memory_limit' => ini_get('memory_limit'),
            'disk_free_gb' => round(disk_free_space('/') / 1024 / 1024 / 1024, 2),
            'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg() : 'N/A'
        ];

        // Application checks
        $health_status['checks']['application'] = [
            'status' => 'healthy',
            'environment' => $_SERVER['ENVIRONMENT'] ?? 'production',
            'deployment_version' => $_SERVER['VERSION_LABEL'] ?? 'unknown',
            'uptime_seconds' => time() - $_SERVER['REQUEST_TIME'],
            'request_time' => $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true)
        ];

        // AWS service checks
        $health_status['checks']['aws'] = [
            'status' => 'healthy',
            'imds_accessible' => getInstanceMetadata('instance-id') !== false,
            'vpc_id' => getInstanceMetadata('network/interfaces/macs/' .
                getInstanceMetadata('mac') . '/vpc-id') ?: 'unknown',
            'security_groups' => getInstanceMetadata('security-groups') ?: 'unknown'
        ];
    } catch (Exception $e) {
        $health_status['checks']['enhanced_error'] = [
            'message' => 'Enhanced checks failed: ' . $e->getMessage()
        ];
    }
}

// Calculate total response time
$health_status['response_time_ms'] = round((microtime(true) - $start_time) * 1000, 2);

// Set appropriate HTTP status code
if ($health_status['status'] === 'unhealthy') {
    http_response_code(503); // Service Unavailable
} else {
    http_response_code(200); // OK
}

// Output health status
echo json_encode($health_status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
