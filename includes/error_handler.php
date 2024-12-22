<?php
// Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors directly to users
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error_message = date('[Y-m-d H:i:s] ') . "Error: [$errno] $errstr - $errfile:$errline\n";
    error_log($error_message);
    
    // Redirect to error page for serious errors
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
    
    header('Location: /error500.php');
    exit;
}

// Register error handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// Create logs directory if it doesn't exist
if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
} 