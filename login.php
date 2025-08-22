<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bill_reminder470');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $errorMessage = "Username and password are required.";
    } else {
        
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");

        
        if ($stmt === false) {
            die("SQL Prepare Error: " . htmlspecialchars($conn->error));
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            
            $stmt->bind_result($id, $hashedPassword);
            $stmt->fetch();
            
            if (password_verify($password, $hashedPassword)) {
                
                $_SESSION['user_id'] = $id; 
                $_SESSION['username'] = $username;
                header('Location: dashboard1.php'); 
                exit();
            } else {
                $errorMessage = "Invalid credentials.";
            }
        } else {
            $errorMessage = "Invalid credentials.";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (!empty($errorMessage)): ?>
        <p style="color:red;"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>
    <form method="POST">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>

</html>
