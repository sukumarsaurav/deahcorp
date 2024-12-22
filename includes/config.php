<?php
class Config {
    private static $config = [];
    private static $loaded = false;
    
    public static function load($env = 'production') {
        if (self::$loaded) {
            return;
        }
        
        // Define the base path
        $basePath = __DIR__;
        
        // Load base config
        $configFile = $basePath . '/config/config.php';
        if (!file_exists($configFile)) {
            throw new Exception("Base config file not found: $configFile");
        }
        self::$config = require $configFile;
        
        // Load environment specific config
        $envConfig = $basePath . '/config/production.php';
        if (file_exists($envConfig)) {
            $envSettings = require $envConfig;
            self::$config = array_merge(self::$config, $envSettings);
        } else {
            throw new Exception("Production config file not found: $envConfig");
        }
        
        self::$loaded = true;
    }
    
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }
        return self::$config[$key] ?? $default;
    }
    
    // Debug method to check configuration
    public static function debug() {
        return [
            'loaded' => self::$loaded,
            'config' => self::$config,
            'base_path' => __DIR__,
            'files' => [
                'config' => file_exists(__DIR__ . '/config/config.php'),
                'production' => file_exists(__DIR__ . '/config/production.php')
            ]
        ];
    }
}
 