<?php
require_once 'db.php';

function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = [
        'success' => false,
        'message' => '',
        'errors' => []
    ];

    // Validate inputs
    $name = validateInput($_POST['name'] ?? '');
    $email = validateInput($_POST['email'] ?? '');
    $phone = validateInput($_POST['phone'] ?? '');
    $subject = validateInput($_POST['subject'] ?? '');
    $message = validateInput($_POST['message'] ?? '');

    // Validation checks
    if (empty($name)) {
        $response['errors']['name'] = 'Name is required';
    } elseif (strlen($name) < 2) {
        $response['errors']['name'] = 'Name must be at least 2 characters long';
    }

    if (empty($email)) {
        $response['errors']['email'] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $response['errors']['email'] = 'Please enter a valid email address';
    }

    if (empty($subject)) {
        $response['errors']['subject'] = 'Subject is required';
    }

    if (empty($message)) {
        $response['errors']['message'] = 'Message is required';
    } elseif (strlen($message) < 10) {
        $response['errors']['message'] = 'Message must be at least 10 characters long';
    }

    // If no validation errors, process the form
    if (empty($response['errors'])) {
        try {
            // Insert into database
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, phone, subject, message) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$name, $email, $phone, $subject, $message]);

            // Send email notification
            $to = "admin@digitalagency.com"; // Replace with your email
            $emailSubject = "New Contact Form Submission: " . $subject;
            $emailMessage = "
                New contact form submission received:\n\n
                Name: $name\n
                Email: $email\n
                Phone: $phone\n
                Subject: $subject\n\n
                Message:\n$message
            ";
            $headers = "From: $email";

            mail($to, $emailSubject, $emailMessage, $headers);

            $response['success'] = true;
            $response['message'] = 'Thank you for your message. We will get back to you soon!';
        } catch (PDOException $e) {
            $response['message'] = 'Sorry, there was an error sending your message. Please try again later.';
        }
    } else {
        $response['message'] = 'Please correct the errors in the form.';
    }

    // Return JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
} 