<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
include 'lamp_report.php';
$output = ob_get_clean();

echo "File executed successfully!\n";
echo "Output length: " . strlen($output) . " bytes\n";

// Check for specific sections
if (strpos($output, 'Architecture Benefits') !== false) {
    echo "✅ Architecture Benefits section found\n";
}
if (strpos($output, 'N/A (Windows)') !== false) {
    echo "✅ Windows-compatible server load found\n";
}
if (strpos($output, 'High Availability') !== false) {
    echo "✅ Architecture benefits content found\n";
}
