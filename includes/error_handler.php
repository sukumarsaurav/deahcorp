<?php
// Error reporting settings
error_reporting(E_ALL);

// Load configuration
require_once __DIR__ . '/Config.php';
Config::load();

// Set error display based on environment
$isProduction = Config::get('APP_ENV') === 'production';
ini_set('display_errors', $isProduction ? 0 : 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error_message = date('[Y-m-d H:i:s] ') . "Error: [$errno] $errstr - $errfile:$errline\n";
    error_log($error_message);
    
    global $isProduction;
    
    if (!$isProduction) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px;'>";
        echo "<h3>Error Details:</h3>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($errstr) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($errfile) . "</p>";
        echo "<p><strong>Line:</strong> " . $errline . "</p>";
        echo "</div>";
        return true;
    }
    
    // Redirect to error page for serious errors in production
    if (in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        header('Location: /error500.php');
        exit;
    }
    
    return true;
}

// Custom exception handler
function customExceptionHandler($exception) {
    $error_message = date('[Y-m-d H:i:s] ') . 'Exception: ' . $exception->getMessage() . 
                    ' in ' . $exception->getFile() . ':' . $exception->getLine() . "\n";
    error_log($error_message);
    
    global $isProduction;
    
    if (!$isProduction) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px;'>";
        echo "<h3>Exception Details:</h3>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . "</p>";
        echo "<p><strong>Line:</strong> " . $exception->getLine() . "</p>";
        echo "<p><strong>Trace:</strong></p>";
        echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        header('Location: /error500.php');
    }
    exit;
}

// Register error handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

// Test write permissions
if (!is_writable($logDir)) {
    error_log("Warning: Logs directory is not writable: $logDir");
} 