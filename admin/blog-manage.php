<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/upload-handler.php';

$auth = new Auth($pdo);

// Check if user is logged in and is admin
if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_post':
                $title = $_POST['title'] ?? '';
                $content = $_POST['content'] ?? '';
                $excerpt = $_POST['excerpt'] ?? '';
                $status = $_POST['status'] ?? 'draft';
                $categories = $_POST['categories'] ?? [];
                
                // Generate slug from title
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                
                // Handle featured image upload
                $featured_image = '';
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                    $uploader = new UploadHandler('../uploads/blog/');
                    $featured_image = $uploader->upload($_FILES['featured_image']);
                    if (!$featured_image) {
                        $errors = array_merge($errors, $uploader->getErrors());
                    }
                }

                if (empty($errors)) {
                    try {
                        $pdo->beginTransaction();

                        // Insert blog post
                        $stmt = $pdo->prepare("
                            INSERT INTO blog_posts 
                            (title, slug, content, excerpt, featured_image, author_id, status)
                            VALUES (?, ?, ?, ?, ?, ?, ?)
                        ");
                        
                        $stmt->execute([
                            $title,
                            $slug,
                            $content,
                            $excerpt,
                            $featured_image ? '/uploads/blog/' . $featured_image : null,
                            $_SESSION['user_id'],
                            $status
                        ]);

                        $post_id = $pdo->lastInsertId();

                        // Add categories
                        if (!empty($categories)) {
                            $stmt = $pdo->prepare("
                                INSERT INTO post_categories (post_id, category_id)
                                VALUES (?, ?)
                            ");
                            
                            foreach ($categories as $category_id) {
                                $stmt->execute([$post_id, $category_id]);
                            }
                        }

                        $pdo->commit();
                        $message = 'Blog post added successfully!';
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $errors[] = 'Error adding blog post: ' . $e->getMessage();
                    }
                }
                break;

            case 'delete_post':
                $post_id = $_POST['post_id'] ?? 0;
                try {
                    // Get image path before deletion
                    $stmt = $pdo->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
                    $stmt->execute([$post_id]);
                    $image = $stmt->fetchColumn();

                    // Delete post (cascade will handle post_categories)
                    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
                    $stmt->execute([$post_id]);

                    // Delete physical file if exists
                    if ($image) {
                        $filepath = $_SERVER['DOCUMENT_ROOT'] . $image;
                        if (file_exists($filepath)) {
                            unlink($filepath);
                        }
                    }

                    $message = 'Blog post deleted successfully!';
                } catch (Exception $e) {
                    $errors[] = 'Error deleting blog post: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all categories for the form
$categories = $pdo->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();

// Get all posts for display
$posts = $pdo->query("
    SELECT p.*, u.username as author_name 
    FROM blog_posts p 
    LEFT JOIN users u ON p.author_id = u.id 
    ORDER BY p.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog - Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/5/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'link image code',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | link image | code'
        });
    </script>
</head>
<body class="admin-page">
    <div class="admin-container">
        <h1>Manage Blog Posts</h1>

        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="message error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="action" value="add_post">
            
            <div class="form-group">
                <label for="title">Post Title *</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content" required></textarea>
            </div>

            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                <input type="file" id="featured_image" name="featured_image" accept="image/*">
            </div>

            <div class="form-group">
                <label>Categories</label>
                <div class="checkbox-group">
                    <?php foreach ($categories as $category): ?>
                        <label>
                            <input type="checkbox" name="categories[]" 
                                   value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Add Post</button>
        </form>

        <h2>Existing Posts</h2>
        <div class="posts-table">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                            <td><?php echo htmlspecialchars($post['status']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($post['created_at'])); ?></td>
                            <td>
                                <a href="blog-edit.php?id=<?php echo $post['id']; ?>" 
                                   class="btn btn-secondary">Edit</a>
                                <form action="" method="POST" class="delete-form" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_post">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this post?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 