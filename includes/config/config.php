<?php
// Base configuration
return [
    'APP_NAME' => 'Digital Agency',
    'APP_ENV' => 'production',
    'APP_DEBUG' => false,
    'APP_URL' => 'https://yourdomain.com',
    
    // Default settings
    'UPLOAD_MAX_SIZE' => 5242880,
    'ALLOWED_FILE_TYPES' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
    'SESSION_LIFETIME' => 120,
    'MAX_LOGIN_ATTEMPTS' => 5
]; 