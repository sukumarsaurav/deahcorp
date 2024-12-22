<?php
// Include error handler first
require_once 'includes/error_handler.php';

try {
    // Load configuration
    require_once 'includes/Config.php';
    Config::load();
    
    // Include database connection
    require_once 'includes/Database.php';
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Start output buffering to catch any errors
    ob_start();
    
    // Test database connection
    try {
        $stmt = $pdo->query("SELECT 1");
    } catch (PDOException $e) {
        throw new Exception("Database connection test failed: " . $e->getMessage());
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Digital Agency - Your One-Stop Solution</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
        <!-- Header -->
        <header class="header">
            <div class="container nav-container">
                <a href="/" class="logo">DigitalAgency</a>
                <nav>
                    <button class="menu-toggle">Menu</button>
                    <ul class="nav-menu">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#portfolio">Portfolio</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero" id="home">
            <div class="container hero-content">
                <h1>Your One-Stop Digital Agency for Website Design, Branding, and Marketing Solutions</h1>
                <p>From stunning websites and apps to powerful digital marketing campaigns and branded merchandise, we've got you covered.</p>
                <a href="#contact" class="btn btn-primary">Get a Free Consultation</a>
            </div>
        </section>

        <!-- Services Section -->
        <section class="services" id="services">
            <div class="container">
                <h2>Our Services</h2>
                <div class="services-grid">
                    <?php
                    // Fetch services from database
                    $stmt = $pdo->query("SELECT * FROM services");
                    while ($service = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="service-card">
                            <h3>' . htmlspecialchars($service['title']) . '</h3>
                            <p>' . htmlspecialchars($service['description']) . '</p>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Additional sections would go here -->

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-info">
                        <h3>DigitalAgency</h3>
                        <p>Your partner in digital success</p>
                    </div>
                    <div class="footer-links">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="#services">Services</a></li>
                            <li><a href="#portfolio">Portfolio</a></li>
                            <li><a href="#about">About</a></li>
                            <li><a href="#contact">Contact</a></li>
                        </ul>
                    </div>
                    <div class="footer-contact">
                        <h4>Contact Us</h4>
                        <p>Email: info@digitalagency.com</p>
                        <p>Phone: (123) 456-7890</p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2024 DigitalAgency. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <script src="assets/js/main.js"></script>
    </body>
    </html>
    <?php
    
    // Flush the output buffer
    ob_end_flush();
    
} catch (Exception $e) {
    // Log the error
    error_log("Critical error in index.php: " . $e->getMessage());
    
    // Clean the output buffer
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Show error based on environment
    if (!Config::get('APP_ENV') === 'production') {
        echo "<h1>Error Details</h1>";
        echo "<p>Message: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>File: " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p>Line: " . $e->getLine() . "</p>";
        echo "<h2>Stack Trace:</h2>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        header('Location: /error500.php');
    }
    exit;
} 