<?php
require_once 'includes/error_handler.php';

try {
    echo "<h1>Configuration Test</h1>";
    
    // Test config loading
    echo "<h2>Config Test:</h2>";
    require_once 'includes/Config.php';
    Config::load();
    echo "<p>Config loaded successfully</p>";
    
    // Test database connection
    echo "<h2>Database Test:</h2>";
    require_once 'includes/Database.php';
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $stmt = $pdo->query("SELECT 1");
    echo "<p>Database connection successful</p>";
    
    // Test file permissions
    echo "<h2>File Permissions Test:</h2>";
    $directories = [
        'logs',
        'uploads',
        'uploads/portfolio',
        'uploads/blog'
    ];
    
    foreach ($directories as $dir) {
        $path = __DIR__ . '/' . $dir;
        echo "<p>Testing $dir: ";
        if (!file_exists($path)) {
            echo "Directory doesn't exist</p>";
            continue;
        }
        echo "Exists, ";
        echo is_writable($path) ? "Writable" : "Not writable";
        echo "</p>";
    }
    
    // Test PHP version and extensions
    echo "<h2>PHP Environment:</h2>";
    echo "<p>PHP Version: " . phpversion() . "</p>";
    echo "<p>Required Extensions:</p>";
    $required_extensions = ['pdo', 'pdo_mysql', 'gd', 'mbstring'];
    foreach ($required_extensions as $ext) {
        echo "<p>$ext: " . (extension_loaded($ext) ? "Loaded" : "Not loaded") . "</p>";
    }
    
} catch (Exception $e) {
    echo "<h1>Test Failed</h1>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
} 