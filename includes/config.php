<?php
class Config {
    private static $config = [];
    
    public static function load($env = 'production') {
        // Load base config
        self::$config = require __DIR__ . '/config/config.php';
        
        // Load environment specific config
        $envConfig = __DIR__ . '/config/' . $env . '.php';
        if (file_exists($envConfig)) {
            self::$config = array_merge(self::$config, require $envConfig);
        }
    }
    
    public static function get($key, $default = null) {
        return self::$config[$key] ?? $default;
    }
}
 