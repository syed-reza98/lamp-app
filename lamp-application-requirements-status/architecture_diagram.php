<?php

/**
 * Standalone Enhanced AWS Architecture Diagram
 * Assignment 3 - AWS LAMP Stack Architecture Visualization
 * 
 * Author: Anika Arman
 * Student ID: 14425754
 */

// Include the enhanced architecture functions
require_once 'aws_architecture_enhanced.php';

// Get environment data
$rds_hostname = $_SERVER['RDS_HOSTNAME'] ?? 'lamp-app-db.cijnrr1efnu0.us-east-1.rds.amazonaws.com';
$rds_port = $_SERVER['RDS_PORT'] ?? '3306';
$rds_db_name = $_SERVER['RDS_DB_NAME'] ?? 'lampapp';
$rds_username = $_SERVER['RDS_USERNAME'] ?? 'lampdbadmin';
$rds_password = $_SERVER['RDS_PASSWORD'] ?? 'lampdbpassword';

// AWS Instance Metadata Service (IMDS) function
function getInstanceMetadata($path)
{
    $token_url = 'http://169.254.169.254/latest/api/token';
    $metadata_url = "http://169.254.169.254/latest/meta-data/$path";

    $context = stream_context_create([
        'http' => [
            'timeout' => 2,
            'method' => 'PUT',
            'header' => "X-aws-ec2-metadata-token-ttl-seconds: 21600\r\n"
        ]
    ]);

    try {
        $token = @file_get_contents($token_url, false, $context);
        if ($token) {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 2,
                    'method' => 'GET',
                    'header' => "X-aws-ec2-metadata-token: $token\r\n"
                ]
            ]);
            return @file_get_contents($metadata_url, false, $context);
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

// Get live environment data
$instance_id = getInstanceMetadata('instance-id') ?: 'i-0fdc269d453d60316';
$instance_type = getInstanceMetadata('instance-type') ?: 't3.micro';
$availability_zone = getInstanceMetadata('placement/availability-zone') ?: 'us-east-1a';
$local_hostname = getInstanceMetadata('local-hostname') ?: 'ip-10-0-2-10.ec2.internal';
$local_ipv4 = getInstanceMetadata('local-ipv4') ?: '10.0.2.10';
$public_ipv4 = getInstanceMetadata('public-ipv4') ?: 'N/A';

// Test database connection
$db_connected = false;
$db_error = '';

try {
    $dsn = "mysql:host=$rds_hostname;port=$rds_port;dbname=$rds_db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $rds_username, $rds_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    $db_connected = true;
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS Architecture Diagram - Assignment 3</title>
    <?php echo getEnhancedArchitectureCSS(); ?>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .page-header p {
            color: #34495e;
            font-size: 18px;
            margin: 8px 0;
            line-height: 1.6;
        }

        .environment-info {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .info-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 14px;
        }

        .info-value {
            color: #5a6c7d;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin-top: 3px;
        }

        .navigation {
            text-align: center;
            margin-top: 30px;
        }

        .nav-link {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            margin: 0 10px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .nav-link:hover {
            background: #2980b9;
        }
    </style>
</head>

<body>
    <div class="page-header">
        <h1>🏗️ AWS Architecture Diagram</h1>
        <p><strong>Assignment 3 - Deliverable 1:</strong> LAMP Stack Scalable Architecture on AWS</p>
        <p><strong>Student:</strong> Anika Arman (14425754) | <strong>Subject:</strong> 32555 Cloud Computing</p>
    </div>

    <div class="environment-info">
        <h3 style="text-align: center; margin-bottom: 15px; color: #2c3e50;">🔍 Live AWS Environment Status</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Instance ID</div>
                <div class="info-value"><?php echo htmlspecialchars($instance_id); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Instance Type</div>
                <div class="info-value"><?php echo htmlspecialchars($instance_type); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Availability Zone</div>
                <div class="info-value"><?php echo htmlspecialchars($availability_zone); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Private IP</div>
                <div class="info-value"><?php echo htmlspecialchars($local_ipv4); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Database Status</div>
                <div class="info-value">
                    <?php if ($db_connected): ?>
                        <span style="color: #27ae60;">✅ Connected</span>
                    <?php else: ?>
                        <span style="color: #e74c3c;">❌ Error</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Last Updated</div>
                <div class="info-value"><?php echo date('Y-m-d H:i:s T'); ?></div>
            </div>
        </div>
    </div>

    <?php echo getEnhancedArchitectureDiagram(); ?>

    <div class="navigation">
        <a href="index.php" class="nav-link">📊 Main Dashboard</a>
        <a href="assign_3_report.php" class="nav-link">📄 Full Architecture Report</a>
        <a href="health.php" class="nav-link">❤️ Health Check</a>
        <a href="http://lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com" class="nav-link">🌐 Live Environment</a>
    </div>
</body>

</html>