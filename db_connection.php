<?php
// --- Database Configuration (XAMPP Default) ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Default XAMPP username
define('DB_PASS', '');          // Default XAMPP password is empty
define('DB_NAME', 'evoting_db');

// --- Shared Database Connection ---
try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    
    // Set PDO error mode to exception for easier debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Stop execution and display an error message if the connection fails
    die("Database connection failed. Please ensure XAMPP MySQL is running. Error: " . $e->getMessage());
}
?>