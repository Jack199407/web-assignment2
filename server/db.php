<?php
/*
Name: Gao Miaomiao 041135845
Created Date: 2024-11-21
db.php: Database connection management
*/

// Database configuration
$host = 'localhost';       // Database host
$username = 'root';        // Database username
$password = '';            // Database password
$database = 'taskmanagement'; // Database name

try {
    // Create a new PDO instance
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    
    // Set PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Enable prepared statements to prevent SQL injection
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // Display success message for debugging
    // echo "Connected to the database successfully!";
} catch (PDOException $e) {
    // Handle connection error
    die("Database connection failed: " . $e->getMessage());
}
?>
