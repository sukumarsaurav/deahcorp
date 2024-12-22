<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get the absolute path to the includes directory
define('BASE_PATH', __DIR__);
define('INCLUDES_PATH', BASE_PATH . '/includes');

// Debug directory structure
echo "<h2>Directory Check:</h2>";
echo "BASE_PATH: " . BASE_PATH . "<br>";
echo "INCLUDES_PATH: " . INCLUDES_PATH . "<br>";
echo "Config.php exists: " . (file_exists(INCLUDES_PATH . '/Config.php') ? 'Yes' : 'No') . "<br>";
echo "Database.php exists: " . (file_exists(INCLUDES_PATH . '/Database.php') ? 'Yes' : 'No') . "<br>";

try {
    // Include required files with absolute paths
    require_once INCLUDES_PATH . '/Config.php';
    require_once INCLUDES_PATH . '/Database.php';
    
    echo "<h1>Configuration Test</h1>";
    
    // Test config loading
    echo "<h2>Config Loading:</h2>";
    Config::load();
    $debug = Config::debug();
    echo "<pre>";
    print_r($debug);
    echo "</pre>";
    
    // Test database connection
    echo "<h2>Database Connection:</h2>";
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Test query
    $result = $pdo->query("SELECT 1")->fetch();
    echo "<p style='color: green;'>Database connection successful!</p>";
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "<h2>Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "</div>";
} 