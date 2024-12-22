<?php
require_once '../includes/db.php';
$pageTitle = 'About Us';
$pageStyle = 'about';
include '../includes/header.php';
?>

<head>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/about.css">
</head>

<section class="about-hero">
    <div class="container">
        <h1>About Us</h1>
        <p>Your trusted partner in digital transformation</p>
    </div>
</section>

<section class="about-content">
    <div class="container">
        <div class="mission-vision">
            <div class="mission">
                <h2>Our Mission</h2>
                <p>To empower businesses with innovative digital solutions that drive growth and success in the modern world.</p>
            </div>
            <div class="vision">
                <h2>Our Vision</h2>
                <p>To be the leading digital agency that transforms businesses through creative and technological excellence.</p>
            </div>
        </div>
    </div>
</section>

<section class="team-section">
    <div class="container">
        <h2>Meet Our Team</h2>
        <div class="team-grid">
            <?php
            $stmt = $pdo->query("SELECT * FROM team_members ORDER BY id ASC");
            while ($member = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="team-member">
                    <div class="member-image">
                        <img src="' . htmlspecialchars($member['image'] ?? '/assets/images/default-avatar.jpg') . '" 
                             alt="' . htmlspecialchars($member['name']) . '">
                    </div>
                    <h3>' . htmlspecialchars($member['name']) . '</h3>
                    <p class="position">' . htmlspecialchars($member['position']) . '</p>
                    <p class="bio">' . htmlspecialchars($member['bio']) . '</p>
                    <div class="social-links">';
                if ($member['linkedin_url']) {
                    echo '<a href="' . htmlspecialchars($member['linkedin_url']) . '" target="_blank">LinkedIn</a>';
                }
                if ($member['twitter_url']) {
                    echo '<a href="' . htmlspecialchars($member['twitter_url']) . '" target="_blank">Twitter</a>';
                }
                echo '</div></div>';
            }
            ?>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 