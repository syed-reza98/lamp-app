<?php

/**
 * Formal Architecture Diagram for Academic Report
 * Clean, professional diagram suitable for PDF submission
 */

function getFormalArchitectureDiagram()
{
    return '
    <div class="formal-architecture-diagram">
        <!-- Diagram Title -->
        <div class="diagram-title">
            <h3>AWS LAMP Stack Architecture - Multi-AZ Deployment</h3>
            <p class="diagram-subtitle">Scalable, Elastic, and Highly Available Implementation</p>
        </div>

        <!-- Internet Layer -->
        <div class="diagram-layer">
            <div class="layer-title">Internet Layer</div>
            <div class="internet-box">
                <div class="service-label">🌐 Internet Users</div>
                <div class="service-desc">Global Traffic Sources</div>
            </div>
        </div>

        <!-- Flow Arrow -->
        <div class="flow-arrow">⬇️</div>

        <!-- DNS Layer -->
        <div class="diagram-layer">
            <div class="layer-title">DNS Resolution</div>
            <div class="dns-box">
                <div class="service-label">🌍 Amazon Route 53</div>
                <div class="service-desc">DNS Management & Health Checks</div>
            </div>
        </div>

        <!-- Flow Arrow -->
        <div class="flow-arrow">⬇️</div>

        <!-- Application Platform Layer -->
        <div class="diagram-layer">
            <div class="layer-title">Application Platform</div>
            <div class="platform-box">
                <div class="service-label">☁️ AWS Elastic Beanstalk <span class="req-badge">(a)</span></div>
                <div class="service-desc">Platform: PHP 8.1 on Amazon Linux 2</div>
                <div class="service-desc">Environment: lamp-prod-vpc</div>
            </div>
        </div>

        <!-- Flow Arrow -->
        <div class="flow-arrow">⬇️</div>

        <!-- Load Balancer Layer -->
        <div class="diagram-layer">
            <div class="layer-title">Load Distribution</div>
            <div class="loadbalancer-box">
                <div class="service-label">⚖️ Classic Load Balancer <span class="req-badge">(e)</span></div>
                <div class="service-desc">Health Checks: TCP:80 (10s interval)</div>
                <div class="service-desc">Cross-AZ Distribution</div>
            </div>
        </div>

        <!-- Flow Arrow -->
        <div class="flow-arrow">⬇️</div>

        <!-- VPC Container -->
        <div class="vpc-container">
            <div class="vpc-header">
                <div class="vpc-title">🏢 Custom VPC <span class="req-badge">(h)</span></div>
                <div class="vpc-details">vpc-0164bd99719cccfbd | CIDR: 10.0.0.0/16</div>
            </div>

            <!-- Auto Scaling Group -->
            <div class="auto-scaling-section">
                <div class="scaling-header">📊 Auto Scaling Group <span class="req-badge">(f)</span></div>
                <div class="scaling-details">Min: 2 | Max: 8 | Trigger: NetworkOut 60%/30%</div>
            </div>

            <!-- Availability Zones -->
            <div class="az-grid">
                <!-- Availability Zone A -->
                <div class="availability-zone az-a">
                    <div class="az-header">📍 Availability Zone A (us-east-1a)</div>
                    
                    <div class="subnet-container">
                        <div class="subnet-header">🌐 Public Subnet 1 <span class="req-badge">(h)</span></div>
                        <div class="subnet-details">subnet-038f2f355ee2000a5 | 10.0.1.0/24</div>
                        
                        <div class="ec2-instance">
                            <div class="instance-header">🖥️ EC2 Instance <span class="req-badge">(b)</span></div>
                            <div class="instance-details">
                                <div>ID: i-07d65eeddeaab6735</div>
                                <div>Type: t3.micro</div>
                                <div>AMI: Custom LAMP <span class="req-badge">(c)</span></div>
                                <div>Security: Custom SG <span class="req-badge">(d)</span></div>
                                <div>Key: lamp-app-key <span class="req-badge">(i)</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="rds-primary">
                        <div class="rds-header">🗄️ RDS Primary <span class="req-badge">(g)</span></div>
                        <div class="rds-details">
                            <div>MySQL 8.0.41</div>
                            <div>Multi-AZ Enabled</div>
                            <div>20GB SSD Storage</div>
                        </div>
                    </div>
                </div>

                <!-- Availability Zone B -->
                <div class="availability-zone az-b">
                    <div class="az-header">📍 Availability Zone B (us-east-1b)</div>
                    
                    <div class="subnet-container">
                        <div class="subnet-header">🌐 Public Subnet 2 <span class="req-badge">(h)</span></div>
                        <div class="subnet-details">subnet-06f4e63adf671e7ea | 10.0.2.0/24</div>
                        
                        <div class="ec2-instance">
                            <div class="instance-header">🖥️ EC2 Instance <span class="req-badge">(b)</span></div>
                            <div class="instance-details">
                                <div>ID: i-0fdc269d453d60316</div>
                                <div>Type: t3.micro</div>
                                <div>AMI: Custom LAMP <span class="req-badge">(c)</span></div>
                                <div>Security: Custom SG <span class="req-badge">(d)</span></div>
                                <div>Key: lamp-app-key <span class="req-badge">(i)</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="rds-standby">
                        <div class="rds-header">🗄️ RDS Standby <span class="req-badge">(g)</span></div>
                        <div class="rds-details">
                            <div>MySQL 8.0.41</div>
                            <div>Standby Instance</div>
                            <div>Sync Replication</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Connection Lines -->
            <div class="db-connection">
                <div class="connection-line horizontal"></div>
                <div class="connection-label">Database Synchronous Replication</div>
            </div>
        </div>

        <!-- Supporting Services -->
        <div class="supporting-services">
            <div class="support-header">🔧 Supporting AWS Services</div>
            <div class="support-grid">
                <div class="support-item">
                    <div class="support-label">📊 CloudWatch</div>
                    <div class="support-desc">Monitoring & Scaling</div>
                </div>
                <div class="support-item">
                    <div class="support-label">📧 SNS <span class="req-badge">(j)</span></div>
                    <div class="support-desc">Email Notifications</div>
                </div>
                <div class="support-item">
                    <div class="support-label">🔐 IAM</div>
                    <div class="support-desc">Access Management</div>
                </div>
                <div class="support-item">
                    <div class="support-label">🪣 S3</div>
                    <div class="support-desc">Application Storage</div>
                </div>
            </div>
        </div>

        <!-- Requirements Legend -->
        <div class="requirements-legend">
            <div class="legend-header">📋 Assignment Requirements Legend</div>
            <div class="legend-grid">
                <div class="legend-item"><span class="req-badge">(a)</span> AWS Elastic Beanstalk</div>
                <div class="legend-item"><span class="req-badge">(b)</span> Amazon EC2</div>
                <div class="legend-item"><span class="req-badge">(c)</span> Custom AMI</div>
                <div class="legend-item"><span class="req-badge">(d)</span> Custom Security Groups</div>
                <div class="legend-item"><span class="req-badge">(e)</span> Load Balancer</div>
                <div class="legend-item"><span class="req-badge">(f)</span> Auto Scaling (2-8 instances)</div>
                <div class="legend-item"><span class="req-badge">(g)</span> RDS Multi-AZ</div>
                <div class="legend-item"><span class="req-badge">(h)</span> Custom VPC (2+ subnets)</div>
                <div class="legend-item"><span class="req-badge">(i)</span> Custom Key Pairs</div>
                <div class="legend-item"><span class="req-badge">(j)</span> Email Notifications</div>
            </div>
        </div>        <!-- Architecture Benefits Summary -->
        <div class="benefits-summary">
            <div class="benefits-header">🎯 Key Architecture Benefits</div>
            <div class="benefits-content">
                <div class="benefit-item">
                    <strong>Scalability:</strong> Auto-scaling from 2-8 instances based on network traffic
                </div>
                <div class="benefit-item">
                    <strong>High Availability:</strong> Multi-AZ deployment with 99.95% uptime SLA
                </div>
                <div class="benefit-item">
                    <strong>Disaster Recovery:</strong> Automatic failover and cross-zone redundancy
                </div>
                <div class="benefit-item">
                    <strong>Cost Optimization:</strong> Pay-as-you-use with burst-capable instances
                </div>
            </div>
        </div>

        <!-- Data Flow Summary -->
        <div class="data-flow-summary">
            <div class="flow-header">🔄 Data Flow & Traffic Path</div>
            <div class="flow-steps">
                <div class="flow-step">
                    <span class="step-number">1</span>
                    <span class="step-text">User requests → Route 53 DNS resolution</span>
                </div>
                <div class="flow-step">
                    <span class="step-number">2</span>
                    <span class="step-text">DNS → Elastic Beanstalk environment URL</span>
                </div>
                <div class="flow-step">
                    <span class="step-number">3</span>
                    <span class="step-text">Load Balancer distributes traffic across healthy instances</span>
                </div>
                <div class="flow-step">
                    <span class="step-number">4</span>
                    <span class="step-text">EC2 instances process requests using LAMP stack</span>
                </div>
                <div class="flow-step">
                    <span class="step-number">5</span>
                    <span class="step-text">Database queries sent to RDS Multi-AZ primary</span>
                </div>
                <div class="flow-step">
                    <span class="step-number">6</span>
                    <span class="step-text">Auto Scaling monitors and adjusts capacity as needed</span>
                </div>
            </div>
        </div>
    </div>';
}
