<?php
require_once '../includes/db.php';

if (!isset($_GET['id'])) {
    header('Location: portfolio.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM portfolio_projects WHERE id = ?");
$stmt->execute([$_GET['id']]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header('Location: portfolio.php');
    exit;
}

$pageTitle = 'Portfolio Details';
$pageStyle = 'portfolio';
include '../includes/header.php';

// Fetch project images
$stmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ?");
$stmt->execute([$_GET['id']]);
$projectImages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="project-hero" style="background-image: url('<?php echo htmlspecialchars($project['featured_image']); ?>')">
    <div class="container">
        <h1><?php echo htmlspecialchars($project['title']); ?></h1>
        <p>Client: <?php echo htmlspecialchars($project['client_name']); ?></p>
    </div>
</section>

<section class="project-details">
    <div class="container">
        <div class="project-info">
            <div class="project-description">
                <h2>Project Overview</h2>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
            </div>
            
            <div class="project-meta">
                <div class="meta-item">
                    <h3>Category</h3>
                    <p><?php echo htmlspecialchars($project['category']); ?></p>
                </div>
                <div class="meta-item">
                    <h3>Date</h3>
                    <p><?php echo date('F Y', strtotime($project['project_date'])); ?></p>
                </div>
                <?php if ($project['project_url']): ?>
                <div class="meta-item">
                    <h3>Project URL</h3>
                    <a href="<?php echo htmlspecialchars($project['project_url']); ?>" 
                       target="_blank" class="project-link">Visit Website</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($projectImages): ?>
        <div class="project-gallery">
            <h2>Project Gallery</h2>
            <div class="gallery-grid">
                <?php foreach ($projectImages as $image): ?>
                    <div class="gallery-item">
                        <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($image['image_description']); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 