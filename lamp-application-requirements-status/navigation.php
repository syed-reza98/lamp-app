<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS LAMP Stack - Assignment 3 Navigation</title>
    <link rel="stylesheet" href="fresh_styles.css">
    <style>
        .navigation-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .nav-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 4px solid var(--aws-orange);
        }

        .nav-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .nav-card h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .nav-card p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .nav-button {
            display: inline-block;
            background: var(--aws-orange);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .nav-button:hover {
            background: #e68900;
            transform: translateY(-2px);
        }

        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #28a745;
            margin-right: 0.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }
    </style>
</head>

<body>
    <div class="navigation-container">
        <header class="header" style="margin-bottom: 2rem;">
            <div class="header-content">
                <h1>AWS LAMP Stack Application</h1>
                <div class="header-meta">
                    <div class="student-info">
                        <strong>Assignment 3</strong> | Student: Anika Arman (14425754)
                    </div>
                    <div class="timestamp">
                        <span class="status-indicator"></span>
                        System Status: <strong>Online</strong>
                    </div>
                </div>
                <div class="header-description">
                    <p>Scalable, Elastic, High-Availability LAMP Stack on AWS</p>
                </div>
            </div>
        </header>

        <div class="nav-grid">
            <div class="nav-card">
                <h3>📊 Comprehensive Report</h3>
                <p>Complete AWS architecture implementation report with all 10 mandatory requirements (a-j), live metrics, and system status monitoring.</p>
                <a href="lamp_report.php" class="nav-button">View Report</a>
            </div>

            <div class="nav-card">
                <h3>🏥 Health Check</h3>
                <p>Enhanced health monitoring endpoint with database connectivity, system metrics, and AWS instance information for load balancer health checks.</p>
                <a href="enhanced_health.php" class="nav-button">Check Health</a>
            </div>

            <div class="nav-card">
                <h3>🗄️ Database Setup</h3>
                <p>Initialize database tables, insert sample data, and verify RDS Multi-AZ connectivity. Creates all required tables for the application.</p>
                <a href="init_database.php" class="nav-button">Initialize DB</a>
            </div>

            <div class="nav-card">
                <h3>🔗 API Endpoints</h3>
                <p>RESTful API demonstrating LAMP stack functionality with endpoints for logs, metrics, users, and system status. Full CRUD operations.</p>
                <a href="api.php" class="nav-button">View API</a>
            </div>

            <div class="nav-card">
                <h3>📈 System Metrics</h3>
                <p>Real-time system performance metrics including CPU usage, memory consumption, database performance, and AWS instance metadata.</p>
                <a href="api.php/metrics" class="nav-button">View Metrics</a>
            </div>

            <div class="nav-card">
                <h3>📝 Application Logs</h3>
                <p>Centralized logging system with severity levels, categories, and instance tracking. View recent logs and application events.</p>
                <a href="api.php/logs" class="nav-button">View Logs</a>
            </div>

            <div class="nav-card">
                <h3>👥 User Management</h3>
                <p>User management system demonstrating database CRUD operations with status tracking and authentication capabilities.</p>
                <a href="api.php/users" class="nav-button">Manage Users</a>
            </div>

            <div class="nav-card">
                <h3>🔧 Legacy Interface</h3>
                <p>Original assignment interface with basic AWS requirements display. Access the previous version for comparison purposes.</p>
                <a href="index.php?legacy=1" class="nav-button">Legacy View</a>
            </div>
        </div>

        <section style="margin-top: 3rem; padding: 2rem; background: rgba(255,255,255,0.95); border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <h2 style="text-align: center; margin-bottom: 2rem; color: var(--text-primary);">AWS Architecture Summary</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; text-align: center;">
                <div style="padding: 1rem;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">☁️</div>
                    <h4>Elastic Beanstalk</h4>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">Application Platform</p>
                </div>
                <div style="padding: 1rem;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">🖥️</div>
                    <h4>EC2 Auto Scaling</h4>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">2-8 Instances</p>
                </div>
                <div style="padding: 1rem;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">🗄️</div>
                    <h4>RDS Multi-AZ</h4>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">MySQL Database</p>
                </div>
                <div style="padding: 1rem;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚖️</div>
                    <h4>Load Balancer</h4>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">Traffic Distribution</p>
                </div>
                <div style="padding: 1rem;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">🌐</div>
                    <h4>Custom VPC</h4>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">Multi-AZ Network</p>
                </div>
            </div>
        </section>

        <footer style="margin-top: 3rem; text-align: center; padding: 2rem; color: var(--text-secondary);">
            <p><strong>32555 Cloud Computing and Software as a Service</strong></p>
            <p>University of Technology Sydney | Faculty of Engineering and Information Technology</p>
            <p style="margin-top: 1rem; font-family: monospace; font-size: 0.9rem;">
                Deployed on: <span style="color: var(--aws-orange);">lamp-prod-vpc.eba-qcb2embn.us-east-1.elasticbeanstalk.com</span>
            </p>
        </footer>
    </div>

    <script>
        // Auto-refresh status indicator
        function updateStatus() {
            fetch('enhanced_health.php')
                .then(response => response.json())
                .then(data => {
                    const indicator = document.querySelector('.status-indicator');
                    if (data.status === 'healthy') {
                        indicator.style.background = '#28a745';
                    } else if (data.status === 'warning') {
                        indicator.style.background = '#ffc107';
                    } else {
                        indicator.style.background = '#dc3545';
                    }
                })
                .catch(() => {
                    document.querySelector('.status-indicator').style.background = '#6c757d';
                });
        }

        // Update status every 30 seconds
        updateStatus();
        setInterval(updateStatus, 30000);

        // Add loading animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.nav-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>

</html>