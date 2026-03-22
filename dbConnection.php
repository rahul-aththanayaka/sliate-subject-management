<?php
// Database Configuration
// Note: Using 'root' user for development (XAMPP default)
// For production environment, create dedicated user with limited privileges

$server = "localhost";
$username = "root";        // XAMPP default user
$password = "";            // XAMPP default password (empty)
$database = "webapp";

// Create database connection
$con = mysqli_connect($server, $username, $password, $database);

// Error handling - check if connection successful
if(!$con){
   die("Database Connection Failed: " . mysqli_connect_error());
}

// Optional: Set character set to UTF-8 for proper encoding
mysqli_set_charset($con, "utf8mb4");
?>