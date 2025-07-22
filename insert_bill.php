<?php
session_start();   
include 'db.php';


if (!isset($_SESSION['username'])) {   
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
    $due_date = isset($_POST['due_date']) ? trim($_POST['due_date']) : '';

    
    if (empty($title) || empty($amount) || empty($due_date)) {                  
        die("All fields are required. <a href='add_bill.php'>Go back</a>");
    }
    if (!is_numeric($amount)) {
        die("Amount must be a valid number. <a href='add_bill.php'>Go back</a>");
    }

    
    $stmt = $conn->prepare("INSERT INTO bills (title, amount, due_date) VALUES (?, ?, ?)");  
    $stmt->bind_param("sds", $title, $amount, $due_date);

    if ($stmt->execute()) {
        echo "Bill added successfully! <a href='add_bill.php'>Add another</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    
    header('Location: add_bill.php');
    exit();
}
?>
