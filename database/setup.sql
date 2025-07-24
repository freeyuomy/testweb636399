-- Create database
CREATE DATABASE IF NOT EXISTS websocket_db;
USE websocket_db;

-- Create tables
CREATE TABLE IF NOT EXISTS connections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    connected_at DATETIME NOT NULL,
    disconnected_at DATETIME NULL,
    INDEX (client_id)
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at DATETIME NOT NULL,
    INDEX (client_id),
    INDEX (sent_at)
);

-- Create a user for the WebSocket application
-- Replace 'password' with a secure password
CREATE USER IF NOT EXISTS 'websocket_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON websocket_db.* TO 'websocket_user'@'localhost';
FLUSH PRIVILEGES; 