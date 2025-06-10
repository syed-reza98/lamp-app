<?php

/**
 * Helper Functions for LAMP Stack Report
 * Consolidated functions to reduce code duplication
 */

/**
 * AWS Instance Metadata Service (IMDS) v2 function
 * Retrieves instance metadata using session token authentication
 */
function getInstanceMetadata($path)
{
    // Get session token first
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

    // Use token to get metadata
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

/**
 * Helper function to render requirement details
 */
function renderDetailItems($details)
{
    $output = '';
    foreach ($details as $key => $value) {
        $output .= '<div class="detail-item">';
        $output .= '<strong>' . htmlspecialchars($key) . ':</strong>';
        $output .= '<span>' . htmlspecialchars($value) . '</span>';
        $output .= '</div>';
    }
    return $output;
}

/**
 * Helper function to get health status class
 */
function getHealthStatusClass($status)
{
    switch (strtoupper($status)) {
        case 'HEALTHY':
        case 'OK':
        case 'GREEN':
        case 'IMPLEMENTED':
            return 'status-healthy';
        case 'WARNING':
        case 'YELLOW':
            return 'status-warning';
        case 'ERROR':
        case 'UNHEALTHY':
        case 'RED':
            return 'status-error';
        default:
            return 'status-info';
    }
}

/**
 * Helper function to render metric cards
 */
function renderMetricCard($icon, $title, $value, $description)
{
    return "
    <div class=\"card metric-card\">
        <div class=\"card-icon\">{$icon}</div>
        <div class=\"card-content\">
            <h3>{$title}</h3>
            <p class=\"metric-value\">{$value}</p>
            <small>{$description}</small>
        </div>
    </div>";
}

/**
 * Helper function to render status cards
 */
function renderStatusCard($icon, $title, $value, $description, $status = 'info')
{
    $statusClass = getHealthStatusClass($status);
    return "
    <div class=\"card status-card {$statusClass}\">
        <div class=\"card-icon\">{$icon}</div>
        <div class=\"card-content\">
            <h3>{$title}</h3>
            <p class=\"status-value\">{$value}</p>
            <small>{$description}</small>
        </div>
    </div>";
}

/**
 * Enhanced database connection with consistent configuration
 * Uses centralized configuration and improved error handling
 */
function getDatabaseConnection($environment = null)
{
    // Load centralized config if no environment provided
    if ($environment === null) {
        $config = require __DIR__ . '/../config/app_config.php';
        $environment = [
            'rds_hostname' => $config['database']['hostname'],
            'rds_port' => $config['database']['port'],
            'rds_db_name' => $config['database']['database'],
            'rds_username' => $config['database']['username'],
            'rds_password' => $config['database']['password']
        ];
    }

    // Validate required environment variables
    $required_keys = ['rds_hostname', 'rds_port', 'rds_db_name', 'rds_username', 'rds_password'];
    foreach ($required_keys as $key) {
        if (empty($environment[$key])) {
            throw new InvalidArgumentException("Missing required environment variable: {$key}");
        }
    }

    $dsn = "mysql:host={$environment['rds_hostname']};port={$environment['rds_port']};dbname={$environment['rds_db_name']};charset=utf8mb4";
    return new PDO($dsn, $environment['rds_username'], $environment['rds_password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4, sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'",
        PDO::ATTR_TIMEOUT => 10,
        PDO::ATTR_PERSISTENT => false
    ]);
}

/**
 * Calculate performance metrics
 */
function getPerformanceMetrics()
{
    return [
        'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        'php_version' => PHP_VERSION,
        'server_load' => sys_getloadavg(),
        'disk_free_gb' => round(disk_free_space('/') / 1024 / 1024 / 1024, 2)
    ];
}

/**
 * Get AWS instance information
 */
function getAWSInstanceInfo()
{
    return [
        'instance_id' => getInstanceMetadata('instance-id') ?: 'i-0fdc269d453d60316',
        'instance_type' => getInstanceMetadata('instance-type') ?: 't3.micro',
        'availability_zone' => getInstanceMetadata('placement/availability-zone') ?: 'us-east-1a',
        'local_hostname' => getInstanceMetadata('local-hostname') ?: 'ip-10-0-2-10.ec2.internal',
        'local_ipv4' => getInstanceMetadata('local-ipv4') ?: '10.0.2.10',
        'public_ipv4' => getInstanceMetadata('public-ipv4') ?: 'N/A',
        'security_groups' => getInstanceMetadata('security-groups') ?: 'lamp-security-group',
        'vpc_id' => getInstanceMetadata('network/interfaces/macs/') ?
            getInstanceMetadata('network/interfaces/macs/' . trim(getInstanceMetadata('network/interfaces/macs/')) . 'vpc-id') : 'vpc-custom'
    ];
}