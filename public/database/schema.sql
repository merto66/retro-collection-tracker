-- Create database
CREATE DATABASE IF NOT EXISTS retro_koleksiyon;
USE retro_koleksiyon;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user'
);

-- Create items table
CREATE TABLE IF NOT EXISTS items (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    estimated_value DECIMAL(10, 2) NOT NULL,
    user_id INT NOT NULL,
    image_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, password, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample items (with user_id)
INSERT INTO items (item_name, category, description, estimated_value, user_id) VALUES
    ('Vintage Vinyl Record Player', 'Electronics', '1950s turntable in excellent working condition', 450.00, 1),
    ('Retro Typewriter', 'Office Equipment', '1960s manual typewriter, fully functional', 320.00, 1),
    ('Classic Camera Collection', 'Photography', 'Set of 3 vintage cameras from 1970s', 850.00, 1);


