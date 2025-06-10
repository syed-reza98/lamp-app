<?php

/**
 * Enhanced AWS Architecture Diagram for Assignment 3
 * LAMP Stack Scalable, Elastic, High-Availability Architecture on AWS
 * 
 * All 10 Mandatory Requirements (a-j) Visualized
 * 
 * Author: Anika Arman
 * Student ID: 14425754
 * Assignment: 32555 Cloud Computing - Assignment 3
 */

function getEnhancedArchitectureDiagram()
{
    return '
    <div class="architecture-container">
        <div class="architecture-header">
            <h2>🏗️ AWS System Architecture - Assignment 3 Requirements Implementation</h2>
            <p><strong>Problem:</strong> Migrate LAMP stack from single desktop PC to scalable, fault-tolerant AWS cloud infrastructure</p>
            <p><strong>Solution:</strong> Multi-AZ, auto-scaling, high-availability architecture addressing scalability and disaster recovery concerns</p>
        </div>

        <div class="architecture-diagram">
            <!-- Internet Layer -->
            <div class="layer internet-layer">
                <div class="layer-header">🌐 INTERNET USERS (Global Traffic)</div>
                <div class="connection-line down"></div>
            </div>

            <!-- Route 53 DNS Layer -->
            <div class="layer dns-layer">
                <div class="service-box route53">
                    <div class="service-header">🌍 Route 53 DNS</div>
                    <div class="service-details">
                        <span class="feature">• Global DNS Resolution</span>
                        <span class="feature">• Health Checks & Failover</span>
                        <span class="feature">• Geographic Routing</span>
                    </div>
                </div>
                <div class="connection-line down"></div>
            </div>

            <!-- Elastic Beanstalk Application Layer -->
            <div class="layer beanstalk-layer">
                <div class="service-box beanstalk">
                    <div class="service-header">
                        ☁️ AWS ELASTIC BEANSTALK <span class="req-tag">(a)</span>
                    </div>
                    <div class="service-details">
                        <span class="app-name">Application: lamp-application</span>
                        <span class="env-name">Environment: lamp-prod-vpc</span>
                        <span class="platform">Platform: PHP 8.1 on Amazon Linux 2</span>
                        <span class="status">Status: 🟢 Ready (Green)</span>
                        <span class="cname">CNAME: lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com</span>
                    </div>
                </div>
                <div class="connection-line down"></div>
            </div>

            <!-- VPC Container -->
            <div class="vpc-container">
                <div class="vpc-header">
                    🏢 CUSTOM VPC - vpc-0164bd99719cccfbd <span class="req-tag">(h)</span>
                    <span class="cidr">CIDR: 10.0.0.0/16</span>
                </div>

                <!-- Load Balancer -->
                <div class="load-balancer-section">
                    <div class="service-box load-balancer">
                        <div class="service-header">
                            ⚖️ CLASSIC LOAD BALANCER <span class="req-tag">(e)</span>
                        </div>
                        <div class="service-details">
                            <span class="feature">Name: awseb-e-r-AWSEBLoa-ID4G50DGRVZZ</span>
                            <span class="feature">Health Check: /health.php</span>
                            <span class="feature">Multi-AZ Distribution</span>
                            <span class="feature">SSL Termination</span>
                        </div>
                    </div>
                </div>

                <!-- Availability Zones -->
                <div class="az-container">
                    <!-- Availability Zone A -->
                    <div class="availability-zone az-a">
                        <div class="az-header">📍 AVAILABILITY ZONE us-east-1a</div>
                        
                        <div class="subnet">
                            <div class="subnet-header">
                                🌐 PUBLIC SUBNET 1 <span class="req-tag">(h)</span>
                                <span class="subnet-cidr">subnet-038f2f355ee2000a5 | CIDR: 10.0.1.0/24</span>
                            </div>
                            
                            <div class="ec2-instance">
                                <div class="instance-header">
                                    🖥️ EC2 INSTANCE <span class="req-tag">(b)</span>
                                </div>
                                <div class="instance-details">
                                    <span class="instance-id">Instance ID: i-0fdc269d453d60316</span>
                                    <span class="instance-type">Type: t3.micro</span>
                                    <span class="ami">Custom AMI <span class="req-tag">(c)</span></span>
                                    <span class="security">Security Groups <span class="req-tag">(d)</span></span>
                                    <span class="keypair">Key Pair: lamp-app <span class="req-tag">(i)</span></span>
                                    <span class="private-ip">Private IP: 10.0.1.90</span>
                                    <span class="lamp-stack">🔧 LAMP Stack: Apache 2.4 + PHP 8.1</span>
                                </div>
                            </div>
                        </div>

                        <!-- RDS Primary -->
                        <div class="rds-primary">
                            <div class="service-header">
                                🗄️ RDS PRIMARY <span class="req-tag">(g)</span>
                            </div>
                            <div class="service-details">
                                <span class="feature">MySQL 8.0.41</span>
                                <span class="feature">Primary in us-east-1a</span>
                                <span class="feature">Multi-AZ Enabled</span>
                            </div>
                        </div>
                    </div>

                    <!-- Availability Zone B -->
                    <div class="availability-zone az-b">
                        <div class="az-header">📍 AVAILABILITY ZONE us-east-1b</div>
                        
                        <div class="subnet">
                            <div class="subnet-header">
                                🌐 PUBLIC SUBNET 2 <span class="req-tag">(h)</span>
                                <span class="subnet-cidr">subnet-06f4e63adf671e7ea | CIDR: 10.0.2.0/24</span>
                            </div>
                            
                            <div class="ec2-instance">
                                <div class="instance-header">
                                    🖥️ EC2 INSTANCE <span class="req-tag">(b)</span>
                                </div>
                                <div class="instance-details">
                                    <span class="instance-id">Instance ID: i-080ad03352fac537f</span>
                                    <span class="instance-type">Type: t3.micro</span>
                                    <span class="ami">Custom AMI <span class="req-tag">(c)</span></span>
                                    <span class="security">Security Groups <span class="req-tag">(d)</span></span>
                                    <span class="keypair">Key Pair: lamp-app <span class="req-tag">(i)</span></span>
                                    <span class="private-ip">Private IP: 10.0.2.10</span>
                                    <span class="lamp-stack">🔧 LAMP Stack: Apache 2.4 + PHP 8.1</span>
                                </div>
                            </div>
                        </div>

                        <!-- RDS Standby -->
                        <div class="rds-standby">
                            <div class="service-header">
                                🗄️ RDS STANDBY <span class="req-tag">(g)</span>
                            </div>
                            <div class="service-details">
                                <span class="feature">MySQL 8.0.41</span>
                                <span class="feature">Standby in us-east-1b</span>
                                <span class="feature">Synchronous Replication</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auto Scaling Group -->
                <div class="auto-scaling-section">
                    <div class="service-box auto-scaling">
                        <div class="service-header">
                            📊 AUTO SCALING GROUP <span class="req-tag">(f)</span>
                        </div>
                        <div class="service-details">
                            <span class="capacity">Min: 2 | Max: 8 | Current: 2</span>
                            <span class="metric">Scaling Metric: Network Output Traffic</span>
                            <span class="thresholds">Upper: 60% | Lower: 30%</span>
                            <span class="policies">Scale Up/Down Policies Configured</span>
                            <span class="asg-name">awseb-e-rpyapuixkj-stack-AWSEBAutoScalingGroup</span>
                        </div>
                    </div>
                </div>

                <!-- RDS Multi-AZ Section -->
                <div class="rds-section">
                    <div class="service-box rds-multi-az">
                        <div class="service-header">
                            🗄️ RDS MYSQL MULTI-AZ <span class="req-tag">(g)</span>
                        </div>
                        <div class="service-details">
                            <span class="db-identifier">DB Identifier: lamp-app-db</span>
                            <span class="engine">Engine: MySQL 8.0.41</span>
                            <span class="endpoint">Endpoint: lamp-app-db.cijnrr1efnu0.us-east-1.rds.amazonaws.com</span>
                            <span class="backup">Automated Backups: 7 days</span>
                            <span class="failover">Automatic Failover: ✅ Enabled</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supporting Services -->
            <div class="supporting-services">
                <div class="support-header">🔧 Supporting AWS Services</div>
                <div class="support-grid">
                    <div class="support-service cloudwatch">
                        <div class="support-title">📊 CloudWatch</div>
                        <div class="support-desc">Monitoring & Auto-scaling Triggers</div>
                    </div>
                    <div class="support-service sns">
                        <div class="support-title">📧 SNS <span class="req-tag">(j)</span></div>
                        <div class="support-desc">Email Notifications</div>
                    </div>
                    <div class="support-service iam">
                        <div class="support-title">🔐 IAM</div>
                        <div class="support-desc">Service Roles & Policies</div>
                    </div>
                    <div class="support-service s3">
                        <div class="support-title">🪣 S3</div>
                        <div class="support-desc">Application Versions</div>
                    </div>
                </div>
            </div>

            <!-- Requirements Summary -->
            <div class="requirements-summary">
                <div class="req-header">✅ Assignment Requirements Status</div>
                <div class="req-grid">
                    <div class="req-item completed">
                        <span class="req-letter">(a)</span>
                        <span class="req-name">AWS Elastic Beanstalk</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item completed">
                        <span class="req-letter">(b)</span>
                        <span class="req-name">Amazon EC2</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item completed">
                        <span class="req-letter">(c)</span>
                        <span class="req-name">Custom AMI</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item completed">
                        <span class="req-letter">(d)</span>
                        <span class="req-name">Custom Security Groups</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item completed">
                        <span class="req-letter">(e)</span>
                        <span class="req-name">Load Balancer</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item completed">
                        <span class="req-letter">(f)</span>
                        <span class="req-name">Auto Scaling (2-8, Network Traffic)</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item completed">
                        <span class="req-letter">(g)</span>
                        <span class="req-name">RDS Multi-AZ</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item completed">
                        <span class="req-letter">(h)</span>
                        <span class="req-name">Custom VPC (2 Public Subnets)</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item completed">
                        <span class="req-letter">(i)</span>
                        <span class="req-name">Custom Key Pairs (Same for All)</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item completed">
                        <span class="req-letter">(j)</span>
                        <span class="req-name">Email Notifications</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                </div>
            </div>

            <!-- Architecture Benefits -->
            <div class="benefits-section">
                <div class="benefits-header">🎯 Architecture Benefits</div>
                <div class="benefits-grid">
                    <div class="benefit scalability">
                        <div class="benefit-title">📈 Scalability</div>
                        <div class="benefit-desc">
                            • Auto-scaling 2-8 instances based on network traffic<br>
                            • Elastic Load Balancer distributes traffic<br>
                            • RDS handles database scaling independently
                        </div>
                    </div>
                    <div class="benefit availability">
                        <div class="benefit-title">🛡️ High Availability</div>
                        <div class="benefit-desc">
                            • Multi-AZ deployment across us-east-1a & us-east-1b<br>
                            • RDS Multi-AZ with automatic failover<br>
                            • Load balancer health checks ensure uptime
                        </div>
                    </div>
                    <div class="benefit recovery">
                        <div class="benefit-title">🔄 Disaster Recovery</div>
                        <div class="benefit-desc">
                            • Automatic instance replacement<br>
                            • Database standby in separate AZ<br>
                            • Consistent AMI ensures quick recovery
                        </div>
                    </div>
                    <div class="benefit security">
                        <div class="benefit-title">🔒 Security</div>
                        <div class="benefit-desc">
                            • Custom security groups control access<br>
                            • VPC isolation and private subnets<br>
                            • IAM roles for secure service interaction
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

function getEnhancedArchitectureCSS()
{
    return '
        .architecture-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .architecture-header {
            text-align: center;
            margin-bottom: 30px;
            background: rgba(255,255,255,0.95);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .architecture-header h2 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .architecture-header p {
            color: #34495e;
            font-size: 16px;
            margin: 8px 0;
            line-height: 1.6;
        }

        .architecture-diagram {
            background: rgba(255,255,255,0.98);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .layer {
            margin-bottom: 25px;
            text-align: center;
        }

        .internet-layer {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 20px;
            border-radius: 12px;
            color: white;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
        }

        .dns-layer {
            margin: 20px 0;
        }

        .beanstalk-layer {
            margin: 25px 0;
        }

        .service-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin: 15px auto;
            max-width: 600px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-left: 5px solid;
        }

        .route53 {
            border-left-color: #ff9500;
            background: linear-gradient(135deg, #fff 0%, #fff5e6 100%);
        }

        .beanstalk {
            border-left-color: #ff6b35;
            background: linear-gradient(135deg, #fff 0%, #fff2f0 100%);
        }

        .load-balancer {
            border-left-color: #28a745;
            background: linear-gradient(135deg, #fff 0%, #f0fff4 100%);
        }

        .auto-scaling {
            border-left-color: #17a2b8;
            background: linear-gradient(135deg, #fff 0%, #e6f9fc 100%);
        }

        .rds-multi-az {
            border-left-color: #6f42c1;
            background: linear-gradient(135deg, #fff 0%, #f8f0ff 100%);
        }

        .service-header {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .service-details {
            display: flex;
            flex-direction: column;
            gap: 5px;
            font-size: 14px;
            color: #5a6c7d;
        }

        .req-tag {
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 8px;
        }

        .vpc-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 3px dashed #6c757d;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            position: relative;
        }

        .vpc-header {
            font-size: 20px;
            font-weight: bold;
            color: #495057;
            text-align: center;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .cidr {
            display: block;
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }

        .load-balancer-section {
            margin-bottom: 25px;
        }

        .az-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin: 25px 0;
        }

        .availability-zone {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            position: relative;
        }

        .az-header {
            font-size: 16px;
            font-weight: bold;
            color: #495057;
            text-align: center;
            margin-bottom: 15px;
            background: #e9ecef;
            padding: 10px;
            border-radius: 8px;
        }

        .subnet {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .subnet-header {
            font-size: 14px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
        }

        .subnet-cidr {
            display: block;
            font-size: 12px;
            color: #6c757d;
            margin-top: 3px;
        }

        .ec2-instance {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 12px;
            margin-top: 10px;
        }

        .instance-header {
            font-size: 14px;
            font-weight: bold;
            color: #856404;
            margin-bottom: 8px;
        }

        .instance-details {
            display: flex;
            flex-direction: column;
            gap: 3px;
            font-size: 12px;
            color: #856404;
        }

        .rds-primary, .rds-standby {
            background: linear-gradient(135deg, #d1ecf1 0%, #b8daff 100%);
            border: 1px solid #b8daff;
            border-radius: 8px;
            padding: 12px;
            margin-top: 10px;
        }

        .auto-scaling-section {
            margin: 25px 0;
            text-align: center;
        }

        .rds-section {
            margin: 25px 0;
            text-align: center;
        }

        .supporting-services {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
        }

        .support-header {
            font-size: 20px;
            font-weight: bold;
            color: #495057;
            text-align: center;
            margin-bottom: 20px;
        }

        .support-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .support-service {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }

        .cloudwatch { border-left-color: #ff9500; }
        .sns { border-left-color: #e74c3c; }
        .iam { border-left-color: #f39c12; }
        .s3 { border-left-color: #27ae60; }

        .support-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .support-desc {
            font-size: 12px;
            color: #7f8c8d;
        }

        .requirements-summary {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            color: white;
        }

        .req-header {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .req-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 12px;
        }

        .req-item {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .req-letter {
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .req-name {
            flex: 1;
            font-size: 14px;
        }

        .req-status {
            font-size: 12px;
            font-weight: bold;
        }

        .benefits-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            color: white;
        }

        .benefits-header {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .benefit {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 20px;
        }

        .benefit-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .benefit-desc {
            font-size: 14px;
            line-height: 1.6;
            opacity: 0.9;
        }

        .connection-line {
            width: 4px;
            height: 30px;
            background: linear-gradient(to bottom, #3498db, #2ecc71);
            margin: 15px auto;
            border-radius: 2px;
        }

        .down::after {
            content: "↓";
            display: block;
            text-align: center;
            color: #3498db;
            font-size: 20px;
            font-weight: bold;
            margin-top: 5px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .az-container {
                grid-template-columns: 1fr;
            }
            
            .req-grid {
                grid-template-columns: 1fr;
            }
            
            .benefits-grid {
                grid-template-columns: 1fr;
            }
            
            .support-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }

        /* Animation */
        .service-box {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .service-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.15);
        }

        .req-item, .benefit, .support-service {
            transition: transform 0.2s ease;
        }

        .req-item:hover, .benefit:hover, .support-service:hover {
            transform: translateY(-1px);        }
    </style>';
}