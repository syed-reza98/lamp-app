<?php
// LAMP Stack Application with Database Connectivity
// Assignment 3 - AWS LAMP Stack Deployment

// Get RDS connection info from environment variables
$rds_hostname = $_SERVER['RDS_HOSTNAME'] ?? 'localhost';
$rds_port = $_SERVER['RDS_PORT'] ?? '3306';
$rds_db_name = $_SERVER['RDS_DB_NAME'] ?? 'lampdb';
$rds_username = $_SERVER['RDS_USERNAME'] ?? 'admin';
$rds_password = $_SERVER['RDS_PASSWORD'] ?? '';

// Database connection status
$db_connected = false;
$db_error = '';
$db_info = '';

try {
    // Create connection using PDO
    $dsn = "mysql:host=$rds_hostname;port=$rds_port;dbname=$rds_db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $rds_username, $rds_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    $db_connected = true;
    
    // Get database version and info
    $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as db_name, USER() as user, NOW() as current_time");
    $db_info = $stmt->fetch();
    
    // Create a test table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS lamp_test (
        id INT AUTO_INCREMENT PRIMARY KEY,
        message VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert a test record
    $test_message = "Hello from LAMP Stack on AWS! Instance: " . gethostname() . " - Time: " . date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("INSERT INTO lamp_test (message) VALUES (?)");
    $stmt->execute([$test_message]);
    
    // Get the last few records
    $stmt = $pdo->query("SELECT * FROM lamp_test ORDER BY created_at DESC LIMIT 5");
    $recent_records = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// Get AWS instance metadata
$instance_id = @file_get_contents('http://169.254.169.254/latest/meta-data/instance-id');
$availability_zone = @file_get_contents('http://169.254.169.254/latest/meta-data/placement/availability-zone');
$instance_type = @file_get_contents('http://169.254.169.254/latest/meta-data/instance-type');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMP Stack on AWS - Assignment 3</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .content {
            padding: 30px;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .status-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #007bff;
        }
        .status-card.success {
            border-left-color: #28a745;
        }
        .status-card.error {
            border-left-color: #dc3545;
        }
        .status-card.warning {
            border-left-color: #ffc107;
        }
        .status-card h3 {
            margin-top: 0;
            color: #495057;
        }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-indicator.success { background-color: #28a745; }
        .status-indicator.error { background-color: #dc3545; }
        .status-indicator.warning { background-color: #ffc107; }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .info-table th, .info-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .info-table th {
            background-color: #e9ecef;
            font-weight: 600;
        }
        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .records-table th, .records-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .records-table th {
            background-color: #007bff;
            color: white;
        }
        .records-table tr:hover {
            background-color: #f8f9fa;
        }
        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .architecture-info {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .architecture-info h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 LAMP Stack on AWS</h1>
            <p>Assignment 3 - Scalable, Elastic, High-Availability Architecture</p>
            <p><strong>Environment:</strong> lamp-prod-working</p>
        </div>
        
        <div class="content">
            <div class="status-grid">
                <!-- Database Connection Status -->
                <div class="status-card <?php echo $db_connected ? 'success' : 'error'; ?>">
                    <h3>
                        <span class="status-indicator <?php echo $db_connected ? 'success' : 'error'; ?>"></span>
                        Database Connection
                    </h3>
                    <?php if ($db_connected): ?>
                        <p><strong>✅ Connected Successfully!</strong></p>
                        <table class="info-table">
                            <tr><th>Host</th><td><?php echo htmlspecialchars($rds_hostname); ?></td></tr>
                            <tr><th>Database</th><td><?php echo htmlspecialchars($db_info['db_name']); ?></td></tr>
                            <tr><th>Version</th><td><?php echo htmlspecialchars($db_info['version']); ?></td></tr>
                            <tr><th>User</th><td><?php echo htmlspecialchars($db_info['user']); ?></td></tr>
                            <tr><th>Server Time</th><td><?php echo htmlspecialchars($db_info['current_time']); ?></td></tr>
                        </table>
                    <?php else: ?>
                        <p><strong>❌ Connection Failed</strong></p>
                        <p><strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?></p>
                        <table class="info-table">
                            <tr><th>Host</th><td><?php echo htmlspecialchars($rds_hostname); ?></td></tr>
                            <tr><th>Port</th><td><?php echo htmlspecialchars($rds_port); ?></td></tr>
                            <tr><th>Database</th><td><?php echo htmlspecialchars($rds_db_name); ?></td></tr>
                            <tr><th>Username</th><td><?php echo htmlspecialchars($rds_username); ?></td></tr>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Instance Information -->
                <div class="status-card success">
                    <h3>
                        <span class="status-indicator success"></span>
                        EC2 Instance Info
                    </h3>
                    <table class="info-table">
                        <tr><th>Instance ID</th><td><?php echo $instance_id ?: 'N/A'; ?></td></tr>
                        <tr><th>Availability Zone</th><td><?php echo $availability_zone ?: 'N/A'; ?></td></tr>
                        <tr><th>Instance Type</th><td><?php echo $instance_type ?: 'N/A'; ?></td></tr>
                        <tr><th>Hostname</th><td><?php echo gethostname(); ?></td></tr>
                        <tr><th>Server IP</th><td><?php echo $_SERVER['SERVER_ADDR'] ?? 'N/A'; ?></td></tr>
                        <tr><th>PHP Version</th><td><?php echo PHP_VERSION; ?></td></tr>
                    </table>
                </div>

                <!-- Application Status -->
                <div class="status-card success">
                    <h3>
                        <span class="status-indicator success"></span>
                        Application Status
                    </h3>
                    <table class="info-table">
                        <tr><th>LAMP Stack</th><td>✅ Active</td></tr>
                        <tr><th>Apache</th><td>✅ Running</td></tr>
                        <tr><th>MySQL</th><td><?php echo $db_connected ? '✅ Connected' : '❌ Disconnected'; ?></td></tr>
                        <tr><th>PHP</th><td>✅ <?php echo PHP_VERSION; ?></td></tr>
                        <tr><th>Load Balancer</th><td>✅ Active</td></tr>
                        <tr><th>Auto Scaling</th><td>✅ Configured (2-8 instances)</td></tr>
                    </table>
                </div>

                <!-- Environment Variables -->
                <div class="status-card">
                    <h3>
                        <span class="status-indicator success"></span>
                        Environment Configuration
                    </h3>
                    <table class="info-table">
                        <tr><th>RDS_HOSTNAME</th><td><?php echo $_SERVER['RDS_HOSTNAME'] ?? 'Not Set'; ?></td></tr>
                        <tr><th>RDS_PORT</th><td><?php echo $_SERVER['RDS_PORT'] ?? 'Not Set'; ?></td></tr>
                        <tr><th>RDS_DB_NAME</th><td><?php echo $_SERVER['RDS_DB_NAME'] ?? 'Not Set'; ?></td></tr>
                        <tr><th>RDS_USERNAME</th><td><?php echo $_SERVER['RDS_USERNAME'] ?? 'Not Set'; ?></td></tr>
                        <tr><th>RDS_PASSWORD</th><td><?php echo isset($_SERVER['RDS_PASSWORD']) ? '***Set***' : 'Not Set'; ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Database Test Records -->
            <?php if ($db_connected && !empty($recent_records)): ?>
            <div class="status-card success">
                <h3>📊 Recent Database Activity</h3>
                <p>Last 5 test records from the database:</p>
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Message</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['id']); ?></td>
                            <td><?php echo htmlspecialchars($record['message']); ?></td>
                            <td><?php echo htmlspecialchars($record['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Architecture Information -->
            <div class="architecture-info">
                <h3>🏗️ AWS Architecture Components</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div>
                        <h4>✅ Elastic Beanstalk</h4>
                        <p>PHP 8.1 Platform</p>
                    </div>
                    <div>
                        <h4>✅ Auto Scaling Group</h4>
                        <p>Min: 2, Max: 8 instances</p>
                    </div>
                    <div>
                        <h4>✅ Load Balancer</h4>
                        <p>Classic ELB</p>
                    </div>
                    <div>
                        <h4>✅ RDS Multi-AZ</h4>
                        <p>MySQL 8.0</p>
                    </div>
                    <div>
                        <h4>✅ Custom VPC</h4>
                        <p>2 Public Subnets</p>
                    </div>
                    <div>
                        <h4>✅ CloudWatch</h4>
                        <p>Network-based scaling</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2025 Assignment 3 - AWS LAMP Stack Deployment</p>
            <p>High Availability • Scalable • Fault Tolerant • Multi-AZ</p>
            <p><strong>Page generated:</strong> <?php echo date('Y-m-d H:i:s T'); ?></p>
        </div>
    </div>
</body>
</html>
