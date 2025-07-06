-- E-commerce Database Schema
-- Database: ecommerce_db

CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    category_id INT,
    image_url VARCHAR(255),
    brand VARCHAR(100),
    model VARCHAR(100),
    specifications JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    zip_code VARCHAR(20),
    country VARCHAR(50),
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    billing_address TEXT,
    payment_method VARCHAR(50),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Cart table (for guest users)
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Contact Messages Table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    status ENUM('new', 'in_progress', 'resolved') DEFAULT 'new'
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Laptops', 'High-performance laptops for work and gaming'),
('Headphones', 'Professional and gaming headphones'),
('Microphones', 'USB and XLR microphones for streaming and recording'),
('Keyboards', 'Mechanical and membrane keyboards'),
('Mouse', 'Gaming and office mouse');

-- Insert sample products
INSERT INTO products (name, description, price, stock_quantity, category_id, brand, model, image_url) VALUES
-- Laptops
('Gaming Laptop Pro', 'High-performance gaming laptop with RTX 4070, 16GB RAM, 1TB SSD', 1299.99, 15, 1, 'GamingTech', 'GT-15X', 'laptop1.jpg'),
('Business Ultrabook', 'Lightweight business laptop with 14" display, 8GB RAM, 512GB SSD', 899.99, 25, 1, 'BusinessPro', 'BU-14S', 'laptop2.jpg'),
('Student Laptop', 'Affordable laptop for students with 15.6" display, 8GB RAM, 256GB SSD', 499.99, 30, 1, 'StudentTech', 'ST-15A', 'laptop3.jpg'),

-- Headphones
('Wireless Gaming Headset', '7.1 surround sound, noise-cancelling microphone, 30-hour battery', 129.99, 40, 2, 'AudioPro', 'WH-2000', 'headphones1.jpg'),
('Studio Monitor Headphones', 'Professional studio headphones with flat frequency response', 199.99, 20, 2, 'StudioTech', 'SM-100', 'headphones2.jpg'),
('Bluetooth Headphones', 'Wireless headphones with active noise cancellation', 89.99, 35, 2, 'WirelessAudio', 'BT-500', 'headphones3.jpg'),

-- Microphones
('USB Streaming Microphone', 'Professional USB microphone with RGB lighting', 79.99, 50, 3, 'StreamTech', 'USB-100', 'microphone1.jpg'),
('XLR Studio Microphone', 'Professional XLR microphone for studio recording', 149.99, 15, 3, 'StudioPro', 'XLR-200', 'microphone2.jpg'),
('Wireless Lavalier Mic', 'Wireless lavalier microphone for presentations', 59.99, 30, 3, 'WirelessMic', 'WL-50', 'microphone3.jpg'),

-- Keyboards
('Mechanical Gaming Keyboard', 'RGB mechanical keyboard with Cherry MX switches', 129.99, 45, 4, 'MechTech', 'MK-100', 'keyboard1.jpg'),
('Wireless Office Keyboard', 'Silent wireless keyboard for office use', 49.99, 60, 4, 'OfficePro', 'WK-200', 'keyboard2.jpg'),
('Compact Mechanical Keyboard', '60% mechanical keyboard for gaming', 89.99, 25, 4, 'CompactTech', 'CM-60', 'keyboard3.jpg'),

-- Mice
('Gaming Mouse Pro', 'High-DPI gaming mouse with RGB lighting', 69.99, 55, 5, 'GamingTech', 'GM-100', 'mouse1.jpg'),
('Wireless Office Mouse', 'Ergonomic wireless mouse for office use', 29.99, 70, 5, 'OfficePro', 'WM-200', 'mouse2.jpg'),
('MMO Gaming Mouse', 'Multi-button MMO gaming mouse', 79.99, 20, 5, 'MMOTech', 'MM-300', 'mouse3.jpg');

-- Insert admin user (password: admin123)
INSERT INTO users (username, email, password_hash, first_name, last_name, is_admin) VALUES
('admin', 'admin@ecommerce.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', TRUE); 