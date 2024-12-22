<?php
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            // Ensure config is loaded
            if (!Config::get('DB_HOST')) {
                throw new Exception("Database configuration not loaded");
            }
            
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=utf8mb4",
                Config::get('DB_HOST'),
                Config::get('DB_NAME')
            );
            
            $this->pdo = new PDO(
                $dsn,
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
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Please check your configuration.");
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