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

session_start();
// Add authentication check here

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_project':
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $client_name = $_POST['client_name'] ?? '';
                $project_date = $_POST['project_date'] ?? '';
                $category = $_POST['category'] ?? '';
                $project_url = $_POST['project_url'] ?? '';
                $technologies = $_POST['technologies'] ?? '';

                // Handle featured image upload
                $uploader = new UploadHandler('../uploads/portfolio/');
                $featured_image = '';
                
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                    $featured_image = $uploader->upload($_FILES['featured_image']);
                    if (!$featured_image) {
                        $errors = array_merge($errors, $uploader->getErrors());
                    }
                }

                if (empty($errors)) {
                    try {
                        $pdo->beginTransaction();

                        // Insert project
                        $stmt = $pdo->prepare("
                            INSERT INTO portfolio_projects 
                            (title, description, client_name, project_date, category, 
                             featured_image, project_url, technologies_used)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        
                        $stmt->execute([
                            $title, $description, $client_name, $project_date,
                            $category, '/uploads/portfolio/' . $featured_image,
                            $project_url, $technologies
                        ]);

                        $project_id = $pdo->lastInsertId();

                        // Handle additional images
                        if (isset($_FILES['additional_images'])) {
                            $stmt = $pdo->prepare("
                                INSERT INTO project_images (project_id, image_url, image_description)
                                VALUES (?, ?, ?)
                            ");

                            foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
                                if ($_FILES['additional_images']['error'][$key] === UPLOAD_ERR_OK) {
                                    $file = [
                                        'name' => $_FILES['additional_images']['name'][$key],
                                        'type' => $_FILES['additional_images']['type'][$key],
                                        'tmp_name' => $tmp_name,
                                        'error' => $_FILES['additional_images']['error'][$key],
                                        'size' => $_FILES['additional_images']['size'][$key]
                                    ];
                                    
                                    $image_name = $uploader->upload($file);
                                    if ($image_name) {
                                        $stmt->execute([
                                            $project_id,
                                            '/uploads/portfolio/' . $image_name,
                                            $_POST['image_descriptions'][$key] ?? ''
                                        ]);
                                    }
                                }
                            }
                        }

                        $pdo->commit();
                        $message = 'Project added successfully!';
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $errors[] = 'Error adding project: ' . $e->getMessage();
                    }
                }
                break;

            case 'delete_project':
                $project_id = $_POST['project_id'] ?? 0;
                try {
                    // Get image paths before deletion
                    $stmt = $pdo->prepare("
                        SELECT featured_image FROM portfolio_projects WHERE id = ?
                        UNION ALL
                        SELECT image_url FROM project_images WHERE project_id = ?
                    ");
                    $stmt->execute([$project_id, $project_id]);
                    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    // Delete project and related images (cascade will handle project_images table)
                    $stmt = $pdo->prepare("DELETE FROM portfolio_projects WHERE id = ?");
                    $stmt->execute([$project_id]);

                    // Delete physical files
                    foreach ($images as $image) {
                        $filepath = $_SERVER['DOCUMENT_ROOT'] . $image;
                        if (file_exists($filepath)) {
                            unlink($filepath);
                        }
                    }

                    $message = 'Project deleted successfully!';
                } catch (Exception $e) {
                    $errors[] = 'Error deleting project: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all projects for display
$projects = $pdo->query("SELECT * FROM portfolio_projects ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Portfolio - Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-page">
    <div class="admin-container">
        <h1>Manage Portfolio</h1>

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
            <input type="hidden" name="action" value="add_project">
            
            <div class="form-group">
                <label for="title">Project Title *</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="client_name">Client Name</label>
                <input type="text" id="client_name" name="client_name">
            </div>

            <div class="form-group">
                <label for="project_date">Project Date</label>
                <input type="date" id="project_date" name="project_date">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category">
            </div>

            <div class="form-group">
                <label for="featured_image">Featured Image *</label>
                <input type="file" id="featured_image" name="featured_image" accept="image/*" required>
            </div>

            <div class="form-group">
                <label for="additional_images">Additional Images</label>
                <input type="file" id="additional_images" name="additional_images[]" accept="image/*" multiple>
                <div id="image-descriptions"></div>
            </div>

            <div class="form-group">
                <label for="project_url">Project URL</label>
                <input type="url" id="project_url" name="project_url">
            </div>

            <div class="form-group">
                <label for="technologies">Technologies Used</label>
                <textarea id="technologies" name="technologies"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Add Project</button>
        </form>

        <h2>Existing Projects</h2>
        <div class="projects-grid">
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <img src="<?php echo htmlspecialchars($project['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($project['title']); ?>">
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p><?php echo htmlspecialchars($project['client_name']); ?></p>
                    <form action="" method="POST" class="delete-form">
                        <input type="hidden" name="action" value="delete_project">
                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this project?')">
                            Delete Project
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Handle dynamic image description fields
        document.getElementById('additional_images').addEventListener('change', function(e) {
            const container = document.getElementById('image-descriptions');
            container.innerHTML = '';
            
            for (let i = 0; i < this.files.length; i++) {
                const div = document.createElement('div');
                div.className = 'form-group';
                div.innerHTML = `
                    <label>Description for ${this.files[i].name}</label>
                    <input type="text" name="image_descriptions[]" placeholder="Enter image description">
                `;
                container.appendChild(div);
            }
        });
    </script>
</body>
</html> 