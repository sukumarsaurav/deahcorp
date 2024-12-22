-- Create the database
CREATE DATABASE IF NOT EXISTS agency_website;
USE agency_website;

-- Services Table
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Portfolio Projects Table
CREATE TABLE portfolio_projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    client_name VARCHAR(100),
    project_date DATE,
    category VARCHAR(50),
    featured_image VARCHAR(255),
    project_url VARCHAR(255),
    technologies_used TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Project Images Table (for multiple images per project)
CREATE TABLE project_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT,
    image_url VARCHAR(255) NOT NULL,
    image_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE
);

-- Blog Posts Table
CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    featured_image VARCHAR(255),
    excerpt TEXT,
    author_id INT,
    status ENUM('draft', 'published') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blog Categories Table
CREATE TABLE blog_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Blog Posts Categories (Many-to-Many relationship)
CREATE TABLE post_categories (
    post_id INT,
    category_id INT,
    PRIMARY KEY (post_id, category_id),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE CASCADE
);

-- Team Members Table
CREATE TABLE team_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    bio TEXT,
    image VARCHAR(255),
    email VARCHAR(100),
    linkedin_url VARCHAR(255),
    twitter_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Testimonials Table
CREATE TABLE testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(100) NOT NULL,
    client_position VARCHAR(100),
    company_name VARCHAR(100),
    testimonial TEXT NOT NULL,
    client_image VARCHAR(255),
    rating INT CHECK (rating >= 1 AND rating <= 5),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact Messages Table
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table (for admin panel)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    role ENUM('admin', 'editor', 'author') DEFAULT 'author',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    reset_token VARCHAR(100) NULL,
    reset_expires TIMESTAMP NULL,
    last_password_change TIMESTAMP NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL
);

-- Insert sample data for Services
INSERT INTO services (title, description, icon) VALUES
('Website Design', 'Custom website design solutions tailored to your business needs', 'web-design'),
('UI/UX Design', 'User-centered design approach for optimal user experience', 'ux-design'),
('Digital Marketing', 'Comprehensive digital marketing strategies to grow your business', 'digital-marketing'),
('App Development', 'Mobile and web application development services', 'app-dev'),
('Branding', 'Complete branding and identity design services', 'branding'),
('SEO Services', 'Search engine optimization to improve your online visibility', 'seo');

-- Insert sample data for Blog Categories
INSERT INTO blog_categories (name, slug, description) VALUES
('Web Design', 'web-design', 'Articles about web design trends and best practices'),
('Digital Marketing', 'digital-marketing', 'Latest digital marketing strategies and tips'),
('Technology', 'technology', 'Technology news and updates'),
('Business', 'business', 'Business insights and growth strategies');

-- Insert sample data for Team Members
INSERT INTO team_members (name, position, bio, email) VALUES
('John Doe', 'CEO & Founder', 'Over 15 years of experience in digital solutions', 'john@example.com'),
('Jane Smith', 'Creative Director', 'Award-winning designer with global experience', 'jane@example.com'),
('Mike Johnson', 'Lead Developer', 'Full-stack developer with 10+ years experience', 'mike@example.com');

-- Insert sample admin user
INSERT INTO users (username, email, password, first_name, last_name, role) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin'); 

-- Add audit trail
CREATE TABLE audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Add sessions table for better session management
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    payload TEXT,
    last_activity INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Add indexes for better performance
ALTER TABLE blog_posts ADD INDEX idx_status_date (status, created_at);
ALTER TABLE portfolio_projects ADD INDEX idx_category (category);
ALTER TABLE contact_messages ADD INDEX idx_status_date (status, created_at); 