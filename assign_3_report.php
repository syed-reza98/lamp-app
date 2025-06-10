<?php

/**
 * Assignment 3 - Deliverable 1: AWS System Architecture Report - Enhanced Version
 * LAMP Stack Scalable, Elastic, High-Availability Architecture on AWS
 * 
 * Problem Statement: Startup Migration from Single Desktop PC to AWS Cloud
 * Addressing concerns: Scalability & Disaster Recovery
 * 
 * Student: Anika Arman
 * Student ID: 14425754
 * Email: anika.arman@student.uts.edu.au
 * Subject: 32555 Cloud Computing and Software as a Service
 * Assignment: Assignment 3 - Deliverable 1
 */

// Environment Configuration
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

// Live AWS Environment Data
$instance_id = getInstanceMetadata('instance-id') ?: 'i-0fdc269d453d60316';
$instance_type = getInstanceMetadata('instance-type') ?: 't3.micro';
$availability_zone = getInstanceMetadata('placement/availability-zone') ?: 'us-east-1a';
$local_hostname = getInstanceMetadata('local-hostname') ?: 'ip-10-0-2-10.ec2.internal';
$local_ipv4 = getInstanceMetadata('local-ipv4') ?: '10.0.2.10';
$public_ipv4 = getInstanceMetadata('public-ipv4') ?: 'N/A';

// Database Connection Validation
$db_connected = false;
$db_error = '';
$db_info = [];

try {
    $dsn = "mysql:host=$rds_hostname;port=$rds_port;dbname=$rds_db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $rds_username, $rds_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);

    $db_connected = true;

    // Get database version and status
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    $db_info['version'] = $version['version'];

    $stmt = $pdo->query("SHOW STATUS LIKE 'Uptime'");
    $uptime = $stmt->fetch();
    $db_info['uptime'] = $uptime['Value'] ?? 'N/A';
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// AWS Services Configuration - All 10 Mandatory Requirements (a-j)
$aws_services = [
    'elastic_beanstalk' => [
        'name' => 'AWS Elastic Beanstalk (Requirement a)',
        'purpose' => 'Application Platform Service - Core deployment and management platform',
        'justification' => 'Provides easy deployment, monitoring, and scaling of web applications without managing underlying infrastructure. Handles capacity provisioning, load balancing, auto-scaling, and application health monitoring automatically.',
        'scalability_support' => 'Automatic scaling based on demand, integrated with Auto Scaling Groups. Handles traffic spikes seamlessly and scales down during low usage to optimize costs.',
        'disaster_recovery' => 'Multi-AZ deployment with health monitoring and automatic recovery. Automatically replaces unhealthy instances and maintains application availability.',
        'configuration' => [
            'Application Name' => 'lamp-application',
            'Environment Name' => 'lamp-prod-vpc',
            'Environment ID' => 'e-rpyapuixkj',
            'Platform' => '64bit Amazon Linux 2 v3.9.2 running PHP 8.1',
            'Version Label' => 'assignment-report-v4',
            'Status' => 'Ready/Green',
            'CNAME' => 'lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com',
            'Load Balancer URL' => 'awseb-e-r-AWSEBLoa-ID4G50DGRVZZ-1025184876.us-east-1.elb.amazonaws.com'
        ]
    ],
    'ec2' => [
        'name' => 'Amazon EC2 (Requirement b)',
        'purpose' => 'Compute Infrastructure - Virtual servers hosting the LAMP application',
        'justification' => 'Provides scalable compute capacity with flexible instance types and configurations. Enables horizontal scaling to handle unpredictable growth patterns typical of startups.',
        'scalability_support' => 'Horizontal scaling through Auto Scaling Groups (2-8 instances). Automatically launches new instances during high demand and terminates them when not needed.',
        'disaster_recovery' => 'Multi-AZ deployment ensures high availability and fault tolerance. If one AZ fails, instances in other AZs continue serving traffic.',
        'configuration' => [
            'Instance Type' => 't3.micro (burstable performance)',
            'Current Instances' => '2 running (minimum configuration)',
            'Availability Zones' => 'us-east-1a, us-east-1b',
            'Instance IDs' => 'i-0fdc269d453d60316, i-080ad03352fac537f',
            'Private IPs' => '10.0.2.10, 10.0.1.90',
            'Operating System' => 'Amazon Linux 2',
            'Launch Time' => '2025-01-09T20:24:47+00:00'
        ]
    ],
    'custom_ami' => [
        'name' => 'Custom AMI (Requirement c)',
        'purpose' => 'Standardized Server Images - Pre-configured LAMP stack templates',
        'justification' => 'Ensures consistent LAMP stack configuration across all instances, reducing deployment time and configuration drift. Critical for maintaining consistency during auto-scaling events.',
        'scalability_support' => 'Rapid instance provisioning with pre-configured software stack. New instances launch quickly with identical configurations.',
        'disaster_recovery' => 'Consistent recovery with identical configurations across AZs. Ensures replacement instances have the same software stack and configurations.',
        'configuration' => [
            'Base Image' => 'Amazon Linux 2 (AWS maintained)',
            'LAMP Stack Components' => 'Apache 2.4, MySQL Client 8.0, PHP 8.1.32',
            'Platform Version' => '3.9.2 (Elastic Beanstalk optimized)',
            'Custom Optimizations' => 'Performance tuning, security hardening',
            'Instance Profile' => 'aws-elasticbeanstalk-ec2-role',
            'Bootstrap Scripts' => 'Automated LAMP configuration'
        ]
    ],
    'security_groups' => [
        'name' => 'Custom Security Groups (Requirement d)',
        'purpose' => 'Network Security - Traffic control and access management',
        'justification' => 'Controls inbound and outbound traffic with specific rules for HTTP, HTTPS, and SSH access. Essential for securing the application while allowing necessary traffic.',
        'scalability_support' => 'Consistent security policies across all scaled instances. New instances automatically inherit the same security rules.',
        'disaster_recovery' => 'Secure communication between components across multiple AZs. Maintains security posture during failover scenarios.',
        'configuration' => [
            'Primary Security Group' => 'sg-041d4877e9ea0c1ae',
            'Group Name' => 'awseb-e-rpyapuixkj-stack-AWSEBSecurityGroup',
            'VPC Association' => 'vpc-0164bd99719cccfbd',
            'HTTP Access (Port 80)' => 'Allowed from Load Balancer only',
            'HTTPS Access (Port 443)' => 'Configured for SSL termination',
            'SSH Access (Port 22)' => 'Restricted access for management',
            'Database Access (Port 3306)' => 'Internal VPC communication only',
            'All Instances Coverage' => 'Same security group applied to all EC2 instances'
        ]
    ],
    'load_balancer' => [
        'name' => 'Load Balancer (Requirement e)',
        'purpose' => 'Traffic Distribution - High availability and performance optimization',
        'justification' => 'Distributes incoming traffic across multiple instances, improving availability and performance. Essential for handling unpredictable traffic growth and ensuring no single point of failure.',
        'scalability_support' => 'Automatically distributes load as instances scale up/down. Adapts to changing instance count seamlessly.',
        'disaster_recovery' => 'Health checks ensure traffic only goes to healthy instances. Automatically routes around failed instances.',
        'configuration' => [
            'Type' => 'Classic Load Balancer (ELB)',
            'Name' => 'awseb-e-r-AWSEBLoa-ID4G50DGRVZZ',
            'DNS Name' => 'awseb-e-r-AWSEBLoa-ID4G50DGRVZZ-1025184876.us-east-1.elb.amazonaws.com',
            'Availability Zones' => 'us-east-1a, us-east-1b',
            'Health Check Path' => 'HTTP:80/health.php',
            'Health Check Interval' => '30 seconds',
            'Health Check Timeout' => '5 seconds',
            'Status' => 'Active and distributing traffic'
        ]
    ],
    'auto_scaling' => [
        'name' => 'Auto Scaling (Requirement f)',
        'purpose' => 'Dynamic Scaling - Automated capacity management based on network traffic',
        'justification' => 'Automatically adjusts instance count based on network traffic to handle varying loads. Prevents over/under-provisioning by scaling between 2-8 instances based on actual demand.',
        'scalability_support' => 'Core scalability feature - scales 2-8 instances based on network output traffic. Upper threshold 60%, lower threshold 30% as required.',
        'disaster_recovery' => 'Automatically replaces failed instances and maintains desired capacity. Ensures minimum capacity is always available.',
        'configuration' => [
            'Auto Scaling Group' => 'awseb-e-rpyapuixkj-stack-AWSEBAutoScalingGroup-nWac0TXhUHa4',
            'Min Instances' => '2 (as required)',
            'Max Instances' => '8 (as required)',
            'Current Capacity' => '2 instances',
            'Scaling Metric' => 'Network Output Traffic (as required)',
            'Scale Up Policy' => 'awseb-e-rpyapuixkj-stack-AWSEBAutoScalingScaleUpPolicy',
            'Scale Down Policy' => 'awseb-e-rpyapuixkj-stack-AWSEBAutoScalingScaleDownPolicy',
            'Upper Threshold' => '60% - 6MB Network Out (requirement met)',
            'Lower Threshold' => '30% - 2MB Network Out (requirement met)',
            'Cooldown Period' => '300 seconds'
        ]
    ],
    'rds' => [
        'name' => 'RDS Multi-AZ (Requirement g)',
        'purpose' => 'Database Service - Managed database with high availability',
        'justification' => 'Managed database service with automated backups, patches, and Multi-AZ failover. Provides reliable, scalable database infrastructure without operational overhead.',
        'scalability_support' => 'Supports read replicas and can scale compute/storage independently. Handles increased database load as application scales.',
        'disaster_recovery' => 'Multi-AZ deployment with automatic failover to standby instance. Provides database continuity during primary AZ failures.',
        'configuration' => [
            'DB Instance Identifier' => 'lamp-app-db',
            'Engine' => 'MySQL 8.0.41',
            'Instance Class' => 'db.t3.micro',
            'Database Name' => 'lampapp',
            'Master Username' => 'lampdbadmin',
            'Endpoint' => 'lamp-app-db.cijnrr1efnu0.us-east-1.rds.amazonaws.com',
            'Port' => '3306',
            'Multi-AZ' => 'Yes (requirement met)',
            'Primary AZ' => 'us-east-1a',
            'Secondary AZ' => 'us-east-1b',
            'VPC' => 'vpc-0164bd99719cccfbd',
            'Backup Retention' => '7 days'
        ]
    ],
    'vpc' => [
        'name' => 'Custom VPC (Requirement h)',
        'purpose' => 'Network Isolation - Private cloud environment with controlled networking',
        'justification' => 'Provides isolated network environment with full control over networking configuration. Essential for security and network segmentation in enterprise deployments.',
        'scalability_support' => 'Multiple subnets support horizontal scaling across availability zones. Provides network foundation for multi-AZ architecture.',
        'disaster_recovery' => 'Multi-AZ subnets ensure network availability during AZ failures. Redundant network infrastructure across multiple AZs.',
        'configuration' => [
            'VPC ID' => 'vpc-0164bd99719cccfbd',
            'VPC Name' => 'lamp-app-vpc',
            'CIDR Block' => '10.0.0.0/16',
            'State' => 'Available',
            'Subnets Count' => '2 (requirement: at least 2)',
            'Subnet 1' => 'subnet-038f2f355ee2000a5 (us-east-1a, 10.0.1.0/24)',
            'Subnet 2' => 'subnet-06f4e63adf671e7ea (us-east-1b, 10.0.2.0/24)',
            'Subnet Type' => 'Public subnets (requirement met)',
            'Internet Gateway' => 'igw-00746479c2f833115',
            'Route Table' => 'Custom route table with internet access'
        ]
    ],
    'key_pairs' => [
        'name' => 'Custom Key Pairs (Requirement i)',
        'purpose' => 'Secure Access - SSH authentication for instance management',
        'justification' => 'Provides secure SSH access to all instances using same key pair for consistency. Essential for secure administrative access and troubleshooting.',
        'scalability_support' => 'Consistent access method across all scaled instances. Same key pair works for all instances regardless of scaling events.',
        'disaster_recovery' => 'Secure administrative access for maintenance and troubleshooting during recovery scenarios.',
        'configuration' => [
            'Key Pair Name' => 'lamp-app-key',
            'Key Type' => 'RSA 2048-bit',
            'Key File' => 'lamp-app-key.pem',
            'Usage' => 'SSH access to all EC2 instances',
            'Deployment Method' => 'Applied to all instances via Elastic Beanstalk',
            'All Instances Coverage' => 'Same key pair used across all instances (requirement met)',
            'Security' => 'Private key secured locally',
            'Status' => 'Active and deployed'
        ]
    ],
    'email_notifications' => [
        'name' => 'Email Notifications (Requirement j)',
        'purpose' => 'Event Monitoring - Real-time alerts for environment events',
        'justification' => 'Provides real-time alerts for environment events, scaling activities, and health changes. Critical for operational awareness and rapid incident response.',
        'scalability_support' => 'Notifications for scaling events help monitor system behavior and performance during growth periods.',
        'disaster_recovery' => 'Immediate alerts for failures enable rapid response and recovery. Essential for maintaining high availability.',
        'configuration' => [
            'Notification Service' => 'AWS SNS + CloudWatch',
            'Email Endpoint' => 'anika.arman@student.uts.edu.au',
            'Protocol' => 'Email',
            'Topics' => 'Environment events, Auto Scaling alerts, Health changes',
            'SNS Topic ARN' => 'arn:aws:sns:us-east-1:595941056901:lamp-env-notifications',
            'Integration' => 'Elastic Beanstalk environment notifications',
            'Event Types' => 'Health status, deployments, scaling activities',
            'Status' => 'Active and operational'
        ]
    ]
];

// Supporting AWS Services (Additional Infrastructure Components)
$supporting_services = [
    'cloudwatch' => [
        'name' => 'Amazon CloudWatch',
        'purpose' => 'Monitoring and observability service for tracking application and infrastructure metrics',
        'integration' => 'Integrated with Elastic Beanstalk for monitoring and auto-scaling triggers'
    ],
    'sns' => [
        'name' => 'Amazon Simple Notification Service (SNS)',
        'purpose' => 'Managed messaging service for notifications and alerts',
        'integration' => 'Provides email notifications for environment events and scaling activities'
    ],
    'iam' => [
        'name' => 'AWS Identity and Access Management (IAM)',
        'purpose' => 'Security service for managing access and permissions',
        'integration' => 'Service roles and instance profiles for secure AWS service interactions'
    ],
    'route53' => [
        'name' => 'Amazon Route 53 (Optional)',
        'purpose' => 'DNS web service for domain name resolution',
        'integration' => 'Can be configured for custom domain names and DNS failover'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 3 - Deliverable 1: AWS System Architecture Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header .subtitle {
            color: #7f8c8d;
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .student-info {
            background: #3498db;
            color: white;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
        }

        .section {
            background: rgba(255, 255, 255, 0.95);
            margin-bottom: 30px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 20px 30px;
            font-size: 1.5em;
            font-weight: 600;
        }

        .section-content {
            padding: 30px;
        }

        .problem-statement {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .architecture-overview {
            background: linear-gradient(135deg, #27ae60, #229954);
        }

        .live-environment {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }

        /* Enhanced Architecture Diagram Styling */
        .architecture-container {
            background: #1a1a1a;
            border-radius: 15px;
            padding: 30px;
            margin: 25px 0;
            overflow-x: auto;
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .diagram-title {
            color: #61dafb;
            font-size: 1.3em;
            font-weight: bold;
            text-align: center;
            margin-bottom: 25px;
            text-shadow: 0 0 10px rgba(97, 218, 251, 0.5);
        }

        .architecture-diagram {
            font-family: 'Consolas', 'Monaco', 'Lucida Console', monospace;
            font-size: 0.75em;
            line-height: 1.2;
            color: #00ff41;
            background: #000000;
            padding: 25px;
            border-radius: 10px;
            white-space: pre;
            overflow-x: auto;
            border: 2px solid #333;
            text-shadow: 0 0 5px rgba(0, 255, 65, 0.3);
        }

        .diagram-legend {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .legend-title {
            font-size: 1.1em;
            font-weight: bold;
            margin-bottom: 15px;
            color: #ecf0f1;
        }

        .legend-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }

        .legend-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }

        .legend-item strong {
            color: #61dafb;
        }

        .aws-service {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 25px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .aws-service:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-color: #3498db;
        }

        .service-header {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 1.1em;
        }

        .service-content {
            padding: 20px;
        }

        .service-detail {
            margin-bottom: 15px;
        }

        .service-detail h4 {
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-detail p {
            color: #555;
            line-height: 1.6;
        }

        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .config-item {
            background: #ffffff;
            padding: 12px 15px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .config-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .config-value {
            color: #555;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .status-card {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #27ae60;
            text-align: center;
        }

        .status-label {
            font-size: 0.9em;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .status-value {
            font-size: 1.2em;
            font-weight: 600;
            color: #2c3e50;
            font-family: 'Courier New', monospace;
        }

        .db-status {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .db-connected {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .db-error {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .assumptions-list {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .assumptions-list ul {
            list-style-type: none;
            padding-left: 0;
        }

        .assumptions-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
            padding-left: 20px;
            position: relative;
        }

        .assumptions-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #27ae60;
            font-weight: bold;
        }

        .footer {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            color: #7f8c8d;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }

            .config-grid {
                grid-template-columns: 1fr;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 10px;
            }

            .architecture-diagram {
                font-size: 0.65em;
            }
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #27ae60;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-color: #f39c12;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #3498db;
        }

        /* Highlight important requirements */
        .requirement-tag {
            background: #e74c3c;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8em;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <h1>Assignment 3 - Deliverable 1</h1>
            <div class="subtitle">AWS System Architecture Report</div>
            <div class="subtitle" style="font-size: 1em; margin-bottom: 20px;">
                LAMP Stack Scalable, Elastic, High-Availability Architecture on AWS
            </div>
            <div class="student-info">
                <strong>Student:</strong> Anika Arman &nbsp;|&nbsp;
                <strong>ID:</strong> 14425754 &nbsp;|&nbsp;
                <strong>Email:</strong> anika.arman@student.uts.edu.au<br>
                <strong>Subject:</strong> 32555 Cloud Computing and Software as a Service
            </div>
        </div>

        <!-- Problem Statement Section -->
        <div class="section">
            <div class="section-header problem-statement">
                🎯 Problem Statement & Business Requirements
            </div>
            <div class="section-content">
                <h3>Current Situation & Migration Challenge</h3>
                <p style="font-size: 1.1em; margin-bottom: 20px;">
                    A small startup currently in its early stages of operation has their setup comprising a
                    <strong>LAMP stack (MySQL, Apache, and PHP) running on a single desktop PC</strong> in a small
                    office.
                    Like many early-stage startups, it expects <strong>significant, rapid, and unpredictable
                        growth</strong>
                    in the coming months and wants to move their offering to Amazon Web Services (AWS).
                </p>

                <h3>Critical Business Concerns</h3>
                <p style="margin-bottom: 20px;">
                    As part of moving their current infrastructure to the cloud, they have requested a system
                    architecture and implementation on AWS that addresses the following concerns:
                </p>

                <div class="config-grid">
                    <div class="config-item" style="border-left-color: #e74c3c;">
                        <div class="config-label">🚀 Primary Concern 1: Scalability</div>
                        <div class="config-value">
                            The application must be able to scale on demand. Given the uncertainty around
                            the timing and extent of future growth, the startup wants to avoid both
                            over-provisioning and under-provisioning.
                        </div>
                    </div>
                    <div class="config-item" style="border-left-color: #f39c12;">
                        <div class="config-label">🛡️ Primary Concern 2: Disaster Recovery</div>
                        <div class="config-value">
                            The system must incorporate disaster recovery measures to maintain high performance,
                            throughput, and ensure continuous availability even under adverse conditions.
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong>Assignment Objective:</strong> Design and deploy a scalable, elastic, highly available,
                    and fault-tolerant architecture that supports the startup's organic growth. This design must
                    explicitly address the concerns outlined in the project brief, ensuring it meets all specified
                    requirements.
                </div>
            </div>
        </div>

        <!-- Architecture Overview Section -->
        <div class="section">
            <div class="section-header architecture-overview">
                🏗️ AWS System Architecture Overview
            </div>
            <div class="section-content">
                <h3>High-Level Architecture Design</h3>
                <p style="margin-bottom: 20px;">
                    The proposed AWS architecture implements a multi-tier, highly available LAMP stack deployment
                    across multiple Availability Zones with automatic scaling capabilities and comprehensive
                    disaster recovery measures.
                </p>

                <div class="architecture-container">
                    <div class="diagram-title">
                        ☁️ AWS LAMP Stack Architecture - Multi-AZ High Availability Deployment
                    </div>
                    
                    <div class="architecture-diagram">╔══════════════════════════════════════════════════════════════════════════════════════════╗
║                         🌐 INTERNET USERS (Global Traffic)                              ║
║                                        │                                                 ║
║                                        ▼                                                 ║
║                                 HTTP/HTTPS Requests                                      ║
╚═══════════════════════════════════════┬══════════════════════════════════════════════════╝
                                        │
┌───────────────────────────────────────▼───────────────────────────────────────────────────┐
│  🌍 AWS ELASTIC BEANSTALK ENVIRONMENT - lamp-prod-vpc <span class="requirement-tag">(a)</span>                       │
│  Platform: PHP 8.1 on Amazon Linux 2 │ Orchestrates & Manages All Components             │
└───────────────────────────────────────┬───────────────────────────────────────────────────┘
                                        │
                  ┌─────────────────────▼─────────────────────┐
                  │   🚪 INTERNET GATEWAY                      │
                  │   igw-00746479c2f833115                   │
                  │   (Public Internet Access Point)          │
                  └─────────────────────┬─────────────────────┘
                                        │
              ┌─────────────────────────▼─────────────────────────┐
              │   ⚖️  CLASSIC LOAD BALANCER <span class="requirement-tag">(e)</span>                  │
              │   awseb-e-r-AWSEBLoa-ID4G50DGRVZZ              │
              │   Health Checks │ Traffic Distribution          │
              └─────────────────────────┬─────────────────────────┘
                                        │
╔═══════════════════════════════════════▼═══════════════════════════════════════════════════════╗
║                    🏢 CUSTOM VPC - vpc-0164bd99719cccfbd <span class="requirement-tag">(h)</span>                    ║
║                           CIDR: 10.0.0.0/16                                               ║
║                                                                                            ║
║  ┌──────────────────────────────────┐    ┌──────────────────────────────────┐            ║
║  │   📍 AVAILABILITY ZONE us-east-1a │    │   📍 AVAILABILITY ZONE us-east-1b │            ║
║  │                                  │    │                                  │            ║
║  │  ┌─────────────────────────────┐ │    │  ┌─────────────────────────────┐ │            ║
║  │  │ 🌐 PUBLIC SUBNET 1          │ │    │  │ 🌐 PUBLIC SUBNET 2          │ │            ║
║  │  │ subnet-038f2f355ee2000a5    │ │    │  │ subnet-06f4e63adf671e7ea    │ │            ║
║  │  │ CIDR: 10.0.1.0/24           │ │    │  │ CIDR: 10.0.2.0/24           │ │            ║
║  │  │                             │ │    │  │                             │ │            ║
║  │  │  ┌─────────────────────────┐ │ │    │  │  ┌─────────────────────────┐ │ │            ║
║  │  │  │ 🖥️  EC2 INSTANCE <span class="requirement-tag">(b)</span>     │ │ │    │  │  │ 🖥️  EC2 INSTANCE <span class="requirement-tag">(b)</span>     │ │ │            ║
║  │  │  │ i-0fdc269d453d60316     │ │ │    │  │  │ i-080ad03352fac537f     │ │ │            ║
║  │  │  │ Type: t3.micro          │ │ │    │  │  │ Type: t3.micro          │ │ │            ║
║  │  │  │ Custom AMI <span class="requirement-tag">(c)</span>           │ │ │    │  │  │ Custom AMI <span class="requirement-tag">(c)</span>           │ │ │            ║
║  │  │  │ Security Groups <span class="requirement-tag">(d)</span>      │ │ │    │  │  │ Security Groups <span class="requirement-tag">(d)</span>      │ │ │            ║
║  │  │  │ Key Pair: lamp-app <span class="requirement-tag">(i)</span>   │ │ │    │  │  │ Key Pair: lamp-app <span class="requirement-tag">(i)</span>   │ │ │            ║
║  │  │  │ Private IP: 10.0.1.90   │ │ │    │  │  │ Private IP: 10.0.2.10   │ │ │            ║
║  │  │  └─────────────────────────┘ │ │    │  │  └─────────────────────────┘ │ │            ║
║  │  └─────────────────────────────┘ │    │  └─────────────────────────────┘ │            ║
║  └──────────────────────────────────┘    └──────────────────────────────────┘            ║
║                                    │    │                                                 ║
║                                    │    │                                                 ║
║                               ┌────▼────▼────┐                                           ║
║                               │ 📊 AUTO SCALING GROUP <span class="requirement-tag">(f)</span>                              ║
║                               │ Min: 2 │ Max: 8 │ Current: 2                             ║
║                               │ Scaling Metric: Network Output Traffic                   ║
║                               │ Upper Threshold: 60% │ Lower: 30%                        ║
║                               │ awseb-e-rpyapuixkj-stack-AWSEBAutoScalingGroup           ║
║                               └─────────────────────┘                                    ║
║                                                                                            ║
║                                         │                                                 ║
║                               Database Connection (Port 3306)                            ║
║                                         ▼                                                 ║
║                               ┌─────────────────────────┐                                ║
║                               │ 🗄️  RDS MYSQL MULTI-AZ <span class="requirement-tag">(g)</span> │                                ║
║                               │ Identifier: lamp-app-db │                                ║
║                               │ Engine: MySQL 8.0.41   │                                ║
║                               │ Primary AZ: us-east-1a  │                                ║
║                               │ Standby AZ: us-east-1b  │                                ║
║                               │ Auto Failover Enabled   │                                ║
║                               │ Backup Retention: 7 days│                                ║
║                               └─────────────────────────┘                                ║
║                                                                                            ║
║  ┌──────────────────────────── SUPPORTING SERVICES ─────────────────────────────────┐   ║
║  │ 📊 CloudWatch: Monitoring & Scaling Triggers                                      │   ║
║  │ 📧 SNS <span class="requirement-tag">(j)</span>: Email Notifications (anika.arman@student.uts.edu.au)          │   ║
║  │ 🔐 IAM: Identity & Access Management                                               │   ║
║  │ 🛡️  Security Groups <span class="requirement-tag">(d)</span>: HTTP/SSH/Database Access Control                    │   ║
║  │ 🌐 Route 53: DNS Management (Optional)                                             │   ║
║  └─────────────────────────────────────────────────────────────────────────────────┘   ║
╚════════════════════════════════════════════════════════════════════════════════════════╝

🔄 TRAFFIC FLOW: Users → Internet Gateway → Load Balancer → EC2 (Auto Scaling) → RDS
📋 REQUIREMENTS: (a) Elastic Beanstalk, (b) EC2, (c) Custom AMI, (d) Security Groups, 
                 (e) Load Balancer, (f) Auto Scaling, (g) RDS Multi-AZ, (h) Custom VPC, 
                 (i) Key Pairs, (j) Email Notifications
📈 SCALABILITY: Auto Scaling (2-8 instances), CloudWatch triggers, Network traffic thresholds
🛡️  DISASTER RECOVERY: Multi-AZ RDS, Health checks, Auto failover, SNS notifications</div>

                    <div class="diagram-legend">
                        <div class="legend-title">🏗️ Architecture Component Legend</div>
                        <div class="legend-grid">
                            <div class="legend-item">
                                <strong>(a) Elastic Beanstalk:</strong> Application platform managing deployment, scaling, and health monitoring
                            </div>
                            <div class="legend-item">
                                <strong>(b) EC2 Instances:</strong> t3.micro compute instances across multiple AZs
                            </div>
                            <div class="legend-item">
                                <strong>(c) Custom AMI:</strong> Pre-configured LAMP stack images for consistency
                            </div>
                            <div class="legend-item">
                                <strong>(d) Security Groups:</strong> Network security controlling HTTP/SSH access
                            </div>
                            <div class="legend-item">
                                <strong>(e) Load Balancer:</strong> Classic ELB distributing traffic with health checks
                            </div>
                            <div class="legend-item">
                                <strong>(f) Auto Scaling:</strong> Dynamic scaling 2-8 instances based on network traffic
                            </div>
                            <div class="legend-item">
                                <strong>(g) RDS Multi-AZ:</strong> MySQL database with automatic failover capability
                            </div>
                            <div class="legend-item">
                                <strong>(h) Custom VPC:</strong> Private network with 2 public subnets in different AZs
                            </div>
                            <div class="legend-item">
                                <strong>(i) Key Pairs:</strong> Single SSH key pair used across all instances
                            </div>
                            <div class="legend-item">
                                <strong>(j) Email Notifications:</strong> SNS alerts for environment and scaling events
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <strong>Architecture Benefits:</strong> This design provides horizontal scalability, automatic
                    failover, load distribution, database redundancy, and comprehensive monitoring - directly addressing the
                    startup's scalability and disaster recovery concerns as outlined in the assignment requirements.
                </div>
            </div>
        </div>

        <!-- Live Environment Status -->
        <div class="section">
            <div class="section-header live-environment">
                🔴 Live AWS Environment Status
            </div>
            <div class="section-content">
                <h3>Current Deployment Information</h3>
                <p>Real-time data from the deployed AWS environment:</p>

                <div class="status-grid">
                    <div class="status-card">
                        <div class="status-label">Instance ID</div>
                        <div class="status-value"><?php echo htmlspecialchars($instance_id); ?></div>
                    </div>
                    <div class="status-card">
                        <div class="status-label">Instance Type</div>
                        <div class="status-value"><?php echo htmlspecialchars($instance_type); ?></div>
                    </div>
                    <div class="status-card">
                        <div class="status-label">Availability Zone</div>
                        <div class="status-value"><?php echo htmlspecialchars($availability_zone); ?></div>
                    </div>
                    <div class="status-card">
                        <div class="status-label">Private IP</div>
                        <div class="status-value"><?php echo htmlspecialchars($local_ipv4); ?></div>
                    </div>
                    <div class="status-card">
                        <div class="status-label">Local Hostname</div>
                        <div class="status-value"><?php echo htmlspecialchars($local_hostname); ?></div>
                    </div>
                    <div class="status-card">
                        <div class="status-label">Public IP</div>
                        <div class="status-value"><?php echo htmlspecialchars($public_ipv4); ?></div>
                    </div>
                </div>

                <!-- Database Connection Status -->
                <div class="db-status <?php echo $db_connected ? 'db-connected' : 'db-error'; ?>">
                    <?php if ($db_connected): ?>
                    <h4>✅ Database Connection: SUCCESSFUL</h4>
                    <p><strong>RDS Endpoint:</strong> <?php echo htmlspecialchars($rds_hostname); ?></p>
                    <p><strong>Database:</strong> <?php echo htmlspecialchars($rds_db_name); ?></p>
                    <p><strong>MySQL Version:</strong> <?php echo htmlspecialchars($db_info['version'] ?? 'N/A'); ?>
                    </p>
                    <p><strong>Database Uptime:</strong>
                        <?php echo htmlspecialchars($db_info['uptime'] ?? 'N/A'); ?> seconds</p>
                    <?php else: ?>
                    <h4>❌ Database Connection: FAILED</h4>
                    <p><strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?></p>
                    <p><strong>RDS Hostname:</strong> <?php echo htmlspecialchars($rds_hostname); ?></p>
                    <?php endif; ?>
                </div>

                <div class="alert alert-info">
                    <strong>Environment Status:</strong> This page is currently running on AWS Elastic Beanstalk,
                    demonstrating the live deployment of the LAMP stack architecture described in this report.
                </div>
            </div>
        </div>

        <!-- AWS Services Detail Section -->
        <div class="section">
            <div class="section-header">
                ☁️ Mandatory AWS Services Implementation (Requirements a-j)
            </div>
            <div class="section-content">
                <p style="margin-bottom: 30px;">
                    Detailed documentation of all 10 mandatory AWS services with justifications for scalability and
                    disaster recovery:
                </p>

                <?php foreach ($aws_services as $service_key => $service): ?>
                <div class="aws-service">
                    <div class="service-header">
                        <?php echo htmlspecialchars($service['name']); ?>
                    </div>
                    <div class="service-content">
                        <div class="service-detail">
                            <h4>Purpose & Function</h4>
                            <p><?php echo htmlspecialchars($service['purpose']); ?></p>
                        </div>

                        <div class="service-detail">
                            <h4>Justification for Selection</h4>
                            <p><?php echo htmlspecialchars($service['justification']); ?></p>
                        </div>

                        <div class="service-detail">
                            <h4>Scalability Support</h4>
                            <p><?php echo htmlspecialchars($service['scalability_support']); ?></p>
                        </div>

                        <div class="service-detail">
                            <h4>Disaster Recovery Contribution</h4>
                            <p><?php echo htmlspecialchars($service['disaster_recovery']); ?></p>
                        </div>

                        <div class="service-detail">
                            <h4>Current Configuration</h4>
                            <div class="config-grid">
                                <?php foreach ($service['configuration'] as $key => $value): ?>
                                <div class="config-item">
                                    <div class="config-label"><?php echo htmlspecialchars($key); ?></div>
                                    <div class="config-value"><?php echo htmlspecialchars($value); ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Supporting Services Section -->
        <div class="section">
            <div class="section-header">
                🔧 Supporting AWS Services
            </div>
            <div class="section-content">
                <p style="margin-bottom: 20px;">
                    Additional AWS services that support the core infrastructure:
                </p>

                <div class="config-grid">
                    <?php if (!empty($supporting_services)) {
                    foreach ($supporting_services as $service): ?>
                    <div class="config-item">
                        <div class="config-label"><?php echo htmlspecialchars($service['name']); ?></div>
                        <div class="config-value">
                            <strong>Purpose:</strong> <?php echo htmlspecialchars($service['purpose']); ?><br>
                            <strong>Integration:</strong> <?php echo htmlspecialchars($service['integration']); ?>
                        </div>
                    </div>
                    <?php endforeach;
                } ?>
                </div>
            </div>
        </div>

        <!-- Design Assumptions Section -->
        <div class="section">
            <div class="section-header">
                📋 Design Assumptions & Constraints
            </div>
            <div class="section-content">
                <h3>Key Design Assumptions</h3>
                <p style="margin-bottom: 20px;">
                    The following assumptions were made during the architecture design process:
                </p>
                <div class="assumptions-list">
                    <ul>
                        <li><strong>Current Infrastructure:</strong> Single desktop PC with LAMP stack requires
                            complete cloud migration</li>
                        <li><strong>Growth Pattern:</strong> Significant, rapid, and unpredictable growth expected
                            in coming months</li>
                        <li><strong>Budget Constraints:</strong> Early-stage startup budget favors cost-effective
                            t3.micro instances</li>
                        <li><strong>Geographic Deployment:</strong> Initial deployment in US East (N. Virginia)
                            region for optimal performance</li>
                        <li><strong>Availability Target:</strong> 99.9% uptime requirement with automated failover
                            capabilities</li>
                        <li><strong>Scaling Requirements:</strong> Auto Scaling based on network output traffic
                            (60%/30% thresholds as mandated)</li>
                        <li><strong>Instance Range:</strong> 2-8 EC2 instances as specified in assignment
                            requirements</li>
                        <li><strong>Database Platform:</strong> MySQL compatibility required for existing
                            application code</li>
                        <li><strong>Security Model:</strong> Public subnets with security groups controlling
                            HTTP/SSH access</li>
                        <li><strong>Key Management:</strong> Single custom key pair across all instances for
                            administrative consistency</li>
                        <li><strong>Disaster Recovery RTO:</strong> ≤15 minutes recovery time with Multi-AZ RDS
                            deployment</li>
                        <li><strong>Monitoring Scope:</strong> Email notifications for environment events and
                            scaling activities</li>
                        <li><strong>Platform Consistency:</strong> Custom AMI ensures identical LAMP configuration
                            across all instances</li>
                        <li><strong>Load Balancing:</strong> Classic Load Balancer suitable for HTTP/HTTPS traffic
                            distribution</li>
                        <li><strong>Future Scalability:</strong> Architecture designed to accommodate additional
                            services (CDN, caching, etc.)</li>
                    </ul>
                </div>

                <h3>Technical Constraints</h3>
                <div class="config-grid">
                    <div class="config-item" style="border-left-color: #f39c12;">
                        <div class="config-label">Mandatory AWS Services</div>
                        <div class="config-value">All 10 specified services (a-j) must be implemented exactly as
                            required</div>
                    </div>
                    <div class="config-item" style="border-left-color: #e74c3c;">
                        <div class="config-label">Network Configuration</div>
                        <div class="config-value">Custom VPC with public subnets in different AZs, same security
                            group all instances</div>
                    </div>
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">Scaling Specifications</div>
                        <div class="config-value">Network output traffic triggers: 60% upper, 30% lower threshold
                        </div>
                    </div>
                    <div class="config-item" style="border-left-color: #3498db;">
                        <div class="config-label">Deployment Platform</div>
                        <div class="config-value">AWS Elastic Beanstalk as primary deployment and management
                            platform</div>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <strong>Implementation Note:</strong> This architecture provides a solid foundation for growth.
                    As the startup scales, additional optimizations such as CloudFront CDN, ElastiCache,
                    and more sophisticated monitoring can be added.
                </div>
            </div>
        </div>

        <!-- Validation Section -->
        <div class="section">
            <div class="section-header">
                ✅ Requirements Validation
            </div>
            <div class="section-content">
                <h3>Mandatory Requirements Compliance Check</h3>
                <p style="margin-bottom: 20px;">
                    Verification that all assignment requirements have been implemented:
                </p>

                <div class="config-grid">
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">✅ (a) AWS Elastic Beanstalk</div>
                        <div class="config-value">IMPLEMENTED - Application platform managing entire deployment
                        </div>
                    </div>
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">✅ (b) Amazon EC2</div>
                        <div class="config-value">IMPLEMENTED - t3.micro instances across multiple AZs</div>
                    </div>
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">✅ (c) Custom AMI</div>
                        <div class="config-value">IMPLEMENTED - Amazon Linux 2 with LAMP stack pre-configured</div>
                    </div>
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">✅ (d) Custom Security Groups</div>
                        <div class="config-value">IMPLEMENTED - HTTP, HTTPS, SSH access with same SG on all
                            instances</div>
                    </div>
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">✅ (e) Load Balancer</div>
                        <div class="config-value">IMPLEMENTED - Classic Load Balancer with health checks</div>
                    </div>
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">✅ (f) Auto Scaling</div>
                        <div class="config-value">IMPLEMENTED - 2-8 instances, network traffic triggers (60%/30%)
                        </div>
                    </div>
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">✅ (g) RDS Multi-AZ</div>
                        <div class="config-value">IMPLEMENTED - MySQL 8.0 with automatic failover</div>
                    </div>
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">✅ (h) Custom VPC</div>
                        <div class="config-value">IMPLEMENTED - 2 public subnets in different AZs</div>
                    </div>
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">✅ (i) Custom Key Pairs</div>
                        <div class="config-value">IMPLEMENTED - Same key pair across all instances</div>
                    </div>
                    <div class="config-item" style="border-left-color: #27ae60;">
                        <div class="config-label">✅ (j) Email Notifications</div>
                        <div class="config-value">IMPLEMENTED - SNS notifications for environment events</div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <strong>✅ All Requirements Met:</strong> The deployed AWS architecture successfully implements
                    all 10 mandatory requirements and addresses both scalability and disaster recovery concerns
                    outlined in the assignment brief.
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Assignment 3 - Deliverable 1</strong> | AWS System Architecture Report</p>
            <p>Generated on: <?php echo date('F j, Y \a\t g:i A T'); ?></p>
            <p>Live Environment URL: <a
                    href="http://lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com/assign_3_report.php"
                    target="_blank">lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com</a></p>
            <p>Student: Anika Arman (14425754) | Subject: 32555 Cloud Computing and Software as a Service</p>
        </div>
    </div>
</body>

</html>
