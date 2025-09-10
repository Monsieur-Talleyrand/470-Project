<?php
session_start();


if (isset($_SESSION['user_id'])) {
    header('Location: dashboard1.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Bill Reminder</title>
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
            text-align: center;
        }
        .welcome-container { 
            max-width: 500px; 
            padding: 40px; 
            background-color: #fff; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 { 
            color: #2c3e50; 
            margin-bottom: 15px; 
        }
        p {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 30px;
        }
        .button-container {
            display: flex;
            gap: 15px; 
        }
        .btn {
            flex: 1; 
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn-login {
            background-color: #3498db;
        }
        .btn-login:hover {
            background-color: #2980b9;
        }
        .btn-register {
            background-color: #27ae60;
        }
        .btn-register:hover {
            background-color: #229954;
        }
    </style>
</head>
<body>

    <div class="welcome-container">
        <h1>Welcome to Bill Reminder</h1>
        <p>Manage your bills efficiently and never miss a payment again.</p>
        
        <div class="button-container">
            <a href="login.php" class="btn btn-login">Login</a>
            <a href="registration.php" class="btn btn-register">Register</a>
        </div>
    </div>

</body>
</html>