<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS LAMP Stack Architecture - Assignment 3</title>
    <link rel="stylesheet" href="optimized_styles.css">
    <link rel="stylesheet" href="architecture_styles.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>🏗️ AWS LAMP Stack Architecture</h1>
            <p>Scalable, Elastic, High-Availability Implementation</p>
        </header>

        <main>
            <?php
            require_once 'aws_architecture_optimized.php';
            echo getEnhancedArchitectureDiagram();
            ?>
        </main>

        <footer>
            <p><strong>Student:</strong> Anika Arman | <strong>ID:</strong> 14425754</p>
            <p><strong>Subject:</strong> 32555 Cloud Computing and Software as a Service</p>
            <p><strong>Generated:</strong> <?php echo date('Y-m-d H:i:s T'); ?></p>
        </footer>
    </div>
</body>

</html>