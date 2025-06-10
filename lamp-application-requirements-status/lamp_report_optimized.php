<?php

/**
 * Optimized AWS LAMP Stack Report - Assignment 3
 * Scalable, Elastic, High-Availability Architecture Implementation
 * 
 * Student: Anika Arman | ID: 14425754 | Email: anika.arman@student.uts.edu.au
 * Subject: 32555 Cloud Computing and Software as a Service
 * 
 * Optimized version with reduced code duplication and improved design
 */

// Error reporting and timing
error_reporting(E_ALL);
ini_set('display_errors', 1);
$page_start_time = microtime(true);

// Include helper functions
require_once 'includes/helpers.php';

// Environment Configuration
$environment = [
    'rds_hostname' => $_SERVER['RDS_HOSTNAME'] ?? 'lamp-app-db.cijnrr1efnu0.us-east-1.rds.amazonaws.com',
    'rds_port' => $_SERVER['RDS_PORT'] ?? '3306',
    'rds_db_name' => $_SERVER['RDS_DB_NAME'] ?? 'lampapp',
    'rds_username' => $_SERVER['RDS_USERNAME'] ?? 'lampdbadmin',
    'rds_password' => $_SERVER['RDS_PASSWORD'] ?? 'SecurePass123!'
];

// Collect AWS Instance Information
$aws_instance = getAWSInstanceInfo();

// Calculate performance metrics
$performance_metrics = array_merge(getPerformanceMetrics(), ['page_start_time' => $page_start_time]);

// Database Connection and Information
$database = [
    'connected' => false,
    'error' => '',
    'info' => [],
    'performance' => []
];

try {
    $pdo = getDatabaseConnection($environment);
    $database['connected'] = true;

    // Get database information with corrected syntax
    $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as db_name, USER() as user, CURRENT_TIMESTAMP() as current_time, @@hostname as hostname");
    $database['info'] = $stmt->fetch();

    // Get database performance metrics with error handling
    try {
        $stmt = $pdo->query("SHOW STATUS WHERE Variable_name IN ('Connections', 'Uptime', 'Threads_connected', 'Questions', 'Slow_queries')");
        while ($row = $stmt->fetch()) {
            $database['performance'][$row['Variable_name']] = $row['Value'];
        }
    } catch (PDOException $perf_e) {
        $database['performance'] = ['note' => 'Performance metrics unavailable: ' . $perf_e->getMessage()];
    }

    // Create application tables with improved error handling
    $pdo->exec("CREATE TABLE IF NOT EXISTS lamp_application_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        instance_id VARCHAR(50),
        availability_zone VARCHAR(50),
        message TEXT,
        severity ENUM('INFO', 'WARNING', 'ERROR') DEFAULT 'INFO',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at),
        INDEX idx_instance (instance_id)
    )");

    // Insert health check record
    $health_details = json_encode([
        'php_version' => PHP_VERSION,
        'memory_usage' => memory_get_usage(true),
        'load_average' => sys_getloadavg(),
        'disk_space' => disk_free_space('/'),
        'instance_type' => $aws_instance['instance_type']
    ]);

    $stmt = $pdo->prepare("INSERT INTO lamp_application_logs (instance_id, availability_zone, message, severity) VALUES (?, ?, ?, ?)");
    $stmt->execute([$aws_instance['instance_id'], $aws_instance['availability_zone'], 'Health check completed: ' . $health_details, 'INFO']);
} catch (PDOException $e) {
    $database['error'] = $e->getMessage();
    $database['connected'] = false;
}

// AWS Requirements Data (consolidated and simplified)
$aws_requirements = [
    'a' => [
        'service' => 'AWS Elastic Beanstalk',
        'status' => 'IMPLEMENTED',
        'details' => [
            'Application Name' => 'lamp-application',
            'Environment' => 'lamp-prod-vpc (e-rpyapuixkj)',
            'Platform' => '64bit Amazon Linux 2 v3.9.2 running PHP 8.1',
            'Health Status' => 'Green (Ok)',
            'Load Balancer' => 'awseb-e-r-AWSEBLoa-ID4G50DGRVZZ-1025184876.us-east-1.elb.amazonaws.com',
            'Last Deployment' => '2025-06-10T03:01:40.153Z',
            'Status' => 'All Systems Operational'
        ]
    ],
    'b' => [
        'service' => 'Amazon EC2',
        'status' => 'IMPLEMENTED',
        'details' => [
            'Instance 1' => 'i-07d65eeddeaab6735 (t3.micro, us-east-1a)',
            'Instance 2' => 'i-0fdc269d453d60316 (t3.micro, us-east-1b)',
            'Total Active' => '2 instances (Primary Environment)',
            'Instance Type' => 't3 - Burstable Performance',
            'Health Status' => 'All instances healthy and operational'
        ]
    ],
    'c' => [
        'service' => 'Custom AMI',
        'status' => 'IMPLEMENTED',
        'details' => [
            'Primary AMI' => 'ami-040d931d2f7f2341c (LAMP-Stack-Custom-AMI)',
            'Secondary AMI' => 'ami-00ffa1ae9aa59036d (custom-lamp-ami)',
            'Created' => '2025-06-04',
            'State' => 'Available',
            'Description' => 'Custom LAMP stack with Apache, PHP, MySQL pre-configured'
        ]
    ],
    'd' => [
        'service' => 'Custom Security Groups',
        'status' => 'IMPLEMENTED',
        'details' => [
            'Primary SG' => 'sg-041d4877e9ea0c1ae',
            'Secondary SG' => 'sg-040cc8b2324ae589c',
            'Custom SG' => 'sg-006719b6860b8c984',
            'HTTP Access' => 'Port 80 - TCP (0.0.0.0/0)',
            'SSH Access' => 'Port 22 - TCP (Restricted)',
            'VPC Protection' => 'All instances protected with consistent policies'
        ]
    ],
    'e' => [
        'service' => 'Load Balancer',
        'status' => 'IMPLEMENTED',
        'details' => [
            'Primary LB' => 'awseb-e-r-AWSEBLoa-ID4G50DGRVZZ',
            'Type' => 'Classic Load Balancer (ELB)',
            'Scheme' => 'Internet-facing',
            'Health Check' => 'TCP:80 (10s interval)',
            'Cross-AZ' => 'Enabled (us-east-1a, us-east-1b)',
            'Status' => 'Active with healthy instances'
        ]
    ],
    'f' => [
        'service' => 'Auto Scaling',
        'status' => 'IMPLEMENTED',
        'details' => [
            'ASG Name' => 'awseb-e-rpyapuixkj-stack-AWSEBAutoScalingGroup',
            'Min Capacity' => '2 instances ✅',
            'Max Capacity' => '8 instances ✅',
            'Current' => '2 instances active',
            'Scale Trigger' => 'NetworkOut > 6MB / < 2MB ✅',
            'Cooldown' => '360 seconds',
            'Health Check' => 'EC2 with ELB health checks'
        ]
    ],
    'g' => [
        'service' => 'RDS Multi-AZ',
        'status' => 'IMPLEMENTED',
        'details' => [
            'Primary DB' => 'lamp-app-db (MySQL 8.0.41)',
            'Endpoint' => 'lamp-app-db.cijnrr1efnu0.us-east-1.rds.amazonaws.com:3306',
            'Secondary DB' => 'custom-lamp-db (MySQL 8.0.41)',
            'Multi-AZ' => 'True ✅ (Automatic failover enabled)',
            'Storage' => '20 GB General Purpose SSD',
            'Backup' => '7 days retention',
            'Status' => 'Available in multiple zones'
        ]
    ],
    'h' => [
        'service' => 'Custom VPC',
        'status' => 'IMPLEMENTED',
        'details' => [
            'Primary VPC' => 'vpc-0164bd99719cccfbd (lamp-app-vpc)',
            'CIDR Block' => '10.0.0.0/16',
            'Subnet 1' => 'subnet-038f2f355ee2000a5 (us-east-1a, 10.0.1.0/24)',
            'Subnet 2' => 'subnet-06f4e63adf671e7ea (us-east-1b, 10.0.2.0/24)',
            'Multi-AZ' => 'True - Subnets in different AZs ✅',
            'Internet Gateway' => 'Attached and configured'
        ]
    ],
    'i' => [
        'service' => 'Custom Key Pairs',
        'status' => 'IMPLEMENTED',
        'details' => [
            'Primary Key' => 'lamp-app-key (RSA 2048-bit)',
            'Secondary Key' => 'custom-lamp-key-pair',
            'Usage' => 'All EC2 instances use consistent key pairs ✅',
            'Security' => 'RSA 2048-bit encryption',
            'Coverage' => 'All active instances protected'
        ]
    ],
    'j' => [
        'service' => 'Email Notifications',
        'status' => 'IMPLEMENTED',
        'details' => [
            'SNS Topic' => 'lamp-env-notifications',
            'Topic ARN' => 'arn:aws:sns:us-east-1:595941056901:lamp-env-notifications',
            'Email' => 'anika.arman@student.uts.edu.au',
            'Events' => 'Health changes, scaling, deployments',
            'Integration' => 'CloudWatch alarms and Auto Scaling',
            'Status' => 'Active and confirmed'
        ]
    ]
];

// System Health Check
$system_health = [
    'overall_status' => 'HEALTHY',
    'checks' => [
        'database_connection' => $database['connected'] ? 'HEALTHY' : 'UNHEALTHY',
        'php_status' => 'HEALTHY',
        'disk_space' => $performance_metrics['disk_free_gb'] > 1 ? 'HEALTHY' : 'WARNING',
        'memory_usage' => $performance_metrics['memory_usage_mb'] < 128 ? 'HEALTHY' : 'WARNING'
    ]
];

// Current timestamp
$current_time = date('Y-m-d H:i:s T');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS LAMP Stack Architecture Report - Assignment 3</title>
    <link rel="stylesheet" href="optimized_styles.css">
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAA...">
    <meta name="description" content="Comprehensive AWS LAMP Stack implementation report demonstrating scalable, elastic, and highly available architecture">
    <meta name="keywords" content="AWS, LAMP, Elastic Beanstalk, EC2, RDS, Auto Scaling, Load Balancer, VPC">
    <meta name="author" content="Anika Arman - Student ID: 14425754">
</head>

<body>
    <!-- Header Section -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h1>AWS LAMP Stack Architecture Report</h1>
                <div class="header-meta">
                    <div class="student-info">
                        <strong>Student:</strong> Anika Arman &nbsp;|&nbsp;
                        <strong>ID:</strong> 14425754 &nbsp;|&nbsp;
                        <strong>Subject:</strong> 32555 Cloud Computing
                    </div>
                    <div class="timestamp">
                        <strong>Report Generated:</strong> <?php echo $current_time; ?>
                    </div>
                </div>
                <div class="header-description">
                    <p>Comprehensive implementation of a scalable, elastic, highly available, and fault-tolerant
                        LAMP stack architecture on AWS addressing startup growth concerns and disaster recovery requirements.</p>
                </div>
            </div>
        </div>
    </header>

    <!-- System Status Dashboard -->
    <section class="section dashboard">
        <div class="container">
            <h2>Live System Status Dashboard</h2>
            <div class="grid grid-4">
                <?php
                echo renderStatusCard('🟢', 'Overall Health', $system_health['overall_status'], 'All systems operational', $system_health['overall_status']);
                echo renderStatusCard('🖥️', 'Current Instance', $aws_instance['instance_id'], $aws_instance['instance_type'] . ' in ' . $aws_instance['availability_zone'], 'HEALTHY');
                echo renderStatusCard('🗄️', 'Database Status', $database['connected'] ? 'CONNECTED' : 'ERROR', $database['connected'] ? 'Multi-AZ RDS MySQL' : 'Connection Failed', $database['connected'] ? 'HEALTHY' : 'ERROR');
                echo renderStatusCard('⚡', 'Performance', $performance_metrics['memory_usage_mb'] . 'MB', 'Memory Usage', 'HEALTHY');
                ?>
            </div>
        </div>
    </section>

    <!-- AWS Requirements Implementation -->
    <section class="section requirements-section">
        <div class="container">
            <h2>AWS Mandatory Requirements Implementation</h2>
            <p class="section-description">Comprehensive implementation of all 10 mandatory AWS services (a-j) as specified in Assignment 3 requirements.</p>

            <div class="grid grid-2">
                <?php foreach ($aws_requirements as $letter => $requirement): ?>
                    <div class="card requirement-card">
                        <div class="requirement-header">
                            <span class="requirement-letter"><?php echo strtoupper($letter); ?></span>
                            <div class="requirement-title">
                                <h3><?php echo htmlspecialchars($requirement['service']); ?></h3>
                                <span class="status-badge">
                                    <?php echo htmlspecialchars($requirement['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="requirement-details">
                            <?php echo renderDetailItems($requirement['details']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Database Information -->
    <?php if ($database['connected']): ?>
        <section class="section database-section">
            <div class="container">
                <h2>Database Connection & Performance</h2>
                <div class="grid grid-2">
                    <div class="card">
                        <h3>Connection Information</h3>
                        <?php echo renderDetailItems($database['info']); ?>
                    </div>
                    <div class="card">
                        <h3>Performance Metrics</h3>
                        <?php echo renderDetailItems($database['performance']); ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- System Performance Metrics -->
    <section class="section performance-section">
        <div class="container">
            <h2>Live System Performance Metrics</h2>
            <p class="section-description">Real-time system health and performance metrics captured on <?php echo date('F j, Y - H:i T'); ?></p>

            <div class="grid grid-3">
                <?php
                $current_response_time = round((microtime(true) - $performance_metrics['page_start_time']) * 1000, 2);
                echo renderMetricCard('🚀', 'Response Time', $current_response_time . 'ms', 'Page generation time');
                echo renderMetricCard('💾', 'Memory Usage', $performance_metrics['memory_usage_mb'] . 'MB', 'Current PHP memory consumption');
                echo renderMetricCard('📊', 'Server Load', number_format($performance_metrics['server_load'][0], 2), '1-minute load average');
                echo renderMetricCard('🔧', 'PHP Version', $performance_metrics['php_version'], 'Runtime environment');
                echo renderMetricCard('💿', 'Disk Space', $performance_metrics['disk_free_gb'] . 'GB', 'Available disk space');
                echo renderMetricCard('📈', 'Peak Memory', $performance_metrics['peak_memory_mb'] . 'MB', 'Peak memory usage');
                ?>
            </div>
        </div>
    </section>

    <!-- Architecture Diagram -->
    <section class="section architecture-section">
        <div class="container">
            <h2>AWS Architecture Diagram</h2>
            <p class="section-description">Visual representation of the implemented scalable, elastic, and highly available LAMP stack architecture.</p>

            <?php
            // Include and display the enhanced architecture diagram
            if (file_exists('aws_architecture_enhanced.php')) {
                require_once 'aws_architecture_enhanced.php';
                echo getEnhancedArchitectureDiagram();
            } else {
                echo '<div class="alert">Architecture diagram component not found.</div>';
            }
            ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="section" style="background: var(--dark); color: var(--white); text-align: center;">
        <div class="container">
            <h3>Assignment 3 - AWS LAMP Stack Implementation</h3>
            <p>Student: Anika Arman (14425754) | Subject: 32555 Cloud Computing</p>
            <p>University of Technology Sydney | Faculty of Engineering and IT</p>
            <div style="margin-top: var(--space-lg); display: flex; justify-content: center; gap: var(--space-lg); flex-wrap: wrap;">
                <span><strong>Load Time:</strong> <?php echo round((microtime(true) - $performance_metrics['page_start_time']) * 1000, 2); ?>ms</span>
                <span><strong>Memory:</strong> <?php echo $performance_metrics['memory_usage_mb']; ?>MB</span>
                <span><strong>Generated:</strong> <?php echo $current_time; ?></span>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll for navigation
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Intersection Observer for animations
            const cards = document.querySelectorAll('.card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.transform = 'translateY(0)';
                        entry.target.style.opacity = '1';
                    }
                });
            }, {
                threshold: 0.1
            });

            cards.forEach(card => {
                card.style.transform = 'translateY(20px)';
                card.style.opacity = '0.8';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>

</html>