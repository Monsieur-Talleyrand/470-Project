<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include 'db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    
    $stmt = $conn->prepare("DELETE FROM bills WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: view_bills.php");
        exit();
    } else {
        echo "Error deleting bill: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No valid ID provided!";
}

$conn->close();
?>