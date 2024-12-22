<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

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
            case 'add_category':
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO blog_categories (name, slug, description)
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$name, $slug, $description]);
                    $message = 'Category added successfully!';
                } catch (Exception $e) {
                    $errors[] = 'Error adding category: ' . $e->getMessage();
                }
                break;

            case 'delete_category':
                $category_id = $_POST['category_id'] ?? 0;
                try {
                    $stmt = $pdo->prepare("DELETE FROM blog_categories WHERE id = ?");
                    $stmt->execute([$category_id]);
                    $message = 'Category deleted successfully!';
                } catch (Exception $e) {
                    $errors[] = 'Error deleting category: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all categories
$categories = $pdo->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-page">
    <div class="admin-container">
        <h1>Manage Blog Categories</h1>

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

        <form action="" method="POST" class="admin-form">
            <input type="hidden" name="action" value="add_category">
            
            <div class="form-group">
                <label for="name">Category Name *</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>

        <h2>Existing Categories</h2>
        <div class="categories-table">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['slug']); ?></td>
                            <td><?php echo htmlspecialchars($category['description']); ?></td>
                            <td>
                                <form action="" method="POST" class="delete-form" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_category">
                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this category?')">
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