<?php
require_once '../includes/db.php';
$pageTitle = 'Our Services';
$pageStyle = 'services';
include '../includes/header.php';
?>

<section class="services-hero">
    <div class="container">
        <h1>Our Services</h1>
        <p>Comprehensive digital solutions for your business growth</p>
    </div>
</section>

<section class="services-grid">
    <div class="container">
        <?php
        $stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
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
</section>

<section class="cta-section">
    <div class="container">
        <h2>Ready to Start Your Project?</h2>
        <p>Let's discuss how we can help your business grow</p>
        <a href="/pages/contact.php" class="btn btn-primary">Get in Touch</a>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 