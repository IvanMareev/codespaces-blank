CREATE DATABASE IF NOT EXISTS gallery_db;

USE gallery_db;

CREATE TABLE IF NOT EXISTS images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    s3_url TEXT NOT NULL
);

CREATE INDEX idx_uploaded_at ON images (uploaded_at DESC);