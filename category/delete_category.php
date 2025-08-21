<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: view_categories.php?msg=Category+deleted');
    } else {
        header('Location: view_categories.php?msg=Error+deleting+category');
    }
    exit();
} else {
    header('Location: view_categories.php?msg=Invalid+ID');
    exit();
}
?>