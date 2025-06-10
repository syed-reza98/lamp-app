<?php

/**
 * Enhanced Health Check Endpoint for AWS LAMP Stack
 * Assignment 3 - Comprehensive Health Monitoring
 * 
 * Student: Anika Arman
 * Student ID: 14425754
 * 
 * This endpoint provides detailed health information for load balancer
 * health checks and monitoring systems.
 */

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Start timing
$start_time = microtime(true);

// Environment Configuration
$environment = [
    'rds_hostname' => $_SERVER['RDS_HOSTNAME'] ?? 'localhost',
    'rds_port' => $_SERVER['RDS_PORT'] ?? '3306',
    'rds_db_name' => $_SERVER['RDS_DB_NAME'] ?? 'lampdb',
    'rds_username' => $_SERVER['RDS_USERNAME'] ?? 'admin',
    'rds_password' => $_SERVER['RDS_PASSWORD'] ?? ''
];

/**
 * Get AWS Instance Metadata
 */
function getInstanceMetadata($path)
{
    $token_context = stream_context_create([
        'http' => [
            'method' => 'PUT',
            'header' => "X-aws-ec2-metadata-token-ttl-seconds: 21600\r\n",
            'timeout' => 3
        ]
    ]);

    $token = @file_get_contents('http://169.254.169.254/latest/api/token', false, $token_context);

    if (!$token) {
        return false;
    }

    $metadata_context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "X-aws-ec2-metadata-token: $token\r\n",
            'timeout' => 3
        ]
    ]);

    try {
        return @file_get_contents("http://169.254.169.254/latest/meta-data/$path", false, $metadata_context);
    } catch (Exception $e) {
        return false;
    }
}

// Initialize health status
$health_status = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'response_time_ms' => 0,
    'version' => '1.0.0',
    'environment' => 'production',
    'instance' => [
        'id' => getInstanceMetadata('instance-id') ?: 'unknown',
        'type' => getInstanceMetadata('instance-type') ?: 'unknown',
        'availability_zone' => getInstanceMetadata('placement/availability-zone') ?: 'unknown',
        'local_ipv4' => getInstanceMetadata('local-ipv4') ?: 'unknown',
        'public_ipv4' => getInstanceMetadata('public-ipv4') ?: 'N/A',
        'hostname' => gethostname(),
        'security_groups' => getInstanceMetadata('security-groups') ?: 'unknown'
    ],
    'checks' => [],
    'metrics' => []
];

// Check 1: PHP Environment
$php_check_start = microtime(true);
try {
    $required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
    $missing_extensions = [];

    foreach ($required_extensions as $ext) {
        if (!extension_loaded($ext)) {
            $missing_extensions[] = $ext;
        }
    }

    if (empty($missing_extensions)) {
        $health_status['checks']['php'] = [
            'status' => 'healthy',
            'message' => 'PHP environment operational',
            'version' => PHP_VERSION,
            'extensions' => $required_extensions,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time')
        ];
    } else {
        $health_status['status'] = 'unhealthy';
        $health_status['checks']['php'] = [
            'status' => 'unhealthy',
            'message' => 'Missing required PHP extensions',
            'missing_extensions' => $missing_extensions
        ];
    }
} catch (Exception $e) {
    $health_status['status'] = 'unhealthy';
    $health_status['checks']['php'] = [
        'status' => 'error',
        'message' => 'PHP check failed: ' . $e->getMessage()
    ];
}
$health_status['checks']['php']['response_time_ms'] = round((microtime(true) - $php_check_start) * 1000, 2);


// Check 3: File System
$fs_check_start = microtime(true);
try {
    $disk_free = disk_free_space('/');
    $disk_total = disk_total_space('/');
    $disk_used_percent = round((($disk_total - $disk_free) / $disk_total) * 100, 2);

    $temp_file = tempnam(sys_get_temp_dir(), 'health_check_');
    if ($temp_file && file_put_contents($temp_file, 'test') && unlink($temp_file)) {
        $health_status['checks']['filesystem'] = [
            'status' => $disk_used_percent < 90 ? 'healthy' : 'warning',
            'message' => 'File system operational',
            'disk_free_bytes' => $disk_free,
            'disk_total_bytes' => $disk_total,
            'disk_used_percent' => $disk_used_percent,
            'temp_dir' => sys_get_temp_dir(),
            'writable' => true
        ];

        if ($disk_used_percent >= 90) {
            $health_status['status'] = 'warning';
            $health_status['checks']['filesystem']['message'] = 'Low disk space warning';
        }
    } else {
        $health_status['status'] = 'unhealthy';
        $health_status['checks']['filesystem'] = [
            'status' => 'unhealthy',
            'message' => 'File system write test failed',
            'writable' => false
        ];
    }
} catch (Exception $e) {
    $health_status['status'] = 'unhealthy';
    $health_status['checks']['filesystem'] = [
        'status' => 'error',
        'message' => 'File system check failed: ' . $e->getMessage()
    ];
}
$health_status['checks']['filesystem']['response_time_ms'] = round((microtime(true) - $fs_check_start) * 1000, 2);

// Check 4: Memory Usage
$memory_check_start = microtime(true);
try {
    $memory_usage = memory_get_usage(true);
    $memory_peak = memory_get_peak_usage(true);
    $memory_limit = ini_get('memory_limit');

    // Convert memory limit to bytes
    $memory_limit_bytes = 0;
    if ($memory_limit !== '-1') {
        $memory_limit_bytes = (int)$memory_limit;
        $unit = strtoupper(substr($memory_limit, -1));
        switch ($unit) {
            case 'G':
                $memory_limit_bytes *= 1024;
            case 'M':
                $memory_limit_bytes *= 1024;
            case 'K':
                $memory_limit_bytes *= 1024;
        }
    }

    $memory_usage_percent = $memory_limit_bytes > 0 ? round(($memory_usage / $memory_limit_bytes) * 100, 2) : 0;

    $health_status['checks']['memory'] = [
        'status' => $memory_usage_percent < 80 ? 'healthy' : 'warning',
        'message' => 'Memory usage within normal limits',
        'usage_bytes' => $memory_usage,
        'peak_bytes' => $memory_peak,
        'limit_bytes' => $memory_limit_bytes,
        'usage_percent' => $memory_usage_percent,
        'limit_string' => $memory_limit
    ];

    if ($memory_usage_percent >= 80) {
        $health_status['status'] = 'warning';
        $health_status['checks']['memory']['message'] = 'High memory usage warning';
    }
} catch (Exception $e) {
    $health_status['checks']['memory'] = [
        'status' => 'error',
        'message' => 'Memory check failed: ' . $e->getMessage()
    ];
}
$health_status['checks']['memory']['response_time_ms'] = round((microtime(true) - $memory_check_start) * 1000, 2);

// Check 5: Load Average (Linux only)
$load_check_start = microtime(true);
try {
    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        $health_status['checks']['load'] = [
            'status' => $load[0] < 2.0 ? 'healthy' : 'warning',
            'message' => 'System load normal',
            'load_1min' => $load[0],
            'load_5min' => $load[1],
            'load_15min' => $load[2]
        ];

        if ($load[0] >= 2.0) {
            $health_status['status'] = 'warning';
            $health_status['checks']['load']['message'] = 'High system load detected';
        }
    } else {
        $health_status['checks']['load'] = [
            'status' => 'info',
            'message' => 'Load average not available on this platform'
        ];
    }
} catch (Exception $e) {
    $health_status['checks']['load'] = [
        'status' => 'error',
        'message' => 'Load check failed: ' . $e->getMessage()
    ];
}
$health_status['checks']['load']['response_time_ms'] = round((microtime(true) - $load_check_start) * 1000, 2);

// Calculate overall response time
$health_status['response_time_ms'] = round((microtime(true) - $start_time) * 1000, 2);

// Add performance metrics
$health_status['metrics'] = [
    'uptime_seconds' => (int)$_SERVER['REQUEST_TIME'],
    'current_timestamp' => time(),
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
];

// Set appropriate HTTP status code
$http_status_code = 200;
switch ($health_status['status']) {
    case 'healthy':
        $http_status_code = 200;
        break;
    case 'warning':
        $http_status_code = 200; // Still operational
        break;
    case 'unhealthy':
        $http_status_code = 503; // Service Unavailable
        break;
    default:
        $http_status_code = 500; // Internal Server Error
}

http_response_code($http_status_code);

// Add summary information
$healthy_checks = array_filter($health_status['checks'], function ($check) {
    return isset($check['status']) && $check['status'] === 'healthy';
});

$warning_checks = array_filter($health_status['checks'], function ($check) {
    return isset($check['status']) && $check['status'] === 'warning';
});

$unhealthy_checks = array_filter($health_status['checks'], function ($check) {
    return isset($check['status']) && ($check['status'] === 'unhealthy' || $check['status'] === 'error');
});

$health_status['summary'] = [
    'total_checks' => count($health_status['checks']),
    'healthy_checks' => count($healthy_checks),
    'warning_checks' => count($warning_checks),
    'unhealthy_checks' => count($unhealthy_checks),
    'overall_status' => $health_status['status'],
    'response_time_ms' => $health_status['response_time_ms']
];

// Output the health status as JSON
echo json_encode($health_status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// Log health check to database if available
if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $stmt = $pdo->prepare("INSERT INTO lamp_health_checks (instance_id, check_type, status, response_time_ms, details) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $health_status['instance']['id'],
            'COMPREHENSIVE_HEALTH_CHECK',
            strtoupper($health_status['status']),
            $health_status['response_time_ms'],
            json_encode($health_status['summary'])
        ]);
    } catch (Exception $e) {
        // Silently fail - don't break health check if logging fails
        error_log("Health check logging failed: " . $e->getMessage());
    }
}