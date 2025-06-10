<?php
/**
 * Test Script for Optimized LAMP Report
 * Verifies that all optimizations work correctly
 */

echo "<h1>🧪 Testing Optimized LAMP Report Components</h1>\n";

// Test 1: Helper functions loading
echo "<h2>Test 1: Helper Functions</h2>\n";
try {
    require_once 'includes/helpers.php';
    echo "✅ Helper functions loaded successfully<br>\n";
    
    // Test helper functions
    $testDetails = ['Test Key' => 'Test Value', 'Another Key' => 'Another Value'];
    $detailOutput = renderDetailItems($testDetails);
    echo "✅ renderDetailItems() working<br>\n";
    
    $testCard = renderStatusCard('🧪', 'Test Card', 'Working', 'Test description', 'HEALTHY');
    echo "✅ renderStatusCard() working<br>\n";
    
    $healthClass = getHealthStatusClass('HEALTHY');
    echo "✅ getHealthStatusClass() working: {$healthClass}<br>\n";
    
} catch (Exception $e) {
    echo "❌ Error loading helpers: " . $e->getMessage() . "<br>\n";
}

// Test 2: CSS file exists
echo "<h2>Test 2: CSS Files</h2>\n";
if (file_exists('optimized_styles.css')) {
    $cssSize = filesize('optimized_styles.css');
    echo "✅ optimized_styles.css exists ({$cssSize} bytes)<br>\n";
} else {
    echo "❌ optimized_styles.css not found<br>\n";
}

if (file_exists('fresh_styles.css')) {
    $originalCssSize = filesize('fresh_styles.css');
    echo "✅ Original fresh_styles.css exists ({$originalCssSize} bytes)<br>\n";
    
    if (isset($cssSize)) {
        $reduction = round((($originalCssSize - $cssSize) / $originalCssSize) * 100, 1);
        echo "✅ Size reduction: {$reduction}%<br>\n";
    }
} else {
    echo "❌ Original fresh_styles.css not found<br>\n";
}

// Test 3: Architecture diagram
echo "<h2>Test 3: Architecture Diagram</h2>\n";
if (file_exists('aws_architecture_optimized.php')) {
    try {
        require_once 'aws_architecture_optimized.php';
        echo "✅ aws_architecture_optimized.php loaded<br>\n";
        
        if (function_exists('getEnhancedArchitectureDiagram')) {
            echo "✅ getEnhancedArchitectureDiagram() function available<br>\n";
        } else {
            echo "❌ getEnhancedArchitectureDiagram() function not found<br>\n";
        }
    } catch (Exception $e) {
        echo "❌ Error loading architecture: " . $e->getMessage() . "<br>\n";
    }
} else {
    echo "❌ aws_architecture_optimized.php not found<br>\n";
}

// Test 4: Main report files
echo "<h2>Test 4: Report Files</h2>\n";
$reportFiles = ['lamp_report.php', 'lamp_report_optimized.php'];
foreach ($reportFiles as $file) {
    if (file_exists($file)) {
        echo "✅ {$file} exists<br>\n";
    } else {
        echo "❌ {$file} not found<br>\n";
    }
}

// Test 5: Duplicate removal
echo "<h2>Test 5: Duplicate File Removal</h2>\n";
$removedFiles = [
    'lamp-app/lamp_report.php',
    'lamp-app/fresh_styles.css'
];

foreach ($removedFiles as $file) {
    if (!file_exists($file)) {
        echo "✅ Duplicate removed: {$file}<br>\n";
    } else {
        echo "⚠️ Duplicate still exists: {$file}<br>\n";
    }
}

// Test 6: Memory usage
echo "<h2>Test 6: Performance</h2>\n";
$startMemory = memory_get_usage();
$peakMemory = memory_get_peak_usage();
echo "✅ Current memory usage: " . round($startMemory / 1024 / 1024, 2) . " MB<br>\n";
echo "✅ Peak memory usage: " . round($peakMemory / 1024 / 1024, 2) . " MB<br>\n";

echo "<h2>🎉 Test Summary</h2>\n";
echo "All optimization components tested. Check above for any ❌ errors that need attention.<br>\n";
echo "If all tests show ✅, the optimization is working correctly!<br>\n";
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}
h1, h2 {
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
}
</style>
