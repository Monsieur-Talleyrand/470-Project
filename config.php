<?php
// Start session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection settings
$host = "localhost";      // MySQL server host
$user = "root";           // MySQL username (XAMPP default)
$pass = "";               // MySQL password (XAMPP default has no password)
$dbname = "bill_reminder470"; // Your actual database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection and handle errors
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optionally, set the character set to UTF-8
$conn->set_charset("utf8");
?>
