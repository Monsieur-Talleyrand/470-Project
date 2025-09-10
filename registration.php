<?php
session_start();


if (isset($_SESSION['user_id'])) {
    header('Location: dashboard1.php');
    exit();
}

include 'db.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    
    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    } elseif (strlen($username) < 3 || strlen($password) < 6) {
        $error_message = "Username must be at least 3 characters and password at least 6 characters.";
    } else {
        
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_message = "This username is already taken. Please choose another.";
        } else {
            
            $password_hashed = password_hash($password, PASSWORD_DEFAULT); 

            $stmt_insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt_insert->bind_param("ss", $username, $password_hashed);
            
            if ($stmt_insert->execute()) {
                
                $new_user_id = $conn->insert_id;
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['username'] = $username;
                
                header("Location: dashboard1.php");
                exit();
            } else {
                $error_message = "Registration failed. Please try again later.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Bill Reminder</title>
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
        .register-container { 
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
            background-color: #27ae60; 
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
            background-color: #229954; 
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #3498db;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h2>Create Your Account</h2>
        
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
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
            <button type="submit">Register</button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

</body>
</html>