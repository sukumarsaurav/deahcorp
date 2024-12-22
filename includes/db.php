<?php
require_once __DIR__ . '/Config.php';

try {
    // Ensure config is loaded
    Config::load();
    
    $pdo = new PDO(
        "mysql:host=" . Config::get('DB_HOST') . ";dbname=" . Config::get('DB_NAME'),
        Config::get('DB_USER'),
        Config::get('DB_PASS'),
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
} 