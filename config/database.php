<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'bolton_university';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }

    public function createDatabase() {
        try {
            $conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);
            
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            
            // Create users table
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                user_type ENUM('student', 'teacher') NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->conn->exec($sql);
            
            // Create motivation_logs table
            $sql = "CREATE TABLE IF NOT EXISTS motivation_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                motivation_level INT NOT NULL,
                log_date DATE NOT NULL,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                UNIQUE KEY unique_user_date (user_id, log_date)
            )";
            $this->conn->exec($sql);
            
            // Create messages table
            $sql = "CREATE TABLE IF NOT EXISTS messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sender_id INT NOT NULL,
                receiver_id INT NOT NULL,
                message TEXT NOT NULL,
                sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_read BOOLEAN DEFAULT FALSE,
                FOREIGN KEY (sender_id) REFERENCES users(id),
                FOREIGN KEY (receiver_id) REFERENCES users(id)
            )";
            $this->conn->exec($sql);
            
            // Create events table
            $sql = "CREATE TABLE IF NOT EXISTS events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                event_date DATE NOT NULL,
                event_time TIME NOT NULL,
                location VARCHAR(255) NOT NULL,
                max_participants INT DEFAULT NULL,
                created_by INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (created_by) REFERENCES users(id)
            )";
            $this->conn->exec($sql);
            
            // Create event_registrations table
            $sql = "CREATE TABLE IF NOT EXISTS event_registrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                user_id INT NOT NULL,
                registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('registered', 'attended', 'cancelled') DEFAULT 'registered',
                FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id),
                UNIQUE KEY unique_event_user (event_id, user_id)
            )";
            $this->conn->exec($sql);
            
        } catch(PDOException $exception) {
            echo "Database creation error: " . $exception->getMessage();
        }
    }
}
?>