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
        <link rel="stylesheet" href="assets/css/services.css">
    </head>
    <body>
        <!-- Header -->
        <header class="header">
            <div class="container nav-container">
                <a href="/" class="logo">DigitalAgency</a>
                <nav>
                    <button class="menu-toggle" id="menuToggle">
                        <span class="hamburger"></span>
                    </button>
                    <ul class="nav-menu">
                        <li><a href="/">Home</a></li>
                        <li><a href="/pages/services.php">Services</a></li>
                        <li><a href="/pages/portfolio.php">Portfolio</a></li>
                        <li><a href="/pages/about.php">About</a></li>
                        <li><a href="/pages/blog.php">Blog</a></li>
                        <li><a href="/pages/contact.php">Contact</a></li>
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
                            <div class="service-icon">
                                <i class="icon-' . htmlspecialchars($service['icon']) . '"></i>
                            </div>
                            <h3>' . htmlspecialchars($service['title']) . '</h3>
                            <p>' . htmlspecialchars($service['description']) . '</p>
                            <a href="/pages/service-detail.php?id=' . $service['id'] . '" class="btn btn-secondary">Learn More</a>
                        </div>';
                    }
                    ?>
                </div>
                <div class="services-cta">
                    <p>Want to explore all our services?</p>
                    <a href="/pages/services.php" class="btn btn-primary">View All Services</a>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="about-preview" id="about">
            <div class="container">
                <h2>About Us</h2>
                <div class="about-content">
                    <div class="about-text">
                        <p>Your trusted partner in digital transformation. We create innovative solutions that drive business growth.</p>
                        <a href="/pages/about.php" class="btn btn-secondary">Learn More About Us</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Portfolio Preview -->
        <section class="portfolio-preview" id="portfolio">
            <div class="container">
                <h2>Our Work</h2>
                <div class="portfolio-grid">
                    <?php
                    // Fetch latest portfolio items
                    $stmt = $pdo->query("SELECT * FROM portfolio_projects ORDER BY created_at DESC LIMIT 3");
                    while ($project = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="portfolio-item">
                            <div class="portfolio-image">
                                <img src="' . htmlspecialchars($project['featured_image']) . '" 
                                     alt="' . htmlspecialchars($project['title']) . '">
                                <div class="portfolio-overlay">
                                    <h3>' . htmlspecialchars($project['title']) . '</h3>
                                    <p>' . htmlspecialchars($project['client_name']) . '</p>
                                    <a href="/pages/portfolio-detail.php?id=' . $project['id'] . '" 
                                       class="btn btn-secondary">View Project</a>
                                </div>
                            </div>
                        </div>';
                    }
                    ?>
                </div>
                <div class="portfolio-cta">
                    <a href="/pages/portfolio.php" class="btn btn-primary">View All Projects</a>
                </div>
            </div>
        </section>

        <!-- Blog Preview -->
        <section class="blog-preview">
            <div class="container">
                <h2>Latest Insights</h2>
                <div class="blog-grid">
                    <?php
                    // Fetch latest blog posts
                    $stmt = $pdo->query("
                        SELECT p.*, u.first_name, u.last_name 
                        FROM blog_posts p
                        LEFT JOIN users u ON p.author_id = u.id
                        WHERE p.status = 'published'
                        ORDER BY p.created_at DESC 
                        LIMIT 3
                    ");
                    while ($post = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="blog-card">
                            <div class="blog-image">
                                <img src="' . htmlspecialchars($post['featured_image']) . '" 
                                     alt="' . htmlspecialchars($post['title']) . '">
                            </div>
                            <div class="blog-content">
                                <h3><a href="/pages/blog-post.php?slug=' . htmlspecialchars($post['slug']) . '">' 
                                    . htmlspecialchars($post['title']) . '</a></h3>
                                <p class="blog-meta">By ' . htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) 
                                    . ' | ' . date('M j, Y', strtotime($post['created_at'])) . '</p>
                                <p class="blog-excerpt">' . htmlspecialchars($post['excerpt']) . '</p>
                            </div>
                        </div>';
                    }
                    ?>
                </div>
                <div class="blog-cta">
                    <a href="/pages/blog.php" class="btn btn-primary">Read More Articles</a>
                </div>
            </div>
        </section>

        <!-- Contact Preview -->
        <section class="contact-preview" id="contact">
            <div class="container">
                <h2>Ready to Start Your Project?</h2>
                <p>Let's discuss how we can help your business grow</p>
                <a href="/pages/contact.php" class="btn btn-primary">Get in Touch</a>
            </div>
        </section>

        <?php include 'includes/footer.php'; ?>

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