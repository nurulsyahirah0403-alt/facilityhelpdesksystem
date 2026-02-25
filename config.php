<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = getenv("MYSQL_HOST") ?: (getenv("MYSQLHOST") ?: "127.0.0.1");
$user = getenv("MYSQL_USER") ?: (getenv("MYSQLUSER") ?: "root");
$pass = getenv("MYSQL_PASSWORD") ?: (getenv("MYSQLPASSWORD") ?: "");
$db   = getenv("MYSQL_DATABASE") ?: (getenv("MYSQLDATABASE") ?: "helpdesk_db");
$port = getenv("MYSQL_PORT") ?: (getenv("MYSQLPORT") ?: 3306);

try {
    $conn = @mysqli_connect($host, $user, $pass, $db, $port);
    
    // Auto-create database if "Unknown database"
    if (!$conn && strpos(mysqli_connect_error(), 'Unknown database') !== false) {
        $conn_nodb = @mysqli_connect($host, $user, $pass, '', $port);
        if ($conn_nodb) {
            mysqli_query($conn_nodb, "CREATE DATABASE IF NOT EXISTS `$db`");
            mysqli_close($conn_nodb);
            $conn = @mysqli_connect($host, $user, $pass, $db, $port);
        }
    }

    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }

    // Auto-create tables if missing
    $tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
    if (mysqli_num_rows($tableCheck) == 0) {
        mysqli_query($conn, "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'technician', 'user') NOT NULL
        )");
        mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('Admin', 'admin@admin.com', 'admin123', 'admin')");
        
        mysqli_query($conn, "CREATE TABLE tickets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            location VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'Open',
            image VARCHAR(255) NULL,
            assigned_to INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (assigned_to) REFERENCES users(id)
        )");
        
        mysqli_query($conn, "CREATE TABLE ticket_updates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ticket_id INT NOT NULL,
            update_text TEXT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (ticket_id) REFERENCES tickets(id)
        )");
    }
} catch (Exception $e) {
    echo "<h1>Database Connection Error</h1>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> " . htmlspecialchars($host) . "</li>";
    echo "<li><strong>User:</strong> " . htmlspecialchars($user) . "</li>";
    echo "<li><strong>Database:</strong> " . htmlspecialchars($db) . "</li>";
    echo "<li><strong>Port:</strong> " . htmlspecialchars($port) . "</li>";
    echo "</ul>";
    die();
}
?>
