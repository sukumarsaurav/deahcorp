<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/Config.php';
require_once 'includes/Database.php';

try {
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