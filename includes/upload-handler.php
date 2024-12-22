<?php
class UploadHandler {
    private $uploadDir;
    private $allowedTypes;
    private $maxSize;
    private $errors = [];

    public function __construct($uploadDir = '../uploads/') {
        $this->uploadDir = $uploadDir;
        $this->allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $this->maxSize = 5 * 1024 * 1024; // 5MB

        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload($file, $customName = null) {
        if (!isset($file['error']) || is_array($file['error'])) {
            $this->errors[] = 'Invalid file parameters.';
            return false;
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->errors[] = 'File is too large.';
                return false;
            case UPLOAD_ERR_NO_FILE:
                $this->errors[] = 'No file was uploaded.';
                return false;
            default:
                $this->errors[] = 'Unknown error occurred.';
                return false;
        }

        if ($file['size'] > $this->maxSize) {
            $this->errors[] = 'File is too large. Maximum size is 5MB.';
            return false;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $this->allowedTypes)) {
            $this->errors[] = 'Invalid file type. Only JPG, PNG and GIF are allowed.';
            return false;
        }

        $extension = array_search($mimeType, [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ], true);

        $fileName = $customName ? $customName . '.' . $extension : 
                                uniqid() . '.' . $extension;
        $filePath = $this->uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            $this->errors[] = 'Failed to move uploaded file.';
            return false;
        }

        return $fileName;
    }

    public function getErrors() {
        return $this->errors;
    }
} 