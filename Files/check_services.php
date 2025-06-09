<?php
// Simple service status check
header('Content-Type: text/plain');

echo "=== Service Status Check ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// Check Apache status
$apache_status = shell_exec('systemctl is-active httpd 2>/dev/null') ?: shell_exec('systemctl is-active apache2 2>/dev/null');
echo "Apache Status: " . trim($apache_status) . "\n";

// Check PHP
$php_version = phpversion();
echo "PHP Version: " . $php_version . "\n";

// Check if we can connect to MySQL
try {
    $pdo = new PDO(
        "mysql:host=" . $_SERVER['RDS_HOSTNAME'] . ";port=" . $_SERVER['RDS_PORT'] . ";dbname=" . $_SERVER['RDS_DB_NAME'],
        $_SERVER['RDS_USERNAME'],
        $_SERVER['RDS_PASSWORD']
    );
    echo "MySQL Connection: OK\n";
    $pdo = null;
} catch (Exception $e) {
    echo "MySQL Connection: FAILED - " . $e->getMessage() . "\n";
}

// Check environment variables
echo "\nEnvironment Variables:\n";
echo "RDS_HOSTNAME: " . (isset($_SERVER['RDS_HOSTNAME']) ? $_SERVER['RDS_HOSTNAME'] : 'NOT SET') . "\n";
echo "RDS_PORT: " . (isset($_SERVER['RDS_PORT']) ? $_SERVER['RDS_PORT'] : 'NOT SET') . "\n";
echo "RDS_DB_NAME: " . (isset($_SERVER['RDS_DB_NAME']) ? $_SERVER['RDS_DB_NAME'] : 'NOT SET') . "\n";
echo "RDS_USERNAME: " . (isset($_SERVER['RDS_USERNAME']) ? $_SERVER['RDS_USERNAME'] : 'NOT SET') . "\n";

// Check disk space
$disk_free = disk_free_space('/');
$disk_total = disk_total_space('/');
$disk_used_percent = (($disk_total - $disk_free) / $disk_total) * 100;
echo "\nDisk Usage: " . round($disk_used_percent, 2) . "%\n";

// Check load
$load = sys_getloadavg();
echo "Load Average: " . implode(', ', $load) . "\n";

echo "\n=== End Status Check ===\n";
?>
