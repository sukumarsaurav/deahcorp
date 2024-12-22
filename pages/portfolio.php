<?php
require_once '../includes/db.php';
$pageTitle = 'Our Portfolio';
include '../includes/header.php';

// Get category filter if set
$category = isset($_GET['category']) ? $_GET['category'] : null;

// Fetch categories for filter
$categories = $pdo->query("SELECT DISTINCT category FROM portfolio_projects WHERE category IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
?>

<section class="portfolio-hero">
    <div class="container">
        <h1>Our Portfolio</h1>
        <p>Explore our latest work and success stories</p>
    </div>
</section>

<section class="portfolio-filters">
    <div class="container">
        <div class="filter-buttons">
            <button class="filter-btn <?php echo !$category ? 'active' : ''; ?>" 
                    onclick="window.location.href='portfolio.php'">All</button>
            <?php foreach ($categories as $cat): ?>
                <button class="filter-btn <?php echo $category === $cat ? 'active' : ''; ?>"
                        onclick="window.location.href='portfolio.php?category=<?php echo urlencode($cat); ?>'">
                    <?php echo htmlspecialchars($cat); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="portfolio-grid">
    <div class="container">
        <?php
        $query = "SELECT * FROM portfolio_projects";
        if ($category) {
            $query .= " WHERE category = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query($query);
        }

        while ($project = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="portfolio-item">
                <div class="portfolio-image">
                    <img src="' . htmlspecialchars($project['featured_image']) . '" 
                         alt="' . htmlspecialchars($project['title']) . '">
                    <div class="portfolio-overlay">
                        <h3>' . htmlspecialchars($project['title']) . '</h3>
                        <p>' . htmlspecialchars($project['client_name']) . '</p>
                        <a href="portfolio-detail.php?id=' . $project['id'] . '" 
                           class="btn btn-secondary">View Project</a>
                    </div>
                </div>
            </div>';
        }
        ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 