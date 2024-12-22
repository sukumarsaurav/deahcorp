<?php
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        // Load configuration
        Config::load('production'); // or get environment from somewhere
        
        try {
            $this->pdo = new PDO(
                "mysql:host=" . Config::get('DB_HOST') . 
                ";dbname=" . Config::get('DB_NAME') . 
                ";charset=utf8mb4",
                Config::get('DB_USER'),
                Config::get('DB_PASS'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch(PDOException $e) {
            // Log error securely without exposing credentials
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Connection failed. Please try again later.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
} 