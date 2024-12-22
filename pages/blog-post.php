<?php
require_once '../includes/db.php';

if (!isset($_GET['slug'])) {
    header('Location: blog.php');
    exit;
}

// Get post details
$stmt = $pdo->prepare("
    SELECT p.*, u.first_name, u.last_name 
    FROM blog_posts p
    LEFT JOIN users u ON p.author_id = u.id
    WHERE p.slug = ? AND p.status = 'published'
");
$stmt->execute([$_GET['slug']]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: blog.php');
    exit;
}

// Get post categories
$stmt = $pdo->prepare("
    SELECT c.name, c.slug
    FROM blog_categories c
    JOIN post_categories pc ON c.id = pc.category_id
    WHERE pc.post_id = ?
");
$stmt->execute([$post['id']]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update view count
$stmt = $pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?");
$stmt->execute([$post['id']]);

$pageTitle = 'Blog Post';
$pageStyle = 'blog';
include '../includes/header.php';
?>

<article class="blog-post-detail">
    <div class="post-hero" style="background-image: url('<?php echo htmlspecialchars($post['featured_image']); ?>')">
        <div class="container">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="post-meta">
                <span class="author">By <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?></span>
                <span class="date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                <span class="views"><?php echo number_format($post['views']); ?> views</span>
            </div>
            <div class="categories">
                <?php foreach ($categories as $category): ?>
                    <a href="blog.php?category=<?php echo urlencode($category['slug']); ?>" 
                       class="category-tag"><?php echo htmlspecialchars($category['name']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="post-content">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>

        <div class="share-buttons">
            <h3>Share this post</h3>
            <a href="https://twitter.com/share?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" 
               target="_blank" class="share-btn twitter">Twitter</a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
               target="_blank" class="share-btn facebook">Facebook</a>
            <a href="https://www.linkedin.com/shareArticle?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($post['title']); ?>" 
               target="_blank" class="share-btn linkedin">LinkedIn</a>
        </div>
    </div>
</article>

<?php include '../includes/footer.php'; ?> 