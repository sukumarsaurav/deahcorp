<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>File Upload Check</h1>";

// Define the files to create
$files = [
    'includes/Config.php' => '<?php
class Config {
    private static $config = [];
    private static $loaded = false;
    
    public static function load($env = \'production\') {
        if (self::$loaded) {
            return;
        }
        
        // Define the base path
        $basePath = __DIR__;
        
        // Load base config
        $configFile = $basePath . \'/config/config.php\';
        if (!file_exists($configFile)) {
            throw new Exception("Base config file not found: $configFile");
        }
        self::$config = require $configFile;
        
        // Load environment specific config
        $envConfig = $basePath . \'/config/production.php\';
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
}',

    'includes/config/config.php' => '<?php
return [
    "APP_NAME" => "Digital Agency",
    "APP_ENV" => "production",
    "APP_DEBUG" => false,
    "APP_URL" => "https://yourdomain.com",
    
    "UPLOAD_MAX_SIZE" => 5242880,
    "ALLOWED_FILE_TYPES" => ["jpg", "jpeg", "png", "gif", "pdf"],
    "SESSION_LIFETIME" => 120,
    "MAX_LOGIN_ATTEMPTS" => 5
];',

    'includes/config/production.php' => '<?php
return [
    "DB_HOST" => "localhost",
    "DB_NAME" => "u911550082_agency_website",
    "DB_USER" => "u911550082_rrot",
    "DB_PASS" => "*nB6!E[S",
    
    "APP_ENV" => "production",
    "APP_DEBUG" => false,
    "APP_URL" => "https://mediumseagreen-hare-603866.hostingersite.com"
];'
];

// Create directories if they don't exist
$directories = [
    'includes',
    'includes/config'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p style='color: green;'>Created directory: $dir</p>";
        } else {
            echo "<p style='color: red;'>Failed to create directory: $dir</p>";
        }
    } else {
        echo "<p>Directory exists: $dir</p>";
    }
}

// Create files
foreach ($files as $path => $content) {
    if (file_put_contents($path, $content) !== false) {
        echo "<p style='color: green;'>Created file: $path</p>";
    } else {
        echo "<p style='color: red;'>Failed to create file: $path</p>";
    }
}

// Check permissions
foreach ($files as $path => $content) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "<p>File permissions for $path: $perms</p>";
    }
}

// Verify file contents
foreach ($files as $path => $content) {
    if (file_exists($path)) {
        $fileContent = file_get_contents($path);
        if ($fileContent === $content) {
            echo "<p style='color: green;'>File content verified: $path</p>";
        } else {
            echo "<p style='color: red;'>File content mismatch: $path</p>";
        }
    }
} 