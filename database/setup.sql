-- AidFlow Laravel Database Schema
-- Run this SQL script in your MySQL database

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create inventories table
CREATE TABLE IF NOT EXISTS inventories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    unit VARCHAR(255) NOT NULL,
    stock INT NOT NULL,
    received DATETIME NULL,
    expirationDate DATETIME NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create scan_events table
CREATE TABLE IF NOT EXISTS scan_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tent_code VARCHAR(255) NOT NULL,
    scanned_at DATETIME NOT NULL,
    barangay_code VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create occupied_tents table
CREATE TABLE IF NOT EXISTS occupied_tents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tent_code VARCHAR(255) NOT NULL,
    barangay_code VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create password_reset_tokens table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent LONGTEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create migrations table
CREATE TABLE IF NOT EXISTS migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create cache table
CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value LONGTEXT NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create jobs table
CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data
INSERT INTO users (name, username, email, password, role, created_at, updated_at) 
VALUES (
    'Admin User',
    'admin',
    'admin@aidflow.test',
    '$2y$12$H3JxYi5oK.hP8q8E/k3ae.PZWOKb0XBq4LjVfKfqhL8P8nY7HM6D6',
    'admin',
    NOW(),
    NOW()
);

INSERT INTO users (name, username, email, password, role, created_at, updated_at) 
VALUES (
    'Regular User',
    'user',
    'user@aidflow.test',
    '$2y$12$H3JxYi5oK.hP8q8E/k3ae.PZWOKb0XBq4LjVfKfqhL8P8nY7HM6D6',
    'user',
    NOW(),
    NOW()
);

-- Insert sample inventory items
INSERT INTO inventories (name, category, unit, stock, received, created_at, updated_at) VALUES
('Rice', 'Food', 'Kilo', 100, NOW(), NOW(), NOW()),
('Sardines', 'Food', 'Can', 200, NOW(), NOW(), NOW()),
('Noodles', 'Food', 'Pack', 150, NOW(), NOW(), NOW()),
('Water', 'Food', 'Bottle', 300, NOW(), NOW(), NOW()),
('Blanket', 'Non-Food', 'Piece', 50, NOW(), NOW(), NOW());
