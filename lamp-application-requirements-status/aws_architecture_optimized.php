<?php

/**
 * Optimized AWS Architecture Diagram for Assignment 3
 * LAMP Stack Scalable, Elastic, High-Availability Architecture on AWS
 * 
 * Author: Anika Arman | Student ID: 14425754
 * Optimized version with reduced code duplication and improved design
 */

function getEnhancedArchitectureDiagram()
{
    return '
    <div class="architecture-container">
        <div class="architecture-header">
            <h2>🏗️ AWS System Architecture - Assignment 3 Implementation</h2>
            <p><strong>Problem:</strong> Migrate LAMP stack from single desktop PC to scalable, fault-tolerant AWS cloud infrastructure</p>
            <p><strong>Solution:</strong> Multi-AZ, auto-scaling, high-availability architecture with disaster recovery</p>
        </div>

        <div class="architecture-diagram">
            <!-- Internet Layer -->
            <div class="layer internet-layer">
                <div class="layer-header">🌐 INTERNET USERS (Global Traffic)</div>
            </div>
            <div class="connection-line"></div>

            <!-- Route 53 DNS Layer -->
            <div class="layer">
                <div class="service-box route53">
                    <div class="service-header">🌍 Route 53 DNS</div>
                    <div class="service-details">
                        <span>• Global DNS Resolution</span>
                        <span>• Health Checks & Failover</span>
                        <span>• Geographic Routing</span>
                    </div>
                </div>
            </div>
            <div class="connection-line"></div>

            <!-- Elastic Beanstalk Application Layer -->
            <div class="layer">
                <div class="service-box beanstalk">
                    <div class="service-header">
                        ☁️ AWS ELASTIC BEANSTALK <span class="req-tag">(a)</span>
                    </div>
                    <div class="service-details">
                        <span><strong>Application:</strong> lamp-application</span>
                        <span><strong>Environment:</strong> lamp-prod-vpc</span>
                        <span><strong>Platform:</strong> PHP 8.1 on Amazon Linux 2</span>
                        <span><strong>Status:</strong> 🟢 Ready (Green)</span>
                        <span><strong>CNAME:</strong> lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com</span>
                    </div>
                </div>
            </div>
            <div class="connection-line"></div>

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
                            <span><strong>Name:</strong> awseb-e-r-AWSEBLoa-ID4G50DGRVZZ</span>
                            <span><strong>Health Check:</strong> TCP:80 (10s interval)</span>
                            <span><strong>Distribution:</strong> Multi-AZ</span>
                            <span><strong>Status:</strong> Active with healthy instances</span>
                        </div>
                    </div>
                </div>

                <!-- Availability Zones -->
                <div class="az-container">
                    <!-- Availability Zone A -->
                    <div class="availability-zone">
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
                                    <span><strong>ID:</strong> i-0fdc269d453d60316</span>
                                    <span><strong>Type:</strong> t3.micro</span>
                                    <span><strong>AMI:</strong> Custom <span class="req-tag">(c)</span></span>
                                    <span><strong>Security:</strong> sg-041d4877e9ea0c1ae <span class="req-tag">(d)</span></span>
                                    <span><strong>Key:</strong> lamp-app <span class="req-tag">(i)</span></span>
                                    <span><strong>LAMP:</strong> Apache 2.4 + PHP 8.1</span>
                                </div>
                            </div>
                        </div>

                        <!-- RDS Primary -->
                        <div class="rds-primary">
                            <div class="service-header">
                                🗄️ RDS PRIMARY <span class="req-tag">(g)</span>
                            </div>
                            <div class="service-details">
                                <span>MySQL 8.0.41</span>
                                <span>Primary in us-east-1a</span>
                                <span>Multi-AZ Enabled</span>
                            </div>
                        </div>
                    </div>

                    <!-- Availability Zone B -->
                    <div class="availability-zone">
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
                                    <span><strong>ID:</strong> i-07d65eeddeaab6735</span>
                                    <span><strong>Type:</strong> t3.micro</span>
                                    <span><strong>AMI:</strong> Custom <span class="req-tag">(c)</span></span>
                                    <span><strong>Security:</strong> sg-041d4877e9ea0c1ae <span class="req-tag">(d)</span></span>
                                    <span><strong>Key:</strong> lamp-app <span class="req-tag">(i)</span></span>
                                    <span><strong>LAMP:</strong> Apache 2.4 + PHP 8.1</span>
                                </div>
                            </div>
                        </div>

                        <!-- RDS Standby -->
                        <div class="rds-standby">
                            <div class="service-header">
                                🗄️ RDS STANDBY <span class="req-tag">(g)</span>
                            </div>
                            <div class="service-details">
                                <span>MySQL 8.0.41</span>
                                <span>Standby in us-east-1b</span>
                                <span>Synchronous Replication</span>
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
                            <span><strong>Capacity:</strong> Min: 2 | Max: 8 | Current: 2</span>
                            <span><strong>Trigger:</strong> NetworkOut Traffic (6MB up / 2MB down)</span>
                            <span><strong>Policy:</strong> Scale up/down based on thresholds</span>
                            <span><strong>Cooldown:</strong> 360 seconds</span>
                            <span><strong>Health:</strong> EC2 + ELB health checks</span>
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
                    <div class="req-item">
                        <span class="req-letter">(a)</span>
                        <span class="req-name">AWS Elastic Beanstalk</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item">
                        <span class="req-letter">(b)</span>
                        <span class="req-name">Amazon EC2</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item">
                        <span class="req-letter">(c)</span>
                        <span class="req-name">Custom AMI</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item">
                        <span class="req-letter">(d)</span>
                        <span class="req-name">Custom Security Groups</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item">
                        <span class="req-letter">(e)</span>
                        <span class="req-name">Load Balancer</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item">
                        <span class="req-letter">(f)</span>
                        <span class="req-name">Auto Scaling (2-8, Network)</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item">
                        <span class="req-letter">(g)</span>
                        <span class="req-name">RDS Multi-AZ</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item">
                        <span class="req-letter">(h)</span>
                        <span class="req-name">Custom VPC (2 Subnets)</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item">
                        <span class="req-letter">(i)</span>
                        <span class="req-name">Custom Key Pairs</span>
                        <span class="req-status">✅ Implemented</span>
                    </div>
                    <div class="req-item">
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
                    <div class="benefit">
                        <div class="benefit-title">📈 Scalability</div>
                        <div class="benefit-desc">
                            • Auto-scaling 2-8 instances based on network traffic<br>
                            • Elastic Load Balancer distributes traffic<br>
                            • RDS handles database scaling independently
                        </div>
                    </div>
                    <div class="benefit">
                        <div class="benefit-title">🛡️ High Availability</div>
                        <div class="benefit-desc">
                            • Multi-AZ deployment across us-east-1a & us-east-1b<br>
                            • RDS Multi-AZ with automatic failover<br>
                            • Load balancer health checks ensure uptime
                        </div>
                    </div>
                    <div class="benefit">
                        <div class="benefit-title">🔄 Disaster Recovery</div>
                        <div class="benefit-desc">
                            • Automatic instance replacement<br>
                            • Database standby in separate AZ<br>
                            • Consistent AMI ensures quick recovery
                        </div>
                    </div>
                    <div class="benefit">
                        <div class="benefit-title">🔒 Security</div>
                        <div class="benefit-desc">
                            • Custom security groups control access<br>
                            • VPC isolation and private subnets<br>
                            • IAM roles for secure service interaction
                        </div>                    </div>
                </div>
            </div>
        </div>
    </div>';
}
