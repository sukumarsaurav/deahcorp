<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Configuration Verification</h1>";

try {
    // Step 1: Check file existence and permissions
    echo "<h2>File System Check:</h2>";
    $files = [
        'includes/Config.php',
        'includes/config/config.php',
        'includes/config/production.php'
    ];
    
    foreach ($files as $file) {
        echo "<p>File: $file<br>";
        echo "Exists: " . (file_exists($file) ? 'Yes' : 'No') . "<br>";
        if (file_exists($file)) {
            echo "Permissions: " . substr(sprintf('%o', fileperms($file)), -4) . "<br>";
            echo "Readable: " . (is_readable($file) ? 'Yes' : 'No') . "</p>";
        }
    }

    // Step 2: Test Config class loading
    echo "<h2>Config Class Test:</h2>";
    require_once 'includes/Config.php';
    echo "<p>Config class loaded successfully</p>";

    // Step 3: Test configuration loading
    echo "<h2>Configuration Loading Test:</h2>";
    Config::load();
    echo "<p>Configuration loaded successfully</p>";

    // Step 4: Test configuration values
    echo "<h2>Configuration Values:</h2>";
    echo "<pre>";
    echo "DB_HOST: " . Config::get('DB_HOST') . "\n";
    echo "DB_NAME: " . Config::get('DB_NAME') . "\n";
    echo "DB_USER: " . Config::get('DB_USER') . "\n";
    echo "APP_NAME: " . Config::get('APP_NAME') . "\n";
    echo "APP_ENV: " . Config::get('APP_ENV') . "\n";
    echo "</pre>";

    // Step 5: Test database connection
    echo "<h2>Database Connection Test:</h2>";
    $dsn = sprintf(
        "mysql:host=%s;dbname=%s;charset=utf8mb4",
        Config::get('DB_HOST'),
        Config::get('DB_NAME')
    );
    
    $pdo = new PDO(
        $dsn,
        Config::get('DB_USER'),
        Config::get('DB_PASS'),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<p style='color: green;'>Database connection successful!</p>";

} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "<h3>Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "</div>";
} 