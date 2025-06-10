# AWS LAMP Stack Application - Assignment 3

**Student:** Anika Arman
**Student ID:** 14425754
**Email:** anika.arman@student.uts.edu.au
**Subject:** 32555 Cloud Computing and Software as a Service
**University:** University of Technology Sydney

## 📋 Project Overview

This project demonstrates a comprehensive implementation of a scalable, elastic, highly available, and fault-tolerant LAMP stack architecture on AWS. The solution addresses the startup's concerns about rapid growth and disaster recovery by implementing all 10 mandatory AWS requirements.

## 🎯 Assignment Requirements Fulfilled

### ✅ All 10 Mandatory AWS Services Implemented:

- **(a) AWS Elastic Beanstalk** - Application platform management
- **(b) Amazon EC2** - Compute instances with auto scaling
- **(c) Custom AMI** - Optimized LAMP stack image
- **(d) Custom Security Groups** - HTTP, HTTPS, and SSH access
- **(e) Load Balancer** - Application Load Balancer for traffic distribution
- **(f) Auto Scaling** - 2-8 instances with network-based triggers (60%/30%)
- **(g) RDS Multi-AZ** - MySQL database with automatic failover
- **(h) Custom VPC** - Multi-AZ public subnets
- **(i) Custom Key Pairs** - Consistent SSH access across instances
- **(j) Email Notifications** - SNS notifications for critical events

## 🏗️ Architecture Benefits

### Scalability
- **Horizontal Scaling:** Auto Scaling Group adjusts capacity based on network traffic
- **Elastic Resources:** Dynamic resource allocation prevents over/under-provisioning
- **Load Distribution:** Application Load Balancer distributes traffic across healthy instances

### High Availability
- **Multi-AZ Deployment:** Resources distributed across multiple Availability Zones
- **Database Failover:** RDS Multi-AZ provides automatic database failover
- **Instance Redundancy:** Minimum 2 instances ensure service continuity

### Fault Tolerance
- **Automatic Recovery:** Failed instances are automatically replaced
- **Health Monitoring:** Continuous health checks ensure service quality
- **Database Replication:** Synchronous replication to standby database

### Disaster Recovery
- **Cross-AZ Redundancy:** Resources replicated across different data centers
- **Automated Backups:** RDS automated backups and point-in-time recovery
- **Infrastructure as Code:** Reproducible deployments through Elastic Beanstalk

## 📁 Application Structure

```
lamp-application-requirements-status/
├── lamp_report.php          # Main comprehensive report (Primary Interface)
├── navigation.php           # Navigation dashboard
├── enhanced_health.php      # Health check endpoint for load balancer
├── api.php                 # RESTful API endpoints
├── init_database.php       # Database initialization script
├── fresh_styles.css        # Modern responsive CSS styles
├── index.php              # Entry point (redirects to main report)
├── health.php             # Basic health check
├── phpinfo.php            # PHP configuration info
├── environment.config     # Elastic Beanstalk configuration
└── README.md              # This documentation
```

## 🚀 Features

### 1. Comprehensive System Report (`lamp_report.php`)
- **Live System Status:** Real-time health monitoring
- **AWS Requirements Display:** All 10 mandatory services with implementation details
- **Architecture Benefits:** Detailed explanation of scalability and disaster recovery
- **Performance Metrics:** Memory usage, response times, system load
- **Database Information:** Connection status, performance metrics
- **Visual Architecture Diagram:** Interactive AWS services visualization

### 2. Enhanced Health Monitoring (`enhanced_health.php`)
- **Multi-Check System:** PHP, Database, Filesystem, Memory, Load Average
- **JSON Response Format:** Structured health data for monitoring systems
- **Response Time Tracking:** Performance metrics for each check
- **AWS Instance Metadata:** Dynamic instance information
- **Load Balancer Compatible:** Proper HTTP status codes for health checks

### 3. RESTful API (`api.php`)
- **GET /status** - System status information
- **GET /logs** - Application logs with filtering
- **POST /logs** - Create log entries
- **GET /metrics** - Performance metrics
- **POST /metrics** - Record custom metrics
- **GET /users** - User management
- **POST /users** - Create new users
- **GET /health** - Detailed health information

### 4. Database Management (`init_database.php`)
- **Table Creation:** Application logs, health checks, metrics, users, deployments
- **Sample Data:** Pre-populated data for demonstration
- **Data Verification:** Confirms successful initialization
- **Error Handling:** Comprehensive error reporting

## 🌐 Live Deployment

**Primary URL:** http://lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com/

### Quick Access Links:
- **Main Report:** `/lamp_report.php`
- **Navigation:** `/navigation.php`
- **Health Check:** `/enhanced_health.php`
- **API Documentation:** `/api.php`
- **Database Init:** `/init_database.php`

## 🛠️ Technical Specifications

### Infrastructure Configuration
- **Platform:** 64bit Amazon Linux 2 v3.9.2 running PHP 8.1
- **Instance Types:** t3.micro (cost-optimized for startup requirements)
- **Auto Scaling:** Min: 2, Max: 8 instances
- **Load Balancer:** Application Load Balancer (ALB)
- **Database:** MySQL 8.0.35 with Multi-AZ deployment

### Security Configuration
- **Security Groups:** Custom rules for HTTP (80), HTTPS (443), SSH (22)
- **VPC:** Custom VPC (10.0.0.0/16) with public subnets
- **Subnets:**
    - Public Subnet 1: 10.0.1.0/24 (us-east-1a)
    - Public Subnet 2: 10.0.2.0/24 (us-east-1b)
- **Key Pairs:** Consistent SSH access across all instances

### Monitoring & Alerting
- **CloudWatch:** Comprehensive metrics collection
- **SNS:** Email notifications for critical events
- **Health Checks:** Application and database connectivity monitoring
- **Auto Recovery:** Automatic instance replacement on failure

## 📊 Performance Metrics

The application continuously monitors:
- **Response Time:** Page generation and API response times
- **Memory Usage:** PHP memory consumption and limits
- **Database Performance:** Connection times and query performance
- **System Load:** CPU load averages and system health
- **Disk Usage:** Available storage and usage percentages

## 🔧 API Usage Examples

### Get System Status
```bash
curl http://lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com/api.php/status
```

### Create Log Entry
```bash
curl -X POST http://lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com/api.php/logs \
  -H "Content-Type: application/json" \
  -d '{"message": "Test log entry", "severity": "INFO", "category": "TESTING"}'
```

### Get Health Information
```bash
curl http://lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com/enhanced_health.php
```

## 🎨 User Interface Features

### Modern Design
- **Responsive Layout:** Mobile-friendly design with CSS Grid and Flexbox
- **AWS Color Scheme:** Professional appearance using AWS brand colors
- **Interactive Elements:** Hover effects and smooth transitions
- **Loading Animations:** Progressive loading for enhanced user experience

### Accessibility
- **Semantic HTML:** Proper structure for screen readers
- **Focus Management:** Keyboard navigation support
- **Color Contrast:** WCAG compliant color combinations
- **Print Styles:** Optimized printing for documentation

## 🚨 Health Check Integration

The application provides multiple health check endpoints:

1. **Basic Health (`/health.php`):** Simple JSON health status
2. **Enhanced Health (`/enhanced_health.php`):** Comprehensive system checks
3. **API Health (`/api.php/health`):** Detailed health information with metrics

Load balancer health checks are configured to use `/enhanced_health.php` for accurate service monitoring.

## 📈 Scalability Features

### Automatic Scaling Triggers
- **Scale Out:** Network output > 60% for 2 consecutive minutes
- **Scale In:** Network output < 30% for 5 consecutive minutes
- **Cooldown Period:** 300 seconds between scaling activities

### Performance Optimization
- **Database Connection Pooling:** Efficient database resource usage
- **Caching Headers:** Optimized browser caching for static resources
- **Compression:** Gzip compression for reduced bandwidth usage
- **Image Optimization:** Efficient loading of visual elements

## 🔒 Security Implementation

### Network Security
- **VPC Isolation:** Custom VPC with controlled network access
- **Security Groups:** Restrictive inbound rules with necessary ports only
- **Public Subnets:** Internet gateway access for web traffic

### Application Security
- **Input Validation:** SQL injection and XSS protection
- **Error Handling:** Secure error messages without sensitive information
- **Environment Variables:** Sensitive configuration stored securely

## 📋 Assignment Deliverables

### Deliverable 1: System Architecture (PDF Report)
The comprehensive report available at `/lamp_report.php` serves as the interactive version of the architecture documentation, including:
- Architecture diagram with all AWS components
- Justification for each AWS service
- Assumptions and design decisions
- Complete list of AWS services used

### Deliverable 2: AWS System Development
The live application demonstrates:
- All 10 mandatory AWS requirements implemented
- Scalable and elastic architecture
- High availability across multiple AZs
- Fault tolerance with automatic recovery
- Disaster recovery capabilities
- Comprehensive monitoring and alerting

## 🎓 Educational Value

This implementation demonstrates:
- **Cloud Architecture Principles:** Scalability, elasticity, high availability
- **AWS Service Integration:** Practical use of multiple AWS services
- **DevOps Practices:** Infrastructure as code, automated deployments
- **Monitoring Strategy:** Comprehensive health checks and alerting
- **Security Best Practices:** Network isolation and access control

## 📞 Support Information

**Student Contact:**
- **Name:** Anika Arman
- **Student ID:** 14425754
- **Email:** anika.arman@student.uts.edu.au
- **Subject:** 32555 Cloud Computing and Software as a Service

**Application URLs:**
- **Primary:** http://lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com/
- **Health Check:** http://lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com/enhanced_health.php

---

*This application successfully demonstrates the migration of a startup's LAMP stack from a single desktop PC to a scalable, elastic, highly available AWS cloud infrastructure that addresses both scalability and disaster recovery concerns as outlined in Assignment 3 requirements.*
