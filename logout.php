<?php
session_start(); // Clear all session variables
session_destroy(); // Destroy the session
header('Location: login.php'); // Destroy the session
exit(); // Destroy the session
?>