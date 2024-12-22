<?php
class ErrorHandler {
    public static function register() {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    public static function handleError($level, $message, $file = '', $line = 0) {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }
    
    public static function handleException($exception) {
        if (Config::get('DEBUG')) {
            echo "<h1>Error</h1>";
            echo "<p>Message: " . self::sanitizeOutput($exception->getMessage()) . "</p>";
            echo "<p>File: " . self::sanitizeOutput($exception->getFile()) . "</p>";
            echo "<p>Line: " . $exception->getLine() . "</p>";
            echo "<h2>Stack Trace:</h2>";
            echo "<pre>" . self::sanitizeOutput($exception->getTraceAsString()) . "</pre>";
        } else {
            http_response_code(500);
            include 'templates/error500.php';
        }
        
        // Log error
        error_log($exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . 
                 $exception->getLine() . "\n" . $exception->getTraceAsString());
    }
    
    public static function handleShutdown() {
        $error = error_get_last();
        if ($error !== null && $error['type'] === E_ERROR) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
    
    private static function sanitizeOutput($output) {
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }
} 