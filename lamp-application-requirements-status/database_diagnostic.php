<?php

/**
 * Database Diagnostic Script
 * Helps identify SQL syntax issues and MySQL compatibility problems
 */

// Get RDS connection info from environment
$environment = [
    'rds_hostname' => $_SERVER['RDS_HOSTNAME'] ?? 'lamp-app-db.cijnrr1efnu0.us-east-1.rds.amazonaws.com',
    'rds_port' => $_SERVER['RDS_PORT'] ?? '3306',
    'rds_db_name' => $_SERVER['RDS_DB_NAME'] ?? 'lampapp',
    'rds_username' => $_SERVER['RDS_USERNAME'] ?? 'lampdbadmin',
    'rds_password' => $_SERVER['RDS_PASSWORD'] ?? 'SecurePass123!'
];

$diagnostics = [
    'timestamp' => date('c'),
    'tests' => [],
    'summary' => []
];

echo "<html><head><title>Database Diagnostic</title></head><body>";
echo "<h1>Database Diagnostic Tool</h1>";
echo "<p>Testing database connectivity and SQL syntax compatibility</p>";

// Test 1: Basic Connection
echo "<h2>Test 1: Basic Database Connection</h2>";
$test_name = 'basic_connection';
try {
    $dsn = "mysql:host={$environment['rds_hostname']};port={$environment['rds_port']};dbname={$environment['rds_db_name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $environment['rds_username'], $environment['rds_password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10
    ]);

    $diagnostics['tests'][$test_name] = [
        'status' => 'PASS',
        'message' => 'Database connection successful',
        'details' => [
            'hostname' => $environment['rds_hostname'],
            'database' => $environment['rds_db_name']
        ]
    ];
    echo "<p style='color: green;'>✅ PASS: Database connection successful</p>";
} catch (PDOException $e) {
    $diagnostics['tests'][$test_name] = [
        'status' => 'FAIL',
        'message' => 'Database connection failed',
        'error' => $e->getMessage()
    ];
    echo "<p style='color: red;'>❌ FAIL: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</body></html>";
    header('Content-Type: application/json');
    echo json_encode($diagnostics, JSON_PRETTY_PRINT);
    exit;
}

// Test 2: MySQL Version and Functions
echo "<h2>Test 2: MySQL Version and Function Tests</h2>";
$sql_tests = [
    'version' => "SELECT VERSION() as mysql_version",
    'current_timestamp_func' => "SELECT CURRENT_TIMESTAMP() as test_time",
    'now_func' => "SELECT NOW() as test_time",
    'current_time_func' => "SELECT CURRENT_TIME() as test_time",
    'database_func' => "SELECT DATABASE() as db_name",
    'user_func' => "SELECT USER() as current_user",
    'combined_query' => "SELECT VERSION() as version, DATABASE() as db_name, USER() as user, CURRENT_TIMESTAMP() as current_time"
];

foreach ($sql_tests as $test_key => $sql) {
    echo "<h3>Testing: {$test_key}</h3>";
    echo "<code>" . htmlspecialchars($sql) . "</code><br>";

    try {
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch();

        $diagnostics['tests'][$test_key] = [
            'status' => 'PASS',
            'sql' => $sql,
            'result' => $result
        ];

        echo "<p style='color: green;'>✅ PASS: Query executed successfully</p>";
        echo "<pre>" . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</pre>";
    } catch (PDOException $e) {
        $diagnostics['tests'][$test_key] = [
            'status' => 'FAIL',
            'sql' => $sql,
            'error' => $e->getMessage(),
            'error_code' => $e->getCode()
        ];

        echo "<p style='color: red;'>❌ FAIL: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Error Code: " . htmlspecialchars($e->getCode()) . "</p>";
    }
}

// Test 3: SQL Mode and Settings
echo "<h2>Test 3: MySQL Configuration</h2>";
try {
    $stmt = $pdo->query("SELECT @@sql_mode as sql_mode, @@version as version");
    $config = $stmt->fetch();

    echo "<h3>Current SQL Mode:</h3>";
    echo "<pre>" . htmlspecialchars($config['sql_mode']) . "</pre>";

    echo "<h3>MySQL Version:</h3>";
    echo "<pre>" . htmlspecialchars($config['version']) . "</pre>";

    $diagnostics['tests']['mysql_config'] = [
        'status' => 'PASS',
        'config' => $config
    ];
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ FAIL: Could not retrieve MySQL configuration</p>";
    $diagnostics['tests']['mysql_config'] = [
        'status' => 'FAIL',
        'error' => $e->getMessage()
    ];
}

// Test 4: Check for problematic queries
echo "<h2>Test 4: Problematic Query Patterns</h2>";
$problematic_queries = [
    'bare_current_time' => "SELECT current_time",
    'bare_current_timestamp' => "SELECT current_timestamp",
    'show_current_time' => "SHOW current_time"
];

foreach ($problematic_queries as $test_key => $sql) {
    echo "<h3>Testing Problematic Query: {$test_key}</h3>";
    echo "<code>" . htmlspecialchars($sql) . "</code><br>";

    try {
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch();

        echo "<p style='color: orange;'>⚠️ UNEXPECTED: This query should have failed but didn't</p>";
        $diagnostics['tests'][$test_key] = [
            'status' => 'UNEXPECTED_PASS',
            'sql' => $sql,
            'result' => $result
        ];
    } catch (PDOException $e) {
        echo "<p style='color: green;'>✅ EXPECTED FAIL: " . htmlspecialchars($e->getMessage()) . "</p>";
        $diagnostics['tests'][$test_key] = [
            'status' => 'EXPECTED_FAIL',
            'sql' => $sql,
            'error' => $e->getMessage(),
            'error_code' => $e->getCode()
        ];
    }
}

// Summary
$total_tests = count($diagnostics['tests']);
$passed_tests = count(array_filter($diagnostics['tests'], function ($test) {
    return $test['status'] === 'PASS';
}));
$failed_tests = count(array_filter($diagnostics['tests'], function ($test) {
    return $test['status'] === 'FAIL';
}));

$diagnostics['summary'] = [
    'total_tests' => $total_tests,
    'passed_tests' => $passed_tests,
    'failed_tests' => $failed_tests,
    'success_rate' => round(($passed_tests / $total_tests) * 100, 2) . '%'
];

echo "<h2>Summary</h2>";
echo "<p>Total Tests: {$total_tests}</p>";
echo "<p>Passed: {$passed_tests}</p>";
echo "<p>Failed: {$failed_tests}</p>";
echo "<p>Success Rate: " . $diagnostics['summary']['success_rate'] . "</p>";

echo "<h2>JSON Output</h2>";
echo "<pre>" . htmlspecialchars(json_encode($diagnostics, JSON_PRETTY_PRINT)) . "</pre>";

echo "</body></html>";
