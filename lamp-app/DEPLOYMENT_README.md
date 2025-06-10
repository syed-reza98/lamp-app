# LAMP Application - AWS Deployment Package

## 📋 Overview
This folder contains the clean, production-ready LAMP stack application for AWS Elastic Beanstalk deployment.

**Student:** Anika Arman
**Student ID:** 14425754
**Assignment:** 32555 Cloud Computing - Assignment 3
**Date:** June 10, 2025

---

## 📁 **File Structure**

### **Core Application Files:**
- **`index.php`** - Main application homepage with AWS integration demo
- **`lamp_report.php`** - Comprehensive AWS infrastructure report (Assignment 3 main deliverable)
- **`api.php`** - RESTful API endpoints for LAMP stack functionality
- **`enhanced_health.php`** - Advanced health check endpoint for load balancer monitoring

### **Database & Initialization:**
- **`init_database.php`** - Database initialization script with sample data
- **`health.php`** - Basic health check endpoint

### **Styling & Assets:**
- **`fresh_styles.css`** - Modern, responsive CSS styling for all pages

### **Configuration Files:**
- **`environment.config`** - Local environment configuration
- **`.ebextensions/environment.config`** - AWS Elastic Beanstalk deployment configuration

### **Utility Files:**
- **`phpinfo.php`** - PHP environment information
- **`README.md`** - Application documentation

---

## 🚀 **Deployment Instructions**

### **For AWS Elastic Beanstalk:**
1. **Create Deployment Package:**
```bash
# Zip all files in this directory
zip -r lamp-app-deployment.zip . -x "*.git*" "*.DS_Store*"
```

2. **Deploy to Elastic Beanstalk:**
    - Upload the zip file to your Elastic Beanstalk environment
    - The `.ebextensions/environment.config` will automatically configure:
        - RDS database connection
        - Auto Scaling (2-8 instances)
        - Security groups
        - Health monitoring

### **For Local XAMPP Testing:**
1. **Copy files to XAMPP:**
```bash
# Copy to your XAMPP htdocs directory
cp -r * C:/xampp/htdocs/lamp-app/
```

2. **Start Services:**
    - Start Apache and MySQL in XAMPP Control Panel
    - Visit: `http://localhost/lamp-app/`

---

## 🔧 **Configuration Details**

### **Database Configuration:**
```php
// Environment variables (automatically set by .ebextensions)
RDS_HOSTNAME: lamp-app-db.cijnrr1efnu0.us-east-1.rds.amazonaws.com
RDS_DB_NAME: lampapp
RDS_USERNAME: lampdbadmin
RDS_PASSWORD: SecurePass123!
```

### **Auto Scaling Configuration:**
- **Min Instances:** 2
- **Max Instances:** 8
- **Scaling Trigger:** NetworkOut traffic
- **Scale Up Threshold:** 6MB
- **Scale Down Threshold:** 2MB

---

## 📊 **Application Features**

### **Main Report (`lamp_report.php`):**
- ✅ Live AWS infrastructure status
- ✅ All 10 mandatory requirements implementation
- ✅ Real-time performance metrics
- ✅ Architecture benefits analysis
- ✅ Technical specifications
- ✅ Health monitoring dashboard

### **API Endpoints (`api.php`):**
- `GET /api.php/status` - Application status
- `GET /api.php/health` - Health check
- `GET /api.php/database` - Database connectivity
- `GET /api.php/metrics` - Performance metrics

### **Health Monitoring:**
- **`/enhanced_health.php`** - Comprehensive health checks
- **`/health.php`** - Basic health endpoint for load balancer

---

## 🛡️ **Security Features**

- ✅ **XSS Protection:** HTML escaping on all outputs
- ✅ **SQL Injection Protection:** Prepared statements
- ✅ **Environment Variables:** Secure credential management
- ✅ **VPC Security:** Custom security groups configured
- ✅ **HTTPS Ready:** SSL certificate support

---

## 📈 **Performance Optimizations**

- ✅ **Consolidated CSS:** Single stylesheet for faster loading
- ✅ **Optimized Database Queries:** Efficient query patterns
- ✅ **Caching Headers:** Proper HTTP caching
- ✅ **Minified Assets:** Compressed for faster delivery
- ✅ **Health Check Optimization:** Fast response times

---

## 🔍 **Testing URLs**

### **After Deployment:**
- **Main Application:** `https://your-app.elasticbeanstalk.com/`
- **Assignment Report:** `https://your-app.elasticbeanstalk.com/lamp_report.php`
- **Health Check:** `https://your-app.elasticbeanstalk.com/enhanced_health.php`
- **API Status:** `https://your-app.elasticbeanstalk.com/api.php/status`
- **Database Init:** `https://your-app.elasticbeanstalk.com/init_database.php`

---

## 🎯 **Assignment 3 Compliance**

### **All 10 Mandatory Requirements Implemented:**
1. **✅ (a) AWS Elastic Beanstalk** - Application deployed and managed
2. **✅ (b) Amazon EC2** - Multiple instances across AZs
3. **✅ (c) Custom AMI** - Custom machine images created
4. **✅ (d) Security Groups** - HTTP/SSH access configured
5. **✅ (e) Load Balancer** - ELB with health checks
6. **✅ (f) Auto Scaling** - 2-8 instances with network triggers
7. **✅ (g) RDS Multi-AZ** - MySQL database with failover
8. **✅ (h) Custom VPC** - Multi-AZ network architecture
9. **✅ (i) Key Pairs** - Consistent SSH key management
10. **✅ (j) Email Notifications** - SNS alerts configured

---

## 📝 **Notes for Developers**

### **Key Features:**
- **Live AWS Integration:** Real-time data from AWS APIs
- **Responsive Design:** Works on desktop, tablet, and mobile
- **Comprehensive Monitoring:** Multiple health check endpoints
- **Production Ready:** Optimized for AWS deployment

### **Customization:**
- Update database credentials in `.ebextensions/environment.config`
- Modify styling in `fresh_styles.css`
- Add new API endpoints in `api.php`
- Extend health checks in `enhanced_health.php`

---

## 📞 **Support**

For questions or issues:
- **Student:** Anika Arman
- **Email:** anika.arman@student.uts.edu.au
- **Subject:** 32555 Cloud Computing and Software as a Service

---

*Last Updated: June 10, 2025*
*Version: lamp-app-v5 (Production Ready)*
