<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AWS LAMP Stack Architecture Diagram - Assignment 3</title>
        <link rel="stylesheet" href="formal_diagram_styles.css">
        <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2c3e50;
        }

        .page-title {
            font-size: 24pt;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .page-subtitle {
            font-size: 16pt;
            color: #34495e;
        }

        .navigation-links {
            text-align: center;
            margin-bottom: 20px;
        }

        .nav-link {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .nav-link:hover {
            background: #2980b9;
        }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="page-header">
                <h1 class="page-title">AWS LAMP Stack Architecture Diagram</h1>
                <h2 class="page-subtitle">Assignment 3 - System Architecture Visualization</h2>
            </div>

            <div class="navigation-links">
                <a href="formal_report.php" class="nav-link">📋 Back to Formal Report</a>
                <a href="javascript:window.print()" class="nav-link">🖨️ Print Diagram</a>
            </div>

            <?php
        // Include the formal architecture diagram
        if (file_exists('formal_architecture_diagram.php')) {
            require_once 'formal_architecture_diagram.php';
            echo getFormalArchitectureDiagram();
        } else {
            echo '<div style="padding: 40px; border: 2px dashed #bdc3c7; color: #7f8c8d; text-align: center;">
                    <p>Formal architecture diagram component not found.</p>
                    <p>Please ensure formal_architecture_diagram.php is available.</p>
                  </div>';
        }
        ?>

            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #ecf0f1;">
                <p><strong>Student:</strong> Anika Arman | <strong>ID:</strong> 14425754</p>
                <p><strong>Subject:</strong> 32555 Cloud Computing and Software as a Service</p>
                <p><strong>Assignment:</strong> Assignment 3 - AWS Application Development</p>
            </div>
        </div>
    </body>

</html>