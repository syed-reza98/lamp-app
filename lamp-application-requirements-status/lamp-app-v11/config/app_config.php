<?php

/**
 * Centralized Configuration for AWS LAMP Stack
 * Single source of truth for environment variables and settings
 * 
 * Student: Anika Arman
 * Student ID: 14425754
 */

// Database Configuration
$config = [
    'database' => [
        'hostname' => $_SERVER['RDS_HOSTNAME'] ?? 'localhost',
        'port' => $_SERVER['RDS_PORT'] ?? '3306',
        'database' => $_SERVER['RDS_DB_NAME'] ?? 'lampapp',
        'username' => $_SERVER['RDS_USERNAME'] ?? 'root',
        'password' => $_SERVER['RDS_PASSWORD'] ?? '',
        'charset' => 'utf8mb4',
        'sql_mode' => 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION',
        'timeout' => 10
    ],

    // Application Configuration
    'app' => [
        'name' => 'LAMP Application',
        'version' => $_SERVER['VERSION_LABEL'] ?? 'v1.0.0-assignment3',
        'environment' => $_SERVER['ENVIRONMENT'] ?? 'production',
        'debug' => filter_var($_SERVER['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'timezone' => 'UTC'
    ],

    // AWS Configuration
    'aws' => [
        'region' => 'us-east-1',
        'instance_metadata_timeout' => 3,
        'metadata_token_ttl' => 21600
    ],

    // Security Configuration
    'security' => [
        'session_lifetime' => 7200,
        'csrf_protection' => true,
        'xss_protection' => true
    ],

    // Performance Configuration
    'performance' => [
        'cache_enabled' => true,
        'cache_ttl' => 300,
        'compression_enabled' => true
    ]
];

/**
 * Validate required configuration
 */
function validateConfig($config)
{
    $required_keys = [
        'database.hostname',
        'database.username',
        'database.password',
        'database.database'
    ];

    foreach ($required_keys as $key) {
        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                throw new InvalidArgumentException("Missing required configuration: {$key}");
            }
            $value = $value[$k];
        }

        if (empty($value)) {
            throw new InvalidArgumentException("Configuration {$key} cannot be empty");
        }
    }
}

// Validate configuration
try {
    validateConfig($config);
} catch (InvalidArgumentException $e) {
    error_log("Configuration Error: " . $e->getMessage());
    if ($config['app']['debug']) {
        throw $e;
    }
}

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Return configuration
return $config;
