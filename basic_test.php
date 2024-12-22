<?php
// Enable error display for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Basic Configuration Test</h1>";

try {
    // Test PHP Version
    echo "<p>PHP Version: " . phpversion() . "</p>";
    
    // Test file permissions
    echo "<p>Current directory: " . __DIR__ . "</p>";
    
    // Test if includes directory exists
    $includesDir = __DIR__ . '/includes';
    echo "<p>Includes directory exists: " . (file_exists($includesDir) ? 'Yes' : 'No') . "</p>";
    
    // List files in includes directory
    if (file_exists($includesDir)) {
        echo "<p>Files in includes directory:</p><ul>";
        foreach (scandir($includesDir) as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    }
    
    // Test if we can require files
    echo "<p>Testing file includes:</p>";
    
    // Try to include Config.php
    if (file_exists($includesDir . '/Config.php')) {
        require_once $includesDir . '/Config.php';
        echo "<p>Config.php included successfully</p>";
    } else {
        echo "<p>Config.php not found</p>";
    }
    
    // Check config directory
    $configDir = $includesDir . '/config';
    if (file_exists($configDir)) {
        echo "<p>Config directory exists</p>";
        echo "<p>Files in config directory:</p><ul>";
        foreach (scandir($configDir) as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>Config directory not found</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; margin: 10px; border: 1px solid red;'>";
    echo "<h2>Error Occurred:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "</div>";
} 