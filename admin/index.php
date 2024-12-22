<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$auth = new Auth($pdo);

// Check if user is logged in and is admin
if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}

// Get statistics
$stats = [
    'posts' => $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn(),
    'projects' => $pdo->query("SELECT COUNT(*) FROM portfolio_projects")->fetchColumn(),
    'messages' => $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn()
];

// Get recent activities
$recentPosts = $pdo->query("
    SELECT title, created_at 
    FROM blog_posts 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll();

$recentMessages = $pdo->query("
    SELECT name, subject, created_at 
    FROM contact_messages 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="admin-page">
    <?php include 'includes/admin-header.php'; ?>

    <div class="admin-container">
        <div class="dashboard-grid">
            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <h3>Blog Posts</h3>
                    <p class="stat-number"><?php echo $stats['posts']; ?></p>
                    <a href="blog-manage.php" class="stat-link">Manage Posts</a>
                </div>
                <div class="stat-card">
                    <h3>Portfolio Projects</h3>
                    <p class="stat-number"><?php echo $stats['projects']; ?></p>
                    <a href="portfolio-manage.php" class="stat-link">Manage Projects</a>
                </div>
                <div class="stat-card">
                    <h3>New Messages</h3>
                    <p class="stat-number"><?php echo $stats['messages']; ?></p>
                    <a href="messages.php" class="stat-link">View Messages</a>
                </div>
                <div class="stat-card">
                    <h3>Users</h3>
                    <p class="stat-number"><?php echo $stats['users']; ?></p>
                    <a href="users.php" class="stat-link">Manage Users</a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="dashboard-section">
                <h2>Recent Blog Posts</h2>
                <div class="activity-list">
                    <?php foreach ($recentPosts as $post): ?>
                        <div class="activity-item">
                            <span class="activity-title"><?php echo htmlspecialchars($post['title']); ?></span>
                            <span class="activity-date">
                                <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="dashboard-section">
                <h2>Recent Messages</h2>
                <div class="activity-list">
                    <?php foreach ($recentMessages as $message): ?>
                        <div class="activity-item">
                            <span class="activity-title">
                                <?php echo htmlspecialchars($message['name']); ?> - 
                                <?php echo htmlspecialchars($message['subject']); ?>
                            </span>
                            <span class="activity-date">
                                <?php echo date('M j, Y', strtotime($message['created_at'])); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/admin-footer.php'; ?>
</body>
</html> 