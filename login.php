<?php
session_start();


if (isset($_SESSION['user_id'])) {
    header('Location: dashboard1.php');
    exit();
}


include 'db.php';

$errorMessage = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    
    if (empty($username) || empty($password)) {
        $errorMessage = "Username and password are required.";
    } else {
        
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            
            if (password_verify($password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['id']; 
                $_SESSION['username'] = $user['username'];
                header('Location: dashboard1.php'); 
                exit();
            } else {
                
                $errorMessage = "Invalid username or password.";
            }
        } else {
            
            $errorMessage = "Invalid username or password.";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Bill Reminder</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
            background-color: #f4f7f6; 
            color: #333; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container { 
            max-width: 400px; 
            width: 100%;
            padding: 40px; 
            background-color: #fff; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 { 
            color: #2c3e50; 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .error-message { 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px; 
            border: 1px solid #f5c6cb;
            color: #721c24; 
            background-color: #f8d7da; 
        }
        label { 
            font-weight: bold; 
            display: block; 
            margin-bottom: 8px; 
        }
        input[type="text"], input[type="password"] { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box;
            margin-bottom: 20px;
        }
        button { 
            background-color: #3498db; 
            color: white; 
            width: 100%;
            padding: 12px 20px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 1.1em;
            font-weight: bold;
        }
        button:hover { 
            background-color: #2980b9; 
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            color: #3498db;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login to Your Account</h2>
        
        <?php if (!empty($errorMessage)): ?>
            <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <div class="register-link">
            <p>Don't have an account? <a href="registration.php">Register here</a></p>
        </div>
    </div>

</body>
</html>