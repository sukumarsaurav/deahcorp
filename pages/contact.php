<?php
require_once '../includes/db.php';
$pageTitle = 'Contact Us';
include '../includes/header.php';

// Include form processor
require_once '../includes/process-contact.php';
?>

<section class="contact-hero">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Get in touch with our team</p>
    </div>
</section>

<section class="contact-content">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <div class="info-item">
                    <i class="icon-location"></i>
                    <p>123 Business Street<br>City, State 12345</p>
                </div>
                <div class="info-item">
                    <i class="icon-phone"></i>
                    <p>(123) 456-7890</p>
                </div>
                <div class="info-item">
                    <i class="icon-email"></i>
                    <p>info@digitalagency.com</p>
                </div>
            </div>
            
            <div class="contact-form">
                <?php if (isset($response)): ?>
                    <div class="message <?php echo $response['success'] ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($response['message']); ?>
                    </div>
                <?php endif; ?>
                
                <form id="contact-form" method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                               class="<?php echo isset($response['errors']['name']) ? 'error' : ''; ?>"
                               required>
                        <?php if (isset($response['errors']['name'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($response['errors']['name']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               class="<?php echo isset($response['errors']['email']) ? 'error' : ''; ?>"
                               required>
                        <?php if (isset($response['errors']['email'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($response['errors']['email']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" 
                               value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>"
                               class="<?php echo isset($response['errors']['subject']) ? 'error' : ''; ?>"
                               required>
                        <?php if (isset($response['errors']['subject'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($response['errors']['subject']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" 
                                  class="<?php echo isset($response['errors']['message']) ? 'error' : ''; ?>"
                                  required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        <?php if (isset($response['errors']['message'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($response['errors']['message']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 