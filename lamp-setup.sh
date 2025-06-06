#!/bin/bash
# LAMP Stack Installation Script for Amazon Linux 2

# Update packages
sudo yum update -y

# Install Apache
sudo yum install -y httpd
sudo systemctl start httpd
sudo systemctl enable httpd

# Install MySQL
sudo yum install -y mysql-server
sudo systemctl start mysqld
sudo systemctl enable mysqld

# Install PHP 7.4
sudo amazon-linux-extras install -y php7.4
sudo yum install -y php-mysqlnd php-mbstring php-xml php-gd php-pdo php-json php-curl

# Restart Apache to load PHP
sudo systemctl restart httpd

# Create a basic PHP info file
echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/info.php

# Create a basic index.php file
cat <<'EOL' | sudo tee /var/www/html/index.php
<!DOCTYPE html>
<html>
<head>
    <title>LAMP Stack on AWS Elastic Beanstalk</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .container { background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px; }
        .info { background-color: #e8f4f8; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 LAMP Stack Application on AWS Elastic Beanstalk</h1>
        
        <div class="info">
            <h2>Server Information:</h2>
            <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_NAME']; ?></p>
            <p><strong>Server IP:</strong> <?php echo $_SERVER['SERVER_ADDR']; ?></p>
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>

        <div class="info">
            <h2>Database Connection Test:</h2>
            <?php
            // Use environment variables from Elastic Beanstalk
            $dbhost = getenv('RDS_HOSTNAME');
            $dbport = getenv('RDS_PORT');
            $dbname = getenv('RDS_DB_NAME');
            $username = getenv('RDS_USERNAME');
            $password = getenv('RDS_PASSWORD');

            if ($dbhost && $dbport && $dbname && $username && $password) {
                try {
                    $pdo = new PDO("mysql:host=$dbhost;port=$dbport;dbname=$dbname", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    echo '<p class="success">✅ Database connection successful!</p>';
                    echo '<p><strong>Database Host:</strong> ' . $dbhost . '</p>';
                    echo '<p><strong>Database Name:</strong> ' . $dbname . '</p>';
                    
                    // Test query
                    $stmt = $pdo->query('SELECT VERSION() as version');
                    $version = $stmt->fetch();
                    echo '<p><strong>MySQL Version:</strong> ' . $version['version'] . '</p>';
                    
                } catch(PDOException $e) {
                    echo '<p class="error">❌ Database connection failed: ' . $e->getMessage() . '</p>';
                }
            } else {
                echo '<p class="error">❌ Database environment variables not configured</p>';
                echo '<p>Please ensure RDS environment variables are set in Elastic Beanstalk</p>';
            }
            ?>
        </div>

        <div class="info">
            <h2>PHP Extensions:</h2>
            <p><?php echo implode(', ', get_loaded_extensions()); ?></p>
        </div>

        <div class="info">
            <h2>Environment Variables:</h2>
            <p><strong>RDS_HOSTNAME:</strong> <?php echo getenv('RDS_HOSTNAME') ?: 'Not set'; ?></p>
            <p><strong>RDS_PORT:</strong> <?php echo getenv('RDS_PORT') ?: 'Not set'; ?></p>
            <p><strong>RDS_DB_NAME:</strong> <?php echo getenv('RDS_DB_NAME') ?: 'Not set'; ?></p>
            <p><strong>RDS_USERNAME:</strong> <?php echo getenv('RDS_USERNAME') ?: 'Not set'; ?></p>
        </div>
    </div>
</body>
</html>
EOL

# Set proper permissions
sudo chown -R apache:apache /var/www/html
sudo chmod -R 755 /var/www/html

echo "LAMP stack installation completed successfully!"
