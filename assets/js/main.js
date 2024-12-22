document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.querySelector('.nav-menu');
    
    menuToggle?.addEventListener('click', function() {
        this.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-container')) {
            menuToggle?.classList.remove('active');
            navMenu.classList.remove('active');
        }
    });

    // Header scroll effect
    const header = document.querySelector('.header');
    const scrollThreshold = 50;

    window.addEventListener('scroll', function() {
        if (window.scrollY > scrollThreshold) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                // Close mobile menu after clicking
                menuToggle?.classList.remove('active');
                navMenu.classList.remove('active');
            }
        });
    });

    // Contact Form AJAX submission
    const contactForm = document.querySelector('#contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach(msg => msg.remove());
            document.querySelectorAll('.error').forEach(field => field.classList.remove('error'));
            
            // Get form data
            const formData = new FormData(this);
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Sending...';
            
            // Send AJAX request
            fetch('/includes/process-contact.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Remove any existing message
                const existingMessage = document.querySelector('.message');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Create new message element
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${data.success ? 'success' : 'error'}`;
                messageDiv.textContent = data.message;
                
                // Insert message before form
                contactForm.insertBefore(messageDiv, contactForm.firstChild);
                
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.querySelector(`#${field}`);
                        if (input) {
                            input.classList.add('error');
                            const errorSpan = document.createElement('span');
                            errorSpan.className = 'error-message';
                            errorSpan.textContent = data.errors[field];
                            input.parentNode.appendChild(errorSpan);
                        }
                    });
                }
                
                // If successful, reset form
                if (data.success) {
                    contactForm.reset();
                    
                    // Scroll to message
                    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(error => {
                // Handle network errors
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message error';
                messageDiv.textContent = 'An error occurred. Please try again later.';
                contactForm.insertBefore(messageDiv, contactForm.firstChild);
            })
            .finally(() => {
                // Restore button state
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            });
        });

        // Real-time validation
        contactForm.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this);
            });
        });
    }
});

// Field validation function
function validateField(field) {
    const errorMessage = field.parentNode.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
    field.classList.remove('error');

    let error = '';
    
    switch(field.id) {
        case 'name':
            if (!field.value.trim()) {
                error = 'Name is required';
            } else if (field.value.length < 2) {
                error = 'Name must be at least 2 characters long';
            }
            break;
            
        case 'email':
            if (!field.value.trim()) {
                error = 'Email is required';
            } else if (!isValidEmail(field.value)) {
                error = 'Please enter a valid email address';
            }
            break;
            
        case 'subject':
            if (!field.value.trim()) {
                error = 'Subject is required';
            }
            break;
            
        case 'message':
            if (!field.value.trim()) {
                error = 'Message is required';
            } else if (field.value.length < 10) {
                error = 'Message must be at least 10 characters long';
            }
            break;
    }

    if (error) {
        field.classList.add('error');
        const errorSpan = document.createElement('span');
        errorSpan.className = 'error-message';
        errorSpan.textContent = error;
        field.parentNode.appendChild(errorSpan);
        return false;
    }
    
    return true;
}

// Email validation helper
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
} 