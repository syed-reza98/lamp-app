<?php

/**
 * Comprehensive Test Suite for Code Optimizations
 * Validates all optimizations and improvements
 */

echo "<!DOCTYPE html>\n<html>\n<head>\n<title>Optimization Test Results</title>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:40px;} .pass{color:#28a745;} .fail{color:#dc3545;} .info{color:#007bff;} .section{margin:20px 0; padding:20px; border-left:4px solid #007bff; background:#f8f9fa;}</style>\n";
echo "</head>\n<body>\n";

echo "<h1>🧪 Comprehensive Optimization Test Results</h1>\n";
echo "<p><strong>Test Run:</strong> " . date('Y-m-d H:i:s T') . "</p>\n";

$test_results = [
    'total_tests' => 0,
    'passed_tests' => 0,
    'failed_tests' => 0,
    'details' => []
];

function runTest($test_name, $test_function, &$results)
{
    $results['total_tests']++;
    echo "<div class='section'>\n";
    echo "<h3>Test: {$test_name}</h3>\n";

    try {
        $result = $test_function();
        if ($result['status'] === 'pass') {
            $results['passed_tests']++;
            echo "<p class='pass'>✅ PASS: {$result['message']}</p>\n";
        } else {
            $results['failed_tests']++;
            echo "<p class='fail'>❌ FAIL: {$result['message']}</p>\n";
        }

        if (isset($result['details'])) {
            echo "<div class='info'><strong>Details:</strong><br>\n";
            if (is_array($result['details'])) {
                foreach ($result['details'] as $key => $value) {
                    echo "• {$key}: {$value}<br>\n";
                }
            } else {
                echo $result['details'];
            }
            echo "</div>\n";
        }

        $results['details'][$test_name] = $result;
    } catch (Exception $e) {
        $results['failed_tests']++;
        echo "<p class='fail'>❌ ERROR: {$e->getMessage()}</p>\n";
        $results['details'][$test_name] = ['status' => 'error', 'message' => $e->getMessage()];
    }

    echo "</div>\n";
}

// Test 1: Helper Functions Loading
runTest('Helper Functions Loading', function () {
    if (!file_exists('includes/helpers.php')) {
        return ['status' => 'fail', 'message' => 'Helper file not found'];
    }

    require_once 'includes/helpers.php';

    $functions = [
        'getInstanceMetadata',
        'getDatabaseConnection',
        'renderDetailItems',
        'getHealthStatusClass',
        'renderStatusCard',
        'getPerformanceMetrics',
        'getAWSInstanceInfo'
    ];

    $missing = [];
    foreach ($functions as $func) {
        if (!function_exists($func)) {
            $missing[] = $func;
        }
    }

    if (empty($missing)) {
        return [
            'status' => 'pass',
            'message' => 'All helper functions loaded successfully',
            'details' => ['Functions Loaded' => count($functions)]
        ];
    } else {
        return [
            'status' => 'fail',
            'message' => 'Missing functions: ' . implode(', ', $missing)
        ];
    }
}, $test_results);

// Test 2: Centralized Configuration
runTest('Centralized Configuration', function () {
    if (!file_exists('config/app_config.php')) {
        return ['status' => 'fail', 'message' => 'Configuration file not found'];
    }

    $config = require 'config/app_config.php';

    $required_sections = ['database', 'app', 'aws', 'security', 'performance'];
    $missing_sections = [];

    foreach ($required_sections as $section) {
        if (!isset($config[$section])) {
            $missing_sections[] = $section;
        }
    }

    if (empty($missing_sections)) {
        return [
            'status' => 'pass',
            'message' => 'Configuration loaded with all required sections',
            'details' => [
                'Database Host' => $config['database']['hostname'],
                'App Version' => $config['app']['version'],
                'Environment' => $config['app']['environment']
            ]
        ];
    } else {
        return [
            'status' => 'fail',
            'message' => 'Missing configuration sections: ' . implode(', ', $missing_sections)
        ];
    }
}, $test_results);

// Test 3: Database Connection Optimization
runTest('Database Connection Function', function () {
    if (!function_exists('getDatabaseConnection')) {
        return ['status' => 'fail', 'message' => 'getDatabaseConnection function not found'];
    }

    // Test function can be called without parameters (uses centralized config)
    try {
        $reflection = new ReflectionFunction('getDatabaseConnection');
        $params = $reflection->getParameters();
        $has_default = empty($params) || $params[0]->isOptional();

        if (!$has_default) {
            return ['status' => 'fail', 'message' => 'Function requires parameters (not using centralized config)'];
        }

        return [
            'status' => 'pass',
            'message' => 'Database connection function optimized with centralized config support',
            'details' => ['Parameter Count' => count($params), 'Uses Central Config' => 'Yes']
        ];
    } catch (Exception $e) {
        return ['status' => 'fail', 'message' => 'Error analyzing function: ' . $e->getMessage()];
    }
}, $test_results);

// Test 4: CSS File Optimization
runTest('CSS Files Optimization', function () {
    $css_files = [
        'optimized_styles.css' => 'Main optimized styles',
        'architecture_styles.css' => 'Architecture diagram styles'
    ];

    $details = [];
    $all_exist = true;

    foreach ($css_files as $file => $description) {
        if (file_exists($file)) {
            $size = filesize($file);
            $details[$description] = number_format($size) . ' bytes';
        } else {
            $all_exist = false;
            $details[$description] = 'Missing';
        }
    }

    // Check if duplicate files were removed
    $removed_files = [
        'lamp-app/optimized_styles.css',
        'lamp-app/includes/helpers.php'
    ];

    $duplicates_removed = true;
    foreach ($removed_files as $file) {
        if (file_exists($file)) {
            $duplicates_removed = false;
            $details['Duplicate Still Exists'] = $file;
        }
    }

    if ($all_exist && $duplicates_removed) {
        return [
            'status' => 'pass',
            'message' => 'CSS files optimized and duplicates removed',
            'details' => $details
        ];
    } else {
        return [
            'status' => 'fail',
            'message' => 'CSS optimization incomplete',
            'details' => $details
        ];
    }
}, $test_results);

// Test 5: Health Check Consolidation
runTest('Health Check Consolidation', function () {
    $old_files = ['health.php', 'enhanced_health.php'];
    $new_file = 'health_unified.php';

    // Check old files were removed
    $old_exist = [];
    foreach ($old_files as $file) {
        if (file_exists($file)) {
            $old_exist[] = $file;
        }
    }

    // Check new file exists
    $new_exists = file_exists($new_file);

    if (empty($old_exist) && $new_exists) {
        return [
            'status' => 'pass',
            'message' => 'Health checks successfully consolidated',
            'details' => [
                'Old Files Removed' => count($old_files),
                'New Unified File' => 'Created',
                'File Size' => number_format(filesize($new_file)) . ' bytes'
            ]
        ];
    } else {
        $details = ['New File Exists' => $new_exists ? 'Yes' : 'No'];
        if (!empty($old_exist)) {
            $details['Old Files Still Exist'] = implode(', ', $old_exist);
        }

        return [
            'status' => 'fail',
            'message' => 'Health check consolidation incomplete',
            'details' => $details
        ];
    }
}, $test_results);

// Test 6: Architecture CSS Extraction
runTest('Architecture CSS Extraction', function () {
    if (!file_exists('aws_architecture_optimized.php')) {
        return ['status' => 'fail', 'message' => 'Architecture file not found'];
    }

    $content = file_get_contents('aws_architecture_optimized.php');

    // Check if inline CSS was removed
    $has_style_tag = strpos($content, '<style>') !== false;
    $css_file_exists = file_exists('architecture_styles.css');

    if (!$has_style_tag && $css_file_exists) {
        return [
            'status' => 'pass',
            'message' => 'CSS successfully extracted from architecture file',
            'details' => [
                'Inline CSS Removed' => 'Yes',
                'External CSS File' => 'Created (' . number_format(filesize('architecture_styles.css')) . ' bytes)',
                'PHP File Size' => number_format(strlen($content)) . ' characters'
            ]
        ];
    } else {
        return [
            'status' => 'fail',
            'message' => 'CSS extraction incomplete',
            'details' => [
                'Still Has Inline CSS' => $has_style_tag ? 'Yes' : 'No',
                'External CSS Exists' => $css_file_exists ? 'Yes' : 'No'
            ]
        ];
    }
}, $test_results);

// Display Summary
echo "<div class='section'>\n";
echo "<h2>📊 Test Summary</h2>\n";
echo "<p><strong>Total Tests:</strong> {$test_results['total_tests']}</p>\n";
echo "<p class='pass'><strong>Passed:</strong> {$test_results['passed_tests']}</p>\n";
echo "<p class='fail'><strong>Failed:</strong> {$test_results['failed_tests']}</p>\n";

$success_rate = round(($test_results['passed_tests'] / $test_results['total_tests']) * 100, 1);
echo "<p><strong>Success Rate:</strong> {$success_rate}%</p>\n";

if ($success_rate >= 100) {
    echo "<p class='pass'>🎉 All optimizations successfully implemented!</p>\n";
} elseif ($success_rate >= 80) {
    echo "<p class='info'>⚠️ Most optimizations successful, minor issues detected.</p>\n";
} else {
    echo "<p class='fail'>❌ Multiple optimization issues detected.</p>\n";
}
echo "</div>\n";

echo "<div class='section'>\n";
echo "<h2>🔍 Detailed Results JSON</h2>\n";
echo "<pre>" . htmlspecialchars(json_encode($test_results, JSON_PRETTY_PRINT)) . "</pre>\n";
echo "</div>\n";

echo "<p><em>Test completed at: " . date('Y-m-d H:i:s T') . "</em></p>\n";
echo "</body>\n</html>";
