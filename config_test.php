<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Config Test</h1>";

try {
    echo "<p>Including Config.php...</p>";
    require_once 'includes/Config.php';
    
    echo "<p>Loading configuration...</p>";
    Config::load();
    
    echo "<p>Testing configuration values:</p>";
    echo "<pre>";
    echo "DB_HOST: " . Config::get('DB_HOST') . "\n";
    echo "DB_NAME: " . Config::get('DB_NAME') . "\n";
    echo "APP_NAME: " . Config::get('APP_NAME') . "\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "<h2>Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "</div>";
} 