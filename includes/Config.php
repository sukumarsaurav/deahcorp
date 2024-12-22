<?php
class Config {
    private static $config = [];
    private static $loaded = false;
    
    public static function load($env = 'production') {
        if (self::$loaded) {
            return;
        }
        
        try {
            // Define the base path
            $basePath = __DIR__;
            
            // Load base config
            $configFile = $basePath . '/config/config.php';
            if (!file_exists($configFile)) {
                throw new Exception("Base config file not found at: $configFile");
            }
            self::$config = require $configFile;
            
            // Load environment specific config
            $envConfig = $basePath . '/config/production.php';
            if (file_exists($envConfig)) {
                $envSettings = require $envConfig;
                if (is_array($envSettings)) {
                    self::$config = array_merge(self::$config, $envSettings);
                } else {
                    throw new Exception("Invalid configuration format in production.php");
                }
            } else {
                throw new Exception("Production config file not found at: $envConfig");
            }
            
            self::$loaded = true;
            
        } catch (Exception $e) {
            // Log the error
            error_log("Configuration Error: " . $e->getMessage());
            throw $e; // Re-throw to be handled by the application's error handler
        }
    }
    
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }
        
        // Support nested keys using dot notation (e.g., 'database.host')
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $value = self::$config;
            
            foreach ($keys as $k) {
                if (!isset($value[$k])) {
                    return $default;
                }
                $value = $value[$k];
            }
            
            return $value;
        }
        
        return self::$config[$key] ?? $default;
    }
    
    public static function all() {
        if (!self::$loaded) {
            self::load();
        }
        return self::$config;
    }
    
    public static function has($key) {
        if (!self::$loaded) {
            self::load();
        }
        return isset(self::$config[$key]);
    }
    
    // Debug method to check configuration status
    public static function debug() {
        return [
            'loaded' => self::$loaded,
            'config' => self::$config,
            'base_path' => __DIR__,
            'files' => [
                'config' => file_exists(__DIR__ . '/config/config.php'),
                'production' => file_exists(__DIR__ . '/config/production.php')
            ],
            'paths' => [
                'config' => __DIR__ . '/config/config.php',
                'production' => __DIR__ . '/config/production.php'
            ]
        ];
    }
    
    // Prevent direct instantiation
    private function __construct() {}
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
