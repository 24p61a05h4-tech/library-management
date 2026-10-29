CREATE DATABASE IF NOT EXISTS library_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_db;

CREATE TABLE IF NOT EXISTS books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  author VARCHAR(120) NOT NULL,
  category VARCHAR(80) NOT NULL,
  isbn VARCHAR(30) DEFAULT '',
  status ENUM('Available','Issued') NOT NULL DEFAULT 'Available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS book_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT IGNORE INTO book_categories (name) VALUES
('Computer Science'), ('Programming'), ('Data Structures & Algorithms'),
('Database Management'), ('Networking'), ('Mathematics'), ('Electronics'), ('General');
CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS student_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT IGNORE INTO book_categories (name) SELECT DISTINCT category FROM books WHERE category <> '';


-- Admin invitations: only a SHA-256 hash is stored; each invite is single-use.
CREATE TABLE IF NOT EXISTS admin_invites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code_hash CHAR(64) NOT NULL UNIQUE,
  created_by INT NOT NULL,
  created_by_username VARCHAR(80) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  used_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_admin_invites_status (used_at, expires_at)
);
