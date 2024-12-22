<?php
require_once '../includes/db.php';
$pageTitle = 'Blog';
$pageStyle = 'blog';
include '../includes/header.php';

// Pagination
$postsPerPage = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $postsPerPage;

// Get total posts count
$totalPosts = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'")->fetchColumn();
$totalPages = ceil($totalPosts / $postsPerPage);

// Get posts
$stmt = $pdo->prepare("
    SELECT p.*, u.first_name, u.last_name 
    FROM blog_posts p
    LEFT JOIN users u ON p.author_id = u.id
    WHERE p.status = 'published'
    ORDER BY p.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$postsPerPage, $offset]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for sidebar
$categories = $pdo->query("
    SELECT c.*, COUNT(pc.post_id) as post_count 
    FROM blog_categories c
    LEFT JOIN post_categories pc ON c.id = pc.category_id
    GROUP BY c.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<head>
    <!-- ... other meta tags ... -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/blog.css">
</head>

<section class="blog-hero">
    <div class="container">
        <h1>Blog</h1>
        <p>Insights, News, and Digital Trends</p>
    </div>
</section>

<section class="blog-content">
    <div class="container">
        <div class="blog-grid">
            <div class="posts-section">
                <?php foreach ($posts as $post): ?>
                    <article class="blog-post">
                        <div class="post-image">
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>">
                        </div>
                        <div class="post-content">
                            <h2><a href="blog-post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a></h2>
                            <div class="post-meta">
                                <span class="author">By <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?></span>
                                <span class="date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                            </div>
                            <p class="excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <a href="blog-post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" 
                               class="read-more">Read More</a>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" 
                               class="<?php echo $page === $i ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="blog-sidebar">
                <div class="sidebar-widget">
                    <h3>Categories</h3>
                    <ul class="category-list">
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="?category=<?php echo urlencode($category['slug']); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                    <span>(<?php echo $category['post_count']; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 