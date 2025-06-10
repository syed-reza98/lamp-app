<?php

/**
 * Assignment 3 - Formal Academic Report
 * AWS LAMP Stack Architecture Implementation
 * 
 * Student: Anika Arman
 * Student ID: 14425754
 * Email: anika.arman@student.uts.edu.au
 * Subject: 32555 Cloud Computing and Software as a Service
 * Assignment: Assignment 3 - AWS LAMP Application Report
 * 
 * This formal report demonstrates the implementation of all 10 mandatory AWS requirements (a-j)
 * with professional documentation suitable for academic submission.
 */

// Include existing configuration and data
require_once 'includes/helpers.php';

// Environment Configuration
$environment = [
    'rds_hostname' => $_SERVER['RDS_HOSTNAME'] ?? 'localhost',
    'rds_port' => $_SERVER['RDS_PORT'] ?? '3306',
    'rds_db_name' => $_SERVER['RDS_DB_NAME'] ?? 'lampapp',
    'rds_username' => $_SERVER['RDS_USERNAME'] ?? 'root',
    'rds_password' => $_SERVER['RDS_PASSWORD'] ?? '',
];

// Collect AWS Instance Information
$aws_instance = is_array(getAWSInstanceInfo()) ? getAWSInstanceInfo() : []; // Defensive: ensure array

// AWS Services Configuration - All 10 Mandatory Requirements
$aws_requirements = [
    'a' => [
        'service' => 'AWS Elastic Beanstalk',
        'status' => 'IMPLEMENTED',
        'requirement' => 'Requirement (a): AWS Beanstalk',
        'justification' => 'Provides platform-as-a-service for easy application deployment and management without infrastructure complexity. Handles capacity provisioning, load balancing, and health monitoring automatically.',
        'implementation' => 'Application: lamp-application | Environment: lamp-prod-vpc | Platform: 64bit Amazon Linux 2 v3.9.2 running PHP 8.1 | Status: Green (Operational)',
        'supports' => 'Scalability and simplified deployment management'
    ],
    'b' => [
        'service' => 'Amazon EC2',
        'status' => 'IMPLEMENTED',
        'requirement' => 'Requirement (b): Amazon EC2',
        'justification' => 'Provides scalable compute capacity in the cloud. Enables horizontal scaling through multiple instances and supports burst capacity for unpredictable traffic patterns.',
        'implementation' => 'Instance 1: i-07d65eeddeaab6735 (t3.micro, us-east-1a) | Instance 2: i-0fdc269d453d60316 (t3.micro, us-east-1b) | Type: Burstable Performance instances',
        'supports' => 'Elastic compute capacity and cost optimization'
    ],
    'c' => [
        'service' => 'Custom AMI',
        'status' => 'IMPLEMENTED',
        'requirement' => 'Requirement (c): Custom AMI (Amazon Machine Image)',
        'justification' => 'Ensures consistent deployment environment across all instances. Pre-configured with LAMP stack reduces deployment time and ensures standardization.',
        'implementation' => 'Primary AMI: ami-040d931d2f7f2341c (LAMP-Stack-Custom-AMI) | Features: Pre-configured Apache, PHP 8.1, MySQL client, security hardening',
        'supports' => 'Consistent deployment and rapid scaling'
    ],
    'd' => [
        'service' => 'Custom Security Groups',
        'status' => 'IMPLEMENTED',
        'requirement' => 'Requirement (d): Custom Security groups allowing HTTP and SSH requests',
        'justification' => 'Implements network-level security controls. Standardized security groups ensure consistent access policies across all instances while maintaining security best practices.',
        'implementation' => 'Primary SG: sg-041d4877e9ea0c1ae | Rules: HTTP (Port 80), SSH (Port 22) | Applied to all instances consistently',
        'supports' => 'Security standardization and access control'
    ],
    'e' => [
        'service' => 'Load Balancer',
        'status' => 'IMPLEMENTED',
        'requirement' => 'Requirement (e): Load Balancer',
        'justification' => 'Distributes incoming traffic across multiple instances to ensure high availability and fault tolerance. Performs health checks to route traffic only to healthy instances.',
        'implementation' => 'Type: Classic Load Balancer (ELB) | Name: awseb-e-r-AWSEBLoa-ID4G50DGRVZZ | Health Check: TCP:80 (10s interval) | Cross-AZ enabled',
        'supports' => 'High availability and traffic distribution'
    ],
    'f' => [
        'service' => 'Auto Scaling',
        'status' => 'IMPLEMENTED',
        'requirement' => 'Requirement (f): Auto Scaling (min 2, max 8, network triggers)',
        'justification' => 'Automatically adjusts capacity based on demand to handle unpredictable growth. Network-based triggers ensure responsive scaling for traffic variations.',
        'implementation' => 'Min: 2 instances | Max: 8 instances | Trigger: NetworkOut > 60% (scale out), < 30% (scale in) | Cooldown: 360 seconds',
        'supports' => 'Elastic scalability and cost optimization'
    ],
    'g' => [
        'service' => 'RDS Multi-AZ',
        'status' => 'IMPLEMENTED',
        'requirement' => 'Requirement (g): RDS (multi-availability zones deployed)',
        'justification' => 'Provides managed database service with automatic failover for disaster recovery. Multi-AZ deployment ensures database availability during infrastructure failures.',
        'implementation' => 'Primary: lamp-app-db (MySQL 8.0.41) | Multi-AZ: Enabled with automatic failover | Backup: 7 days retention | Storage: 20GB SSD',
        'supports' => 'Disaster recovery and database high availability'
    ],
    'h' => [
        'service' => 'Custom VPC',
        'status' => 'IMPLEMENTED',
        'requirement' => 'Requirement (h): Custom Virtual Private Cloud (VPC) with 2+ subnets in different AZs',
        'justification' => 'Provides isolated network environment with full control over network topology. Multi-AZ subnets enable geographic distribution for fault tolerance.',
        'implementation' => 'VPC: vpc-0164bd99719cccfbd (10.0.0.0/16) | Subnet 1: subnet-038f2f355ee2000a5 (us-east-1a) | Subnet 2: subnet-06f4e63adf671e7ea (us-east-1b)',
        'supports' => 'Network isolation and multi-AZ distribution'
    ],
    'i' => [
        'service' => 'Custom Key Pairs',
        'status' => 'IMPLEMENTED',
        'requirement' => 'Requirement (i): All instances use the same custom key pairs',
        'justification' => 'Ensures consistent and secure SSH access across all instances. Standardized key management simplifies administration and security compliance.',
        'implementation' => 'Primary Key: lamp-app-key (RSA 2048-bit) | Coverage: All EC2 instances use identical key pairs | Security: Private key secured locally',
        'supports' => 'Standardized security and access management'
    ],
    'j' => [
        'service' => 'Email Notifications',
        'status' => 'IMPLEMENTED',
        'requirement' => 'Requirement (j): Set email notifications for important events',
        'justification' => 'Provides proactive monitoring and alerting for critical system events. Enables rapid response to issues affecting system performance or availability.',
        'implementation' => 'SNS Topic: lamp-env-notifications | Email: anika.arman@student.uts.edu.au | Events: Health changes, scaling, deployments',
        'supports' => 'Proactive monitoring and incident response'
    ]
];

// Architecture Benefits addressing assignment concerns
$architecture_benefits = [
    'scalability' => [
        'concern' => 'Scalability Challenge',
        'description' => 'The startup needs to handle unpredictable growth without over-provisioning or under-provisioning resources.',
        'solution' => 'Auto Scaling with Elastic Load Balancer',
        'implementation' => 'Auto Scaling Group (2-8 instances) with network-based triggers (60%/30% thresholds) ensures automatic capacity adjustment. Load balancer distributes traffic efficiently across healthy instances.',
        'benefit' => 'Eliminates capacity planning guesswork while optimizing costs through demand-based scaling.'
    ],
    'disaster_recovery' => [
        'concern' => 'Disaster Recovery Requirements',
        'description' => 'System must maintain high performance and continuous availability even under adverse conditions.',
        'solution' => 'Multi-AZ Deployment with RDS Failover',
        'implementation' => 'RDS Multi-AZ deployment provides automatic database failover. EC2 instances distributed across us-east-1a and us-east-1b availability zones with load balancer health checks.',
        'benefit' => 'Achieves 99.95% uptime SLA with automatic failover capability and cross-zone redundancy.'
    ]
];

// Design assumptions
$design_assumptions = [
    'Traffic is primarily HTTP-based web application traffic',
    'Database workload is typical OLTP (Online Transaction Processing)',
    'Startup operates primarily during business hours with some 24/7 requirements',
    'Cost optimization is important given startup budget constraints',
    'Application can tolerate brief interruptions during scaling events',
    'Geographic distribution limited to single AWS region (us-east-1)',
    'SSL/TLS termination handled at application level initially'
];

// Current timestamp
$current_time = date('Y-m-d H:i:s T');
$formatted_date = date('F j, Y');
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Assignment 3 - AWS LAMP Stack Architecture Report | Anika Arman</title>
        <link rel="stylesheet" href="formal_architecture_styles.css">
        <link rel="stylesheet" href="formal_diagram_styles.css">

        <style>
        /* Formal Academic Report Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #333;
            background: #ffffff;
            font-size: 12pt;
        }

        .report-container {
            max-width: 210mm;
            /* A4 width */
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            min-height: 100vh;
        }

        .report-header {
            text-align: center;
            padding: 40px 30px;
            border-bottom: 3px solid #2c3e50;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .report-title {
            font-size: 24pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-subtitle {
            font-size: 16pt;
            color: #34495e;
            margin-bottom: 20px;
        }

        .student-info {
            background: white;
            border: 2px solid #3498db;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            display: inline-block;
        }

        .student-info table {
            border-collapse: collapse;
        }

        .student-info td {
            padding: 5px 15px;
            border-bottom: 1px solid #ecf0f1;
        }

        .student-info td:first-child {
            font-weight: bold;
            color: #2c3e50;
        }

        .report-content {
            padding: 30px;
        }

        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 18pt;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .subsection-title {
            font-size: 14pt;
            color: #34495e;
            margin: 20px 0 10px 0;
            font-weight: bold;
        }

        .requirement-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin: 20px 0;
        }

        .requirement-card {
            border: 1px solid #bdc3c7;
            border-radius: 8px;
            padding: 20px;
            background: #f8f9fa;
            page-break-inside: avoid;
        }

        .requirement-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .requirement-letter {
            background: #3498db;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }

        .requirement-title {
            font-size: 14pt;
            font-weight: bold;
            color: #2c3e50;
        }

        .status-implemented {
            background: #27ae60;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10pt;
            margin-left: auto;
        }

        .detail-section {
            margin: 10px 0;
        }

        .detail-label {
            font-weight: bold;
            color: #34495e;
            margin-bottom: 5px;
        }

        .detail-content {
            margin-left: 20px;
            color: #555;
        }

        .architecture-diagram {
            background: white;
            border: 2px solid #34495e;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            page-break-inside: avoid;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin: 20px 0;
        }

        .benefit-card {
            border-left: 4px solid #e74c3c;
            background: #fdfdfd;
            padding: 20px;
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .benefit-title {
            font-size: 14pt;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .assumptions-list {
            list-style-type: none;
            padding-left: 0;
        }

        .assumptions-list li {
            padding: 8px 0;
            border-bottom: 1px solid #ecf0f1;
            position: relative;
            padding-left: 25px;
        }

        .assumptions-list li:before {
            content: "▶";
            color: #3498db;
            position: absolute;
            left: 0;
        }

        .services-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }

        .service-item {
            background: #3498db;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }

        .page-break {
            page-break-before: always;
        }

        /* Print Styles */
        @media print {
            body {
                font-size: 11pt;
                color: black;
            }

            .report-container {
                box-shadow: none;
                max-width: none;
                margin: 0;
            }

            .page-break {
                page-break-before: always;
            }

            button {
                display: none !important;
            }

            .report-header {
                background: white !important;
                border-bottom: 3px solid black !important;
            }

            .student-info {
                border: 2px solid black !important;
            }

            .section-title {
                border-bottom: 2px solid black !important;
            }

            .requirement-card,
            .benefit-card {
                border: 1px solid black !important;
                background: white !important;
                page-break-inside: avoid;
            }

            .architecture-diagram {
                border: 2px solid black !important;
                background: white !important;
                page-break-inside: avoid;
            }
        }

        @media screen and (max-width: 768px) {
            .report-container {
                margin: 0;
            }

            .report-content {
                padding: 20px;
            }

            .report-title {
                font-size: 20pt;
            }
        }
        </style>
    </head>

    <body>
        <div class="report-container">
            <!-- Report Header -->
            <div class="report-header">
                <h1 class="report-title">Assignment 3 - System Architecture Report</h1>
                <h2 class="report-subtitle">AWS LAMP Stack Implementation</h2>
                <h3 style="color: #7f8c8d; margin-bottom: 10px;">Scalable, Elastic, Highly Available Architecture</h3>
                <!-- Print Button -->
                <div style="margin: 15px 0;">
                    <button onclick="window.print()"
                        style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; margin: 5px;">
                        🖨️ Print Report (PDF)
                    </button>
                    <button onclick="window.open('architecture_diagram_standalone.php', '_blank')"
                        style="background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; margin: 5px;">
                        📊 View Architecture Diagram (Fullscreen)
                    </button>
                </div>

                <div class="student-info">
                    <table>
                        <tr>
                            <td>Student:</td>
                            <td>Anika Arman</td>
                        </tr>
                        <tr>
                            <td>Student ID:</td>
                            <td>14425754</td>
                        </tr>
                        <tr>
                            <td>Email:</td>
                            <td>anika.arman@student.uts.edu.au</td>
                        </tr>
                        <tr>
                            <td>Subject:</td>
                            <td>32555 Cloud Computing and Software as a Service</td>
                        </tr>
                        <tr>
                            <td>Assignment:</td>
                            <td>Assignment 3 - AWS Application Development</td>
                        </tr>
                        <tr>
                            <td>Report Date:</td>
                            <td><?php echo $formatted_date; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="report-content">
                <!-- Executive Summary -->
                <div class="section">
                    <h2 class="section-title">1. Executive Summary</h2>
                    <p>This report presents the design and implementation of a scalable, elastic, highly available, and
                        fault-tolerant AWS LAMP stack architecture for a growing startup. The solution addresses the
                        critical challenges of unpredictable growth and disaster recovery requirements while
                        implementing all 10 mandatory AWS services as specified in the assignment requirements.</p>

                    <p>The implemented architecture successfully transitions the startup from a single desktop PC LAMP
                        stack to a robust cloud infrastructure capable of handling rapid, unpredictable growth while
                        maintaining high availability and cost optimization.</p>
                </div>

                <!-- Problem Statement -->
                <div class="section">
                    <h2 class="section-title">2. Problem Statement & Requirements</h2>

                    <h3 class="subsection-title">2.1 Current State</h3>
                    <p>The startup currently operates a LAMP stack (MySQL, Apache, and PHP) on a single desktop PC in a
                        small office. This setup presents significant limitations:</p>
                    <ul style="margin: 10px 0 10px 30px;">
                        <li>Single point of failure</li>
                        <li>Limited scalability for growth</li>
                        <li>No disaster recovery capabilities</li>
                        <li>Resource constraints during peak usage</li>
                    </ul>

                    <h3 class="subsection-title">2.2 Business Requirements</h3>
                    <div class="benefits-grid">
                        <div class="benefit-card">
                            <div class="benefit-title">Scalability Requirement</div>
                            <p><strong>Challenge:</strong>
                                <?php echo $architecture_benefits['scalability']['description']; ?></p>
                            <p><strong>Solution:</strong>
                                <?php echo $architecture_benefits['scalability']['solution']; ?></p>
                        </div>
                        <div class="benefit-card">
                            <div class="benefit-title">Disaster Recovery Requirement</div>
                            <p><strong>Challenge:</strong>
                                <?php echo $architecture_benefits['disaster_recovery']['description']; ?></p>
                            <p><strong>Solution:</strong>
                                <?php echo $architecture_benefits['disaster_recovery']['solution']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- AWS Services Implementation -->
                <div class="section page-break">
                    <h2 class="section-title">3. AWS Services Implementation</h2>
                    <p>The following section details the implementation of all 10 mandatory AWS services with
                        justification for each component:</p>

                    <div class="requirement-grid">
                        <?php foreach ($aws_requirements as $letter => $requirement): ?>
                        <div class="requirement-card">
                            <div class="requirement-header">
                                <div class="requirement-letter"><?php echo strtoupper($letter); ?></div>
                                <div class="requirement-title"><?php echo $requirement['service']; ?></div>
                                <div class="status-implemented"><?php echo $requirement['status']; ?></div>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Requirement:</div>
                                <div class="detail-content"><?php echo $requirement['requirement']; ?></div>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Justification:</div>
                                <div class="detail-content"><?php echo $requirement['justification']; ?></div>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Implementation Details:</div>
                                <div class="detail-content"><?php echo $requirement['implementation']; ?></div>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Supports:</div>
                                <div class="detail-content"><?php echo $requirement['supports']; ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div> <!-- Architecture Diagram -->
                <div class="section page-break">
                    <h2 class="section-title">4. System Architecture Diagram</h2>

                    <div
                        style="text-align: center; margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                        <p style="margin: 10px 0; color: #495057;"><strong>💡 Tip:</strong> For a larger view suitable
                            for detailed analysis, <a href="architecture_diagram_standalone.php" target="_blank"
                                style="color: #007bff; text-decoration: none; font-weight: bold;">click here to view the
                                diagram in fullscreen</a></p>
                    </div>

                    <div class="architecture-diagram">
                        <h3 style="color: #2c3e50; margin-bottom: 20px;">AWS LAMP Stack Architecture</h3>
                        <?php
                    // Include the formal architecture diagram
                    if (file_exists('formal_architecture_diagram.php')) {
                        require_once 'formal_architecture_diagram.php';
                        echo getFormalArchitectureDiagram();
                    } else {
                        echo '<div style="padding: 40px; border: 2px dashed #bdc3c7; color: #7f8c8d;">
                                <p>Formal architecture diagram component not found.</p>
                                <p>Please ensure formal_architecture_diagram.php is available.</p>
                              </div>';
                    }
                    ?>
                    </div>
                </div>

                <!-- Architecture Benefits -->
                <div class="section">
                    <h2 class="section-title">5. Solution Benefits & Implementation</h2>

                    <h3 class="subsection-title">5.1 Scalability Solution</h3>
                    <p><strong>Implementation:</strong>
                        <?php echo $architecture_benefits['scalability']['implementation']; ?></p>
                    <p><strong>Benefit:</strong> <?php echo $architecture_benefits['scalability']['benefit']; ?></p>

                    <h3 class="subsection-title">5.2 Disaster Recovery Solution</h3>
                    <p><strong>Implementation:</strong>
                        <?php echo $architecture_benefits['disaster_recovery']['implementation']; ?></p>
                    <p><strong>Benefit:</strong> <?php echo $architecture_benefits['disaster_recovery']['benefit']; ?>
                    </p>

                    <h3 class="subsection-title">5.3 Additional Benefits</h3>
                    <ul style="margin: 10px 0 10px 30px;">
                        <li><strong>Cost Optimization:</strong> Pay-as-you-use model with t3.micro instances and
                            demand-based scaling</li>
                        <li><strong>Security:</strong> VPC isolation, security groups, and standardized access controls
                        </li>
                        <li><strong>Monitoring:</strong> Comprehensive health checks and email notifications for
                            proactive management</li>
                        <li><strong>Maintenance:</strong> Managed services reduce operational overhead</li>
                    </ul>
                </div>

                <!-- Design Assumptions -->
                <div class="section">
                    <h2 class="section-title">6. Design Assumptions</h2>
                    <p>The following assumptions were made during the design process:</p>

                    <ul class="assumptions-list">
                        <?php foreach ($design_assumptions as $assumption): ?>
                        <li><?php echo $assumption; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- AWS Services Used -->
                <div class="section">
                    <h2 class="section-title">7. Complete List of AWS Services Used</h2>

                    <h3 class="subsection-title">7.1 Core Services (Mandatory Requirements)</h3>
                    <div class="services-list">
                        <div class="service-item">AWS Elastic Beanstalk</div>
                        <div class="service-item">Amazon EC2</div>
                        <div class="service-item">Custom AMI</div>
                        <div class="service-item">Security Groups</div>
                        <div class="service-item">Load Balancer (ELB)</div>
                        <div class="service-item">Auto Scaling</div>
                        <div class="service-item">RDS Multi-AZ</div>
                        <div class="service-item">Custom VPC</div>
                        <div class="service-item">Key Pairs</div>
                        <div class="service-item">SNS Notifications</div>
                    </div>

                    <h3 class="subsection-title">7.2 Supporting Services</h3>
                    <div class="services-list">
                        <div class="service-item">Amazon CloudWatch</div>
                        <div class="service-item">AWS IAM</div>
                        <div class="service-item">Amazon S3</div>
                        <div class="service-item">Amazon Route 53</div>
                    </div>
                </div>

                <!-- Implementation Status -->
                <div class="section">
                    <h2 class="section-title">8. Implementation Status & Verification</h2>

                    <p><strong>Deployment Environment:</strong> AWS Account (Live Implementation)</p>
                    <p><strong>Implementation Status:</strong> All 10 mandatory requirements successfully implemented
                        and operational</p>
                    <p><strong>Verification URL:</strong>
                        <code>http://lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com/</code>
                    </p>

                    <h3 class="subsection-title">8.1 Current System Status</h3>
                    <ul style="margin: 10px 0 10px 30px;">
                        <li>✅ Elastic Beanstalk Environment: Green (Healthy)</li>
                        <li>✅ EC2 Instances: 2 active instances across multiple AZs</li>
                        <li>✅ RDS Database: Multi-AZ deployment operational</li>
                        <li>✅ Load Balancer: Active with health checks passing</li>
                        <li>✅ Auto Scaling: Configured with network-based triggers</li>
                        <li>✅ Email Notifications: Active and confirmed</li>
                    </ul>
                </div>

                <!-- Conclusion -->
                <div class="section">
                    <h2 class="section-title">9. Conclusion</h2>
                    <p>The implemented AWS LAMP stack architecture successfully addresses the startup's scalability and
                        disaster recovery requirements while meeting all 10 mandatory AWS service requirements. The
                        solution provides:</p>

                    <ul style="margin: 10px 0 10px 30px;">
                        <li><strong>Elastic Scalability:</strong> Automatic scaling from 2 to 8 instances based on
                            network traffic</li>
                        <li><strong>High Availability:</strong> Multi-AZ deployment with 99.95% uptime SLA</li>
                        <li><strong>Disaster Recovery:</strong> Automatic failover capabilities and cross-zone
                            redundancy</li>
                        <li><strong>Cost Optimization:</strong> Pay-as-you-use model with efficient resource utilization
                        </li>
                        <li><strong>Operational Excellence:</strong> Managed services with comprehensive monitoring</li>
                    </ul>

                    <p>This architecture positions the startup for sustainable growth while maintaining cost efficiency
                        and operational resilience.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p><strong>Assignment 3 - AWS LAMP Stack Architecture Report</strong></p>
                <p>Student: Anika Arman (14425754) | Subject: 32555 Cloud Computing and Software as a Service</p>
                <p>University of Technology Sydney | Faculty of Engineering and Information Technology</p>
                <p>Report Generated: <?php echo $current_time; ?></p>
            </div>
        </div>

        <script>
        // Print functionality for PDF generation
        document.addEventListener('DOMContentLoaded', function() {
            // Add print button functionality
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    window.print();
                }
            });
        });
        </script>
    </body>

</html>