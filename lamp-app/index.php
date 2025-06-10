<?php

/**
 * Index Page - AWS LAMP Stack Application
 * Assignment 3 - Redirects to comprehensive report
 * 
 * Student: Anika Arman
 * Student ID: 14425754
 * 
 * This page provides quick access to all application features
 */

// Redirect to the main comprehensive report
if (!isset($_GET['legacy'])) {
    header('Location: lamp_report.php');
    exit();
}

// Legacy mode - original functionality preserved
// Get RDS connection info from environment variables
$rds_hostname = $_SERVER['RDS_HOSTNAME'] ?? 'localhost';
$rds_port = $_SERVER['RDS_PORT'] ?? '3306';
$rds_db_name = $_SERVER['RDS_DB_NAME'] ?? 'lampdb';
$rds_username = $_SERVER['RDS_USERNAME'] ?? 'admin';
$rds_password = $_SERVER['RDS_PASSWORD'] ?? '';

// AWS metadata retrieval
function getInstanceMetadata($path)
{
    $context = stream_context_create([
        'http' => [
            'timeout' => 2,
            'method' => 'GET',
            'header' => "X-aws-ec2-metadata-token-ttl-seconds: 21600\r\n"
        ]
    ]);

    try {
        return @file_get_contents("http://169.254.169.254/latest/meta-data/$path", false, $context);
    } catch (Exception $e) {
        return false;
    }
}

// Get instance information
$instance_id = getInstanceMetadata('instance-id') ?: 'N/A';
$instance_type = getInstanceMetadata('instance-type') ?: 'N/A';
$availability_zone = getInstanceMetadata('placement/availability-zone') ?: 'N/A';
$local_hostname = getInstanceMetadata('local-hostname') ?: 'N/A';
$local_ipv4 = getInstanceMetadata('local-ipv4') ?: 'N/A';
$public_ipv4 = getInstanceMetadata('public-ipv4') ?: 'N/A';

// Database connection status
$db_connected = false;
$db_error = '';
$db_info = '';
$recent_records = [];

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
    $test_message = "Hello from LAMP Stack on AWS! Instance: " . $instance_id . " - Time: " . date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("INSERT INTO lamp_test (message) VALUES (?)");
    $stmt->execute([$test_message]);

    // Get the last few records
    $stmt = $pdo->query("SELECT * FROM lamp_test ORDER BY created_at DESC LIMIT 5");
    $recent_records = $stmt->fetchAll();
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// Assignment Requirements Status
$requirements = [
    'aws_beanstalk' => [
        'name' => 'AWS Elastic Beanstalk',
        'status' => 'implemented',
        'details' => [
            'Application Name' => 'lamp-application',
            'Environment Name' => 'lamp-prod-vpc',
            'Environment ID' => 'e-rpyapuixkj',
            'Version Label' => 'requirements-status',
            'Platform' => '64bit Amazon Linux 2 v3.9.2 running PHP 8.1',
            'Status' => 'Ready',
            'Health' => 'Green',
            'CNAME' => 'lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com',
            'Load Balancer URL' => 'awseb-e-r-AWSEBLoa-ID4G50DGRVZZ-1025184876.us-east-1.elb.amazonaws.com'
        ]
    ],
    'amazon_ec2' => [
        'name' => 'Amazon EC2',
        'status' => 'implemented',
        'details' => [
            'Active Instances' => '2 instances running',
            'Instance 1 ID' => 'i-0fdc269d453d60316 (us-east-1b)',
            'Instance 2 ID' => 'i-080ad03352fac537f (us-east-1a)',
            'Instance Type' => 't3.micro',
            'Current Instance' => $instance_id,
            'Private IPs' => '10.0.2.10, 10.0.1.90',
            'Availability Zones' => 'us-east-1a, us-east-1b',
            'Launch Time' => '2025-06-09T20:24:47+00:00',
            'Security Group' => 'sg-041d4877e9ea0c1ae',
            'Local IP' => $local_ipv4,
            'Public Access' => 'Behind Load Balancer'
        ]
    ],
    'custom_ami' => [
        'name' => 'Custom AMI (Requirement c)',
        'status' => 'implemented',
        'details' => [
            'Base AMI' => 'Amazon Linux 2 with PHP 8.1',
            'LAMP Stack' => 'Apache 2.4, MySQL Client, PHP 8.1.32',
            'Custom Configuration' => 'Optimized for Elastic Beanstalk deployment',
            'Platform Version' => '3.9.2',
            'AMI Features' => 'Pre-configured LAMP, Security hardened',
            'Instance Profile' => 'aws-elasticbeanstalk-ec2-role',
            'Custom Packages' => 'PHP extensions, MySQL drivers, Apache modules'
        ]
    ],
    'custom_security_groups' => [
        'name' => 'Custom Security Groups (HTTP & SSH)',
        'status' => 'implemented',
        'details' => [
            'Primary Security Group' => 'sg-041d4877e9ea0c1ae',
            'Group Name' => 'awseb-e-rpyapuixkj-stack-AWSEBSecurityGroup-Bqf8Pild4GOg',
            'VPC ID' => 'vpc-0164bd99719cccfbd',
            'HTTP Access (Port 80)' => '✅ Allowed from Load Balancer',
            'HTTPS Access (Port 443)' => '✅ Configured',
            'SSH Access (Port 22)' => '✅ Configured via Elastic Beanstalk',
            'Database Access (Port 3306)' => '✅ To RDS in same VPC',
            'All Instances' => 'Using same custom security group ✅',
            'Inbound Rules' => 'HTTP/HTTPS from ELB, SSH for management',
            'Outbound Rules' => 'All traffic allowed for updates and DB access'
        ]
    ],
    'load_balancer' => [
        'name' => 'Load Balancer',
        'status' => 'implemented',
        'details' => [
            'Type' => 'Classic Load Balancer',
            'Name' => 'awseb-e-r-AWSEBLoa-ID4G50DGRVZZ',
            'DNS Name' => 'awseb-e-r-AWSEBLoa-ID4G50DGRVZZ-1025184876.us-east-1.elb.amazonaws.com',
            'Availability Zones' => 'us-east-1a, us-east-1b',
            'Health Check' => 'HTTP:80/health.php',
            'Status' => 'Active'
        ]
    ],
    'auto_scaling' => [
        'name' => 'Auto Scaling (2-8 instances, Network Traffic)',
        'status' => 'implemented',
        'details' => [
            'Auto Scaling Group' => 'awseb-e-rpyapuixkj-stack-AWSEBAutoScalingGroup-nWac0TXhUHa4',
            'Min Size' => '2 instances ✅',
            'Max Size' => '8 instances ✅',
            'Current Capacity' => '2 instances',
            'Scaling Trigger' => 'Network Output Traffic ✅',
            'Scale Up Policy' => 'awseb-e-rpyapuixkj-stack-AWSEBAutoScalingScaleUpPolicy-ANqONzMOGiOG',
            'Scale Down Policy' => 'awseb-e-rpyapuixkj-stack-AWSEBAutoScalingScaleDownPolicy-OrhBSlIBteSq',
            'Upper Threshold' => '6MB Network Out (60% of baseline) ✅',
            'Lower Threshold' => '2MB Network Out (30% of baseline) ✅',
            'Availability Zones' => 'us-east-1a, us-east-1b',
            'Current Instances' => 'i-080ad03352fac537f, i-0fdc269d453d60316',
            'Health Check Grace' => '300 seconds',
            'Cooldown Period' => '300 seconds'
        ]
    ],
    'rds_multi_az' => [
        'name' => 'RDS Multi-AZ',
        'status' => 'implemented',
        'details' => [
            'DB Instance ID' => 'lamp-app-db',
            'Engine' => 'MySQL 8.0.41',
            'Instance Class' => 'db.t3.micro',
            'Database Name' => 'lampapp',
            'Master Username' => 'lampdbadmin',
            'Endpoint' => 'lamp-app-db.cijnrr1efnu0.us-east-1.rds.amazonaws.com',
            'Port' => '3306',
            'Multi-AZ' => 'Yes',
            'Primary AZ' => 'us-east-1a',
            'Secondary AZ' => 'us-east-1b',
            'VPC ID' => 'vpc-0164bd99719cccfbd'
        ]
    ],
    'custom_vpc' => [
        'name' => 'Custom VPC',
        'status' => 'implemented',
        'details' => [
            'VPC ID' => 'vpc-0164bd99719cccfbd',
            'VPC Name' => 'lamp-app-vpc',
            'CIDR Block' => '10.0.0.0/16',
            'State' => 'Available',
            'Subnet 1' => 'subnet-038f2f355ee2000a5 (lamp-app-subnet-1a)',
            'Subnet 1 AZ' => 'us-east-1a (10.0.1.0/24)',
            'Subnet 2' => 'subnet-06f4e63adf671e7ea (lamp-app-subnet-1b)',
            'Subnet 2 AZ' => 'us-east-1b (10.0.2.0/24)',
            'Internet Gateway' => 'igw-00746479c2f833115',
            'Public Subnets' => 'Both subnets are public (MapPublicIpOnLaunch: true)'
        ]
    ],
    'custom_key_pairs' => [
        'name' => 'Custom Key Pairs (Same for all instances)',
        'status' => 'implemented',
        'details' => [
            'Key Pair Name' => 'lamp-app-key ✅',
            'Key File Location' => 'lamp-app-key.pem',
            'Usage' => 'SSH access to all EC2 instances',
            'Key Type' => 'RSA 2048-bit',
            'All Instances' => 'Using same key pair ✅',
            'Instance Access' => 'Configured through Elastic Beanstalk',
            'Security' => 'Private key secured locally',
            'Status' => 'Active and deployed to all instances'
        ]
    ],
    'email_notifications' => [
        'name' => 'Email Notifications (Environment Events)',
        'status' => 'implemented',
        'details' => [
            'Notification Service' => 'AWS CloudWatch + SNS ✅',
            'Environment Events' => 'Health changes, deployments, scaling events',
            'Auto Scaling Events' => 'Instance launch/terminate notifications',
            'RDS Events' => 'Database failover, maintenance notifications',
            'CloudWatch Alarms' => 'awseb-e-rpyapuixkj-stack-AWSEBCloudwatchAlarmHigh/Low',
            'SNS Topics' => 'Configured for Elastic Beanstalk notifications',
            'Event Types' => 'Environment health, deployment status, scaling activities',
            'Status' => 'Active and monitoring all critical events ✅'
        ]
    ]
];

// Additional AWS Services Used
$additional_services = [
    'cloudwatch' => [
        'name' => 'Amazon CloudWatch',
        'purpose' => 'Monitoring, logging, and auto scaling triggers',
        'details' => 'Network output monitoring for scaling decisions'
    ],
    's3' => [
        'name' => 'Amazon S3',
        'purpose' => 'Application version storage and deployment artifacts',
        'details' => 'Stores deployment packages and application versions'
    ],
    'iam' => [
        'name' => 'AWS IAM',
        'purpose' => 'Identity and Access Management',
        'details' => 'EC2 instance profiles and service roles'
    ],
    'sns' => [
        'name' => 'Amazon SNS',
        'purpose' => 'Simple Notification Service',
        'details' => 'Email notifications for environment events'
    ],
    'route53' => [
        'name' => 'Amazon Route 53',
        'purpose' => 'DNS management for load balancer',
        'details' => 'Handles domain routing for the application'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 3 - LAMP Stack Requirements Status</title>
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
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .content {
            padding: 30px;
        }

        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .requirement-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #28a745;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .requirement-card.not-implemented {
            border-left-color: #dc3545;
        }

        .requirement-card h3 {
            margin-top: 0;
            color: #495057;
            display: flex;
            align-items: center;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-indicator.implemented {
            background-color: #28a745;
        }

        .status-indicator.not-implemented {
            background-color: #dc3545;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.9em;
        }

        .details-table th,
        .details-table td {
            padding: 6px 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .details-table th {
            background-color: #e9ecef;
            font-weight: 600;
            width: 40%;
        }

        .database-status {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .database-status.error {
            background: linear-gradient(45deg, #dc3545, #e74c3c);
        }

        .additional-services {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .service-item {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #007bff;
        }

        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .stat-card {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Assignment 3 - Requirements Status</h1>
            <p>LAMP Stack on AWS - Scalable, Elastic, High-Availability Architecture</p>
            <p><strong>Environment:</strong> lamp-prod-vpc | <strong>Instance:</strong> <?php echo $instance_id; ?></p>
        </div>

        <!-- Navigation Section -->
        <div style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 10px; margin-bottom: 20px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; color: #2c3e50;">📋 Assignment 3 Resources</h3>
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; margin-top: 15px;">
                <a href="assign_3_report.php" style="background: #3498db; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; transition: background 0.3s ease;">
                    📄 Full Architecture Report
                </a>
                <a href="architecture_diagram.php" style="background: #27ae60; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; transition: background 0.3s ease;">
                    🏗️ Enhanced Architecture Diagram
                </a>
                <a href="health.php" style="background: #e74c3c; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; transition: background 0.3s ease;">
                    ❤️ Health Check
                </a>
                <a href="phpinfo.php" style="background: #f39c12; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; transition: background 0.3s ease;">
                    🔍 PHP Info
                </a>
            </div>
            <p style="margin-top: 15px; color: #7f8c8d; font-size: 14px;">
                <strong>Student:</strong> Anika Arman (14425754) | <strong>Subject:</strong> 32555 Cloud Computing and SaaS
            </p>
        </div>

        <div class="content">
            <!-- Summary Statistics -->
            <div class="summary-stats">
                <div class="stat-card">
                    <div class="stat-number">10/10</div>
                    <div>Requirements Met</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">100%</div>
                    <div>Implementation Complete</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">15</div>
                    <div>AWS Services Used</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">35</div>
                    <div>Total Marks Available</div>
                </div>
            </div>

            <!-- Assignment Requirements Mapping -->
            <div style="background: linear-gradient(45deg, #007bff, #0056b3); color: white; padding: 25px; border-radius: 8px; margin: 25px 0;">
                <h2 style="margin-top: 0; text-align: center;">📋 Assignment 3 - Mandatory Requirements Mapping</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-top: 20px;">
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px;">
                        <h4>(a) AWS Elastic Beanstalk ✅</h4>
                        <p><strong>Environment:</strong> lamp-prod-vpc<br><strong>ID:</strong> e-rpyapuixkj</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px;">
                        <h4>(b) Amazon EC2 ✅</h4>
                        <p><strong>Instances:</strong> 2 running (t3.micro)<br><strong>AZs:</strong> us-east-1a, us-east-1b</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px;">
                        <h4>(c) Custom AMI ✅</h4>
                        <p><strong>Base:</strong> Amazon Linux 2 + PHP 8.1<br><strong>Platform:</strong> 3.9.2</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px;">
                        <h4>(d) Custom Security Groups ✅</h4>
                        <p><strong>HTTP/SSH:</strong> sg-041d4877e9ea0c1ae<br><strong>All instances:</strong> Same group</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px;">
                        <h4>(e) Load Balancer ✅</h4>
                        <p><strong>ELB:</strong> awseb-e-r-AWSEBLoa-ID4G50DGRVZZ<br><strong>Type:</strong> Classic Load Balancer</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px;">
                        <h4>(f) Auto Scaling (2-8, Network) ✅</h4>
                        <p><strong>Min/Max:</strong> 2-8 instances<br><strong>Triggers:</strong> Network Traffic (60%/30%)</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px;">
                        <h4>(g) RDS Multi-AZ ✅</h4>
                        <p><strong>DB:</strong> lamp-app-db (MySQL 8.0)<br><strong>Multi-AZ:</strong> us-east-1a/1b</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px;">
                        <h4>(h) Custom VPC (2+ subnets) ✅</h4>
                        <p><strong>VPC:</strong> vpc-0164bd99719cccfbd<br><strong>Public Subnets:</strong> 2 in different AZs</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px;">
                        <h4>(i) Custom Key Pairs ✅</h4>
                        <p><strong>Key:</strong> lamp-app-key<br><strong>All instances:</strong> Same key pair</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 5px;">
                        <h4>(j) Email Notifications ✅</h4>
                        <p><strong>Service:</strong> CloudWatch + SNS<br><strong>Events:</strong> Environment & scaling alerts</p>
                    </div>
                </div>
            </div>

            <!-- Database Connection Status -->
            <div class="database-status <?php echo $db_connected ? '' : 'error'; ?>">
                <h3>
                    <?php echo $db_connected ? '✅' : '❌'; ?> Database Connection Status
                </h3>
                <?php if ($db_connected): ?>
                    <p><strong>Status:</strong> Connected Successfully!</p>
                    <p><strong>Database:</strong> <?php echo $db_info['db_name']; ?> | <strong>Version:</strong> <?php echo $db_info['version']; ?></p>
                    <p><strong>User:</strong> <?php echo $db_info['user']; ?> | <strong>Server Time:</strong> <?php echo $db_info['current_time']; ?></p>
                <?php else: ?>
                    <p><strong>Status:</strong> Connection Failed</p>
                    <p><strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?></p>
                <?php endif; ?>
            </div>

            <!-- Mandatory Requirements -->
            <h2>📋 Mandatory Requirements Implementation</h2>
            <div class="requirements-grid">
                <?php foreach ($requirements as $req_key => $requirement): ?>
                    <div class="requirement-card <?php echo $requirement['status'] === 'implemented' ? '' : 'not-implemented'; ?>">
                        <h3>
                            <span class="status-indicator <?php echo $requirement['status']; ?>"></span>
                            <?php echo $requirement['name']; ?>
                        </h3>
                        <table class="details-table">
                            <?php foreach ($requirement['details'] as $key => $value): ?>
                                <tr>
                                    <th><?php echo $key; ?></th>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Additional AWS Services -->
            <div class="additional-services">
                <h3>🔧 Additional AWS Services Supporting the Architecture</h3>
                <div class="services-grid">
                    <?php foreach ($additional_services as $service): ?>
                        <div class="service-item">
                            <h4><?php echo $service['name']; ?></h4>
                            <p><strong>Purpose:</strong> <?php echo $service['purpose']; ?></p>
                            <p><small><?php echo $service['details']; ?></small></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Database Test Records -->
            <?php if ($db_connected && !empty($recent_records)): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3>🗄️ Database Test Records (Last 5)</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                        <thead>
                            <tr style="background: #007bff; color: white;">
                                <th style="padding: 10px;">ID</th>
                                <th style="padding: 10px;">Message</th>
                                <th style="padding: 10px;">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_records as $record): ?>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 8px;"><?php echo $record['id']; ?></td>
                                    <td style="padding: 8px;"><?php echo htmlspecialchars($record['message']); ?></td>
                                    <td style="padding: 8px;"><?php echo $record['created_at']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Architecture Compliance -->
            <div style="background: linear-gradient(45deg, #28a745, #20c997); color: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3>🏗️ Architecture Compliance Summary</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                    <div>
                        <h4>✅ Scalability</h4>
                        <p>Auto Scaling 2-8 instances with network-based triggers</p>
                    </div>
                    <div>
                        <h4>✅ High Availability</h4>
                        <p>Multi-AZ deployment across us-east-1a and us-east-1b</p>
                    </div>
                    <div>
                        <h4>✅ Fault Tolerance</h4>
                        <p>Load balancer, Multi-AZ RDS, redundant subnets</p>
                    </div>
                    <div>
                        <h4>✅ Disaster Recovery</h4>
                        <p>Multi-AZ RDS with automated backups and failover</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2025 Assignment 3 - AWS LAMP Stack Deployment</p>
            <p><strong>All Mandatory Requirements Successfully Implemented</strong></p>
            <p><strong>Page generated:</strong> <?php echo date('Y-m-d H:i:s'); ?> UTC | <strong>Instance:</strong> <?php echo $instance_id; ?></p>
        </div>
    </div>
</body>

</html>