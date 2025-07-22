<?php
session_start();                             

$conn = new mysqli('localhost', 'root', '', 'bill_reminder470');


if ($conn->connect_error) {                                 
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    
    if (empty($username) || empty($password)) {           
        echo "Username and password are required.";
    } elseif (strlen($username) < 3 || strlen($password) < 6) {
        echo "Username must be at least 3 characters and password at least 6 characters.";
    } else {
        
        $username = $conn->real_escape_string($username);                  
        $password = password_hash($password, PASSWORD_BCRYPT);

        
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";   
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['username'] = $username;
            header("Location: dashboard1.php");
            exit(); 
        } else {
            echo "Error: " . $conn->error; 
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form method="POST">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>