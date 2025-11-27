-- Resource Library CMS Database Setup
-- Create database and tables for the CMS system

-- Create database
CREATE DATABASE IF NOT EXISTS resource_library_cms;
USE resource_library_cms;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Categories table for resource organization
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Resources table for storing resource information
CREATE TABLE resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    resource_type ENUM('document', 'link', 'video', 'tutorial', 'other') NOT NULL,
    file_path VARCHAR(500), -- For uploaded files
    external_url VARCHAR(500), -- For external links
    file_size INT, -- File size in bytes
    mime_type VARCHAR(100), -- MIME type for uploaded files
    category_id INT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO categories (name, description) VALUES
('Documents', 'PDF files, Word documents, and other text-based resources'),
('Videos', 'Video tutorials, presentations, and multimedia content'),
('Links', 'External websites, articles, and online resources'),
('Tutorials', 'Step-by-step guides and educational content'),
('Software', 'Applications, tools, and software resources'),
('Research', 'Academic papers, studies, and research materials');

-- Create indexes for better performance
CREATE INDEX idx_resources_category ON resources(category_id);
CREATE INDEX idx_resources_user ON resources(user_id);
CREATE INDEX idx_resources_type ON resources(resource_type);
CREATE INDEX idx_resources_title ON resources(title);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);

-- Create a default admin user (password: admin123)
-- Password hash for 'admin123' using PHP password_hash()
INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES
('admin', 'admin@example.com', '$2y$10$/7QOzJkezudc6GZniCoWX.M4rNHLolnP.gXEBWrJsGSz82PGqnZji', 'Admin', 'User');

