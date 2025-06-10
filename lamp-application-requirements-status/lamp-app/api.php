<?php

/**
 * API Endpoint for AWS LAMP Stack Application
 * Assignment 3 - RESTful API Demonstration
 * 
 * Student: Anika Arman
 * Student ID: 14425754
 * 
 * This API provides endpoints to demonstrate the LAMP stack functionality
 * and AWS integration capabilities.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Environment Configuration
$environment = [
    'rds_hostname' => $_SERVER['RDS_HOSTNAME'] ?? 'localhost',
    'rds_port' => $_SERVER['RDS_PORT'] ?? '3306',
    'rds_db_name' => $_SERVER['RDS_DB_NAME'] ?? 'lampdb',
    'rds_username' => $_SERVER['RDS_USERNAME'] ?? 'admin',
    'rds_password' => $_SERVER['RDS_PASSWORD'] ?? ''
];

// API Response structure
$response = [
    'status' => 'success',
    'timestamp' => date('c'),
    'data' => null,
    'meta' => [
        'instance_id' => 'unknown',
        'availability_zone' => 'unknown',
        'method' => $_SERVER['REQUEST_METHOD'],
        'endpoint' => $_SERVER['REQUEST_URI'],
        'version' => '1.0.0'
    ],
    'errors' => []
];

// Get AWS instance metadata
function getInstanceMetadata($path)
{
    $token_context = stream_context_create([
        'http' => [
            'method' => 'PUT',
            'header' => "X-aws-ec2-metadata-token-ttl-seconds: 21600\r\n",
            'timeout' => 2
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
            'timeout' => 2
        ]
    ]);

    return @file_get_contents("http://169.254.169.254/latest/meta-data/$path", false, $metadata_context);
}

$response['meta']['instance_id'] = getInstanceMetadata('instance-id') ?: 'unknown';
$response['meta']['availability_zone'] = getInstanceMetadata('placement/availability-zone') ?: 'unknown';

// Database connection
$pdo = null;
try {
    $dsn = "mysql:host={$environment['rds_hostname']};port={$environment['rds_port']};dbname={$environment['rds_db_name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $environment['rds_username'], $environment['rds_password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    $response['status'] = 'error';
    $response['errors'][] = 'Database connection failed: ' . $e->getMessage();
    http_response_code(503);
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit();
}

// Parse request
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

// Remove script name from path if present
if (end($path_parts) === 'api.php') {
    array_pop($path_parts);
}

$endpoint = $path_parts[count($path_parts) - 1] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Get request data
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$query_params = $_GET;

try {
    switch ($endpoint) {
        case 'status':
            // System status endpoint
            if ($method === 'GET') {
                $stmt = $pdo->query("SELECT VERSION() as db_version, NOW() as db_time");
                $db_info = $stmt->fetch();

                $response['data'] = [
                    'system' => [
                        'status' => 'operational',
                        'php_version' => PHP_VERSION,
                        'server_time' => date('c'),
                        'uptime' => $_SERVER['REQUEST_TIME'],
                        'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB'
                    ],
                    'database' => [
                        'status' => 'connected',
                        'version' => $db_info['db_version'],
                        'time' => $db_info['db_time'],
                        'hostname' => $environment['rds_hostname']
                    ],
                    'aws' => [
                        'instance_id' => $response['meta']['instance_id'],
                        'availability_zone' => $response['meta']['availability_zone'],
                        'region' => substr($response['meta']['availability_zone'], 0, -1)
                    ]
                ];
            } else {
                http_response_code(405);
                $response['status'] = 'error';
                $response['errors'][] = 'Method not allowed';
            }
            break;

        case 'logs':
            // Application logs endpoint
            if ($method === 'GET') {
                $limit = isset($query_params['limit']) ? min(100, max(1, (int)$query_params['limit'])) : 20;
                $severity = $query_params['severity'] ?? null;
                $category = $query_params['category'] ?? null;

                $sql = "SELECT * FROM lamp_application_logs WHERE 1=1";
                $params = [];

                if ($severity) {
                    $sql .= " AND severity = ?";
                    $params[] = strtoupper($severity);
                }

                if ($category) {
                    $sql .= " AND category = ?";
                    $params[] = strtoupper($category);
                }

                $sql .= " ORDER BY created_at DESC LIMIT ?";
                $params[] = $limit;

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $logs = $stmt->fetchAll();

                $response['data'] = $logs;
                $response['meta']['count'] = count($logs);
                $response['meta']['limit'] = $limit;
            } elseif ($method === 'POST') {
                // Create new log entry
                $message = $input['message'] ?? '';
                $severity = strtoupper($input['severity'] ?? 'INFO');
                $category = strtoupper($input['category'] ?? 'GENERAL');

                if (empty($message)) {
                    http_response_code(400);
                    $response['status'] = 'error';
                    $response['errors'][] = 'Message is required';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO lamp_application_logs (instance_id, availability_zone, message, severity, category) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $response['meta']['instance_id'],
                        $response['meta']['availability_zone'],
                        $message,
                        $severity,
                        $category
                    ]);

                    $log_id = $pdo->lastInsertId();
                    $response['data'] = ['id' => $log_id, 'message' => 'Log entry created successfully'];
                }
            } else {
                http_response_code(405);
                $response['status'] = 'error';
                $response['errors'][] = 'Method not allowed';
            }
            break;

        case 'metrics':
            // Application metrics endpoint
            if ($method === 'GET') {
                $limit = isset($query_params['limit']) ? min(100, max(1, (int)$query_params['limit'])) : 50;
                $metric_name = $query_params['metric'] ?? null;

                $sql = "SELECT * FROM lamp_application_metrics WHERE 1=1";
                $params = [];

                if ($metric_name) {
                    $sql .= " AND metric_name = ?";
                    $params[] = $metric_name;
                }

                $sql .= " ORDER BY recorded_at DESC LIMIT ?";
                $params[] = $limit;

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $metrics = $stmt->fetchAll();

                $response['data'] = $metrics;
                $response['meta']['count'] = count($metrics);
            } elseif ($method === 'POST') {
                // Record new metric
                $metric_name = $input['metric_name'] ?? '';
                $metric_value = $input['metric_value'] ?? 0;
                $metric_unit = $input['metric_unit'] ?? 'count';
                $tags = $input['tags'] ?? [];

                if (empty($metric_name)) {
                    http_response_code(400);
                    $response['status'] = 'error';
                    $response['errors'][] = 'Metric name is required';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO lamp_application_metrics (instance_id, metric_name, metric_value, metric_unit, tags) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $response['meta']['instance_id'],
                        $metric_name,
                        $metric_value,
                        $metric_unit,
                        json_encode($tags)
                    ]);

                    $metric_id = $pdo->lastInsertId();
                    $response['data'] = ['id' => $metric_id, 'message' => 'Metric recorded successfully'];
                }
            } else {
                http_response_code(405);
                $response['status'] = 'error';
                $response['errors'][] = 'Method not allowed';
            }
            break;

        case 'users':
            // Users endpoint
            if ($method === 'GET') {
                $limit = isset($query_params['limit']) ? min(50, max(1, (int)$query_params['limit'])) : 10;
                $status = $query_params['status'] ?? null;

                $sql = "SELECT id, username, email, full_name, status, created_at, last_login FROM lamp_users WHERE 1=1";
                $params = [];

                if ($status) {
                    $sql .= " AND status = ?";
                    $params[] = strtoupper($status);
                }

                $sql .= " ORDER BY created_at DESC LIMIT ?";
                $params[] = $limit;

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $users = $stmt->fetchAll();

                $response['data'] = $users;
                $response['meta']['count'] = count($users);
            } elseif ($method === 'POST') {
                // Create new user
                $username = $input['username'] ?? '';
                $email = $input['email'] ?? '';
                $full_name = $input['full_name'] ?? '';
                $status = strtoupper($input['status'] ?? 'ACTIVE');

                if (empty($username) || empty($email) || empty($full_name)) {
                    http_response_code(400);
                    $response['status'] = 'error';
                    $response['errors'][] = 'Username, email, and full_name are required';
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO lamp_users (username, email, full_name, status) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$username, $email, $full_name, $status]);

                        $user_id = $pdo->lastInsertId();
                        $response['data'] = ['id' => $user_id, 'message' => 'User created successfully'];
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) { // Duplicate entry
                            http_response_code(409);
                            $response['status'] = 'error';
                            $response['errors'][] = 'Username or email already exists';
                        } else {
                            throw $e;
                        }
                    }
                }
            } else {
                http_response_code(405);
                $response['status'] = 'error';
                $response['errors'][] = 'Method not allowed';
            }
            break;

        case 'health':
            // Detailed health endpoint
            if ($method === 'GET') {
                $checks = [];

                // Database check
                $start = microtime(true);
                $stmt = $pdo->query("SELECT 1");
                $db_response_time = round((microtime(true) - $start) * 1000, 2);

                $checks['database'] = [
                    'status' => 'healthy',
                    'response_time_ms' => $db_response_time,
                    'hostname' => $environment['rds_hostname']
                ];

                // Memory check
                $memory_usage = memory_get_usage(true);
                $memory_limit = ini_get('memory_limit');
                $checks['memory'] = [
                    'status' => 'healthy',
                    'usage_bytes' => $memory_usage,
                    'usage_mb' => round($memory_usage / 1024 / 1024, 2),
                    'limit' => $memory_limit
                ];

                // Disk check
                $disk_free = disk_free_space('/');
                $disk_total = disk_total_space('/');
                $checks['disk'] = [
                    'status' => $disk_free > (1024 * 1024 * 1024) ? 'healthy' : 'warning',
                    'free_bytes' => $disk_free,
                    'total_bytes' => $disk_total,
                    'usage_percent' => round((($disk_total - $disk_free) / $disk_total) * 100, 2)
                ];

                $response['data'] = [
                    'overall_status' => 'healthy',
                    'checks' => $checks,
                    'timestamp' => date('c')
                ];
            } else {
                http_response_code(405);
                $response['status'] = 'error';
                $response['errors'][] = 'Method not allowed';
            }
            break;

        default:
            // API documentation endpoint
            if (empty($endpoint)) {
                $response['data'] = [
                    'message' => 'AWS LAMP Stack API - Assignment 3',
                    'version' => '1.0.0',
                    'student' => 'Anika Arman (14425754)',
                    'endpoints' => [
                        'GET /status' => 'System status information',
                        'GET /logs' => 'Application logs (supports ?severity, ?category, ?limit)',
                        'POST /logs' => 'Create log entry',
                        'GET /metrics' => 'Application metrics (supports ?metric, ?limit)',
                        'POST /metrics' => 'Record metric',
                        'GET /users' => 'User management (supports ?status, ?limit)',
                        'POST /users' => 'Create user',
                        'GET /health' => 'Detailed health checks'
                    ],
                    'documentation' => 'https://docs.aws.amazon.com/elasticbeanstalk/',
                    'aws_services' => [
                        'Elastic Beanstalk',
                        'EC2',
                        'RDS Multi-AZ',
                        'Auto Scaling',
                        'Load Balancer',
                        'VPC',
                        'CloudWatch',
                        'SNS'
                    ]
                ];
            } else {
                http_response_code(404);
                $response['status'] = 'error';
                $response['errors'][] = 'Endpoint not found';
            }
            break;
    }
} catch (PDOException $e) {
    $response['status'] = 'error';
    $response['errors'][] = 'Database error: ' . $e->getMessage();
    http_response_code(500);
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['errors'][] = 'Server error: ' . $e->getMessage();
    http_response_code(500);
}

// Set appropriate HTTP status code if not already set
if ($response['status'] === 'error' && http_response_code() === 200) {
    http_response_code(400);
}

// Add response metadata
$response['meta']['response_time_ms'] = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2);
$response['meta']['memory_usage_mb'] = round(memory_get_usage(true) / 1024 / 1024, 2);

// Output response
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);