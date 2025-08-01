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
    } else {
        
        $username = $conn->real_escape_string($username);

        
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();
            
            
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['username'] = $username;
                header('Location: dashboard1.php'); 
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "User does not exist.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>