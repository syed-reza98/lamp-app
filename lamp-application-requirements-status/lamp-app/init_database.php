<?php

/**
 * Database Initialization Script for AWS LAMP Stack
 * Assignment 3 - Database Setup and Sample Data
 * 
 * Student: Anika Arman
 * Student ID: 14425754
 * 
 * This script initializes the database with required tables and sample data
 * to demonstrate the LAMP stack functionality.
 */

// Environment Configuration
$environment = [
    'rds_hostname' => $_SERVER['RDS_HOSTNAME'] ?? 'localhost',
    'rds_port' => $_SERVER['RDS_PORT'] ?? '3306',
    'rds_db_name' => $_SERVER['RDS_DB_NAME'] ?? 'lampdb',
    'rds_username' => $_SERVER['RDS_USERNAME'] ?? 'admin',
    'rds_password' => $_SERVER['RDS_PASSWORD'] ?? ''
];

$init_results = [
    'success' => false,
    'messages' => [],
    'errors' => [],
    'tables_created' => [],
    'sample_data_inserted' => false
];

try {    // Connect to database with enhanced configuration
    $dsn = "mysql:host={$environment['rds_hostname']};port={$environment['rds_port']};dbname={$environment['rds_db_name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $environment['rds_username'], $environment['rds_password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4, sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'"
    ]);

    $init_results['messages'][] = "Successfully connected to database: {$environment['rds_hostname']}";

    // Create application logs table
    $pdo->exec("CREATE TABLE IF NOT EXISTS lamp_application_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        instance_id VARCHAR(50) NOT NULL,
        availability_zone VARCHAR(50),
        message TEXT NOT NULL,
        severity ENUM('INFO', 'WARNING', 'ERROR', 'DEBUG') DEFAULT 'INFO',
        category VARCHAR(50) DEFAULT 'GENERAL',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at),
        INDEX idx_instance (instance_id),
        INDEX idx_severity (severity),
        INDEX idx_category (category)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $init_results['tables_created'][] = 'lamp_application_logs';
    $init_results['messages'][] = "Created table: lamp_application_logs";

    // Create health checks table
    $pdo->exec("CREATE TABLE IF NOT EXISTS lamp_health_checks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        instance_id VARCHAR(50) NOT NULL,
        check_type VARCHAR(50) NOT NULL,
        status ENUM('HEALTHY', 'UNHEALTHY', 'WARNING', 'ERROR') DEFAULT 'HEALTHY',
        response_time_ms INT DEFAULT 0,
        details JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at),
        INDEX idx_instance_type (instance_id, check_type),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $init_results['tables_created'][] = 'lamp_health_checks';
    $init_results['messages'][] = "Created table: lamp_health_checks";

    // Create application metrics table
    $pdo->exec("CREATE TABLE IF NOT EXISTS lamp_application_metrics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        instance_id VARCHAR(50) NOT NULL,
        metric_name VARCHAR(100) NOT NULL,
        metric_value DECIMAL(15,4) NOT NULL,
        metric_unit VARCHAR(20) DEFAULT 'count',
        tags JSON,
        recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_recorded_at (recorded_at),
        INDEX idx_instance_metric (instance_id, metric_name),
        INDEX idx_metric_name (metric_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $init_results['tables_created'][] = 'lamp_application_metrics';
    $init_results['messages'][] = "Created table: lamp_application_metrics";

    // Create users table for sample application
    $pdo->exec("CREATE TABLE IF NOT EXISTS lamp_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        full_name VARCHAR(100) NOT NULL,
        status ENUM('ACTIVE', 'INACTIVE', 'SUSPENDED') DEFAULT 'ACTIVE',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        INDEX idx_username (username),
        INDEX idx_email (email),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $init_results['tables_created'][] = 'lamp_users';
    $init_results['messages'][] = "Created table: lamp_users";

    // Create deployment info table
    $pdo->exec("CREATE TABLE IF NOT EXISTS lamp_deployment_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        deployment_id VARCHAR(100) NOT NULL,
        version VARCHAR(50) NOT NULL,
        environment VARCHAR(50) NOT NULL,
        deployment_status ENUM('PENDING', 'SUCCESS', 'FAILED', 'ROLLBACK') DEFAULT 'PENDING',
        instance_id VARCHAR(50) NOT NULL,
        deployment_notes TEXT,
        deployed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completed_at TIMESTAMP NULL,
        INDEX idx_deployment_id (deployment_id),
        INDEX idx_instance (instance_id),
        INDEX idx_deployed_at (deployed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $init_results['tables_created'][] = 'lamp_deployment_info';
    $init_results['messages'][] = "Created table: lamp_deployment_info";

    // Insert sample data
    $instance_id = 'i-0fdc269d453d60316'; // Replace with actual instance ID if available

    // Sample application logs
    $sample_logs = [
        ['INFO', 'APPLICATION', 'Application started successfully'],
        ['INFO', 'DATABASE', 'Database connection established'],
        ['INFO', 'DEPLOYMENT', 'Assignment 3 LAMP stack deployed'],
        ['INFO', 'MONITORING', 'Health checks initialized'],
        ['WARNING', 'PERFORMANCE', 'Memory usage above 70%'],
        ['INFO', 'SECURITY', 'SSL certificate validated'],
        ['INFO', 'SCALING', 'Auto scaling group configured'],
        ['INFO', 'BACKUP', 'Database backup completed']
    ];

    $log_stmt = $pdo->prepare("INSERT INTO lamp_application_logs (instance_id, availability_zone, message, severity, category) VALUES (?, ?, ?, ?, ?)");
    foreach ($sample_logs as $log) {
        $log_stmt->execute([$instance_id, 'us-east-1a', $log[2], $log[0], $log[1]]);
    }

    // Sample users
    $sample_users = [
        ['anika.arman', 'anika.arman@student.uts.edu.au', 'Anika Arman', 'ACTIVE'],
        ['admin', 'admin@lamp-app.com', 'System Administrator', 'ACTIVE'],
        ['testuser1', 'test1@example.com', 'Test User One', 'ACTIVE'],
        ['testuser2', 'test2@example.com', 'Test User Two', 'INACTIVE']
    ];

    $user_stmt = $pdo->prepare("INSERT INTO lamp_users (username, email, full_name, status) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP");
    foreach ($sample_users as $user) {
        $user_stmt->execute($user);
    }

    // Sample metrics
    $sample_metrics = [
        ['cpu_usage_percent', 25.5, 'percent'],
        ['memory_usage_mb', 128.7, 'megabytes'],
        ['disk_usage_percent', 45.2, 'percent'],
        ['active_connections', 15, 'count'],
        ['response_time_ms', 250.5, 'milliseconds'],
        ['requests_per_minute', 120, 'count']
    ];

    $metric_stmt = $pdo->prepare("INSERT INTO lamp_application_metrics (instance_id, metric_name, metric_value, metric_unit, tags) VALUES (?, ?, ?, ?, ?)");
    foreach ($sample_metrics as $metric) {
        $tags = json_encode(['source' => 'initialization', 'environment' => 'production']);
        $metric_stmt->execute([$instance_id, $metric[0], $metric[1], $metric[2], $tags]);
    }

    // Sample deployment info
    $deployment_stmt = $pdo->prepare("INSERT INTO lamp_deployment_info (deployment_id, version, environment, deployment_status, instance_id, deployment_notes, completed_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $deployment_stmt->execute([
        'deploy-' . date('Ymd-His'),
        'v1.0.0-assignment3',
        'production',
        'SUCCESS',
        $instance_id,
        'Assignment 3 LAMP stack deployment with all 10 AWS requirements implemented',
        date('Y-m-d H:i:s')
    ]);

    $init_results['sample_data_inserted'] = true;
    $init_results['messages'][] = "Sample data inserted successfully";

    // Verify data insertion
    $count_stmt = $pdo->query("SELECT 
        (SELECT COUNT(*) FROM lamp_application_logs) as log_count,
        (SELECT COUNT(*) FROM lamp_health_checks) as health_count,
        (SELECT COUNT(*) FROM lamp_application_metrics) as metric_count,
        (SELECT COUNT(*) FROM lamp_users) as user_count,
        (SELECT COUNT(*) FROM lamp_deployment_info) as deployment_count");

    $counts = $count_stmt->fetch();
    $init_results['messages'][] = "Data verification: " . json_encode($counts);

    $init_results['success'] = true;
    $init_results['messages'][] = "Database initialization completed successfully";
} catch (PDOException $e) {
    $init_results['errors'][] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $init_results['errors'][] = "General error: " . $e->getMessage();
}

// Return results
header('Content-Type: application/json');
echo json_encode($init_results, JSON_PRETTY_PRINT);