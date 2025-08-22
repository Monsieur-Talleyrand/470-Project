<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    
    header('Location: ../login.php');
    exit();
}


require_once __DIR__ . '/../db.php';



header('Location: view_categories.php?msg=Category+deleted');
exit();