<?php

ini_set('memory_limit', '1024M'); 


$host = 'localhost';         
$user = 'root';              
$password = '';              
$database = 'bill_reminder470';


$conn = new mysqli($host, $user, $password, $database); 


if ($conn->connect_error) {                                     
    die("Connection failed: " . $conn->connect_error);
}
?>