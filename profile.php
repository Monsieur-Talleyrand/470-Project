<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

$loggedInUserId = $_SESSION['user_id'];
$message = '';
$message_type = ''; // Will be 'success' or 'error' for styling

// Handle the form submission for password change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Step 1: Fetch the user's current hashed password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $loggedInUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Step 2: Securely verify the current password against the stored hash
    if ($user && password_verify($current_password, $user['password'])) {
        // Step 3: Check if new passwords match and are not empty
        if (!empty($new_password) && $new_password === $confirm_password) {
            
            // Step 3a: Add a strength check for the new password
            if (strlen($new_password) < 6) {
                $message = "New password must be at least 6 characters long.";
                $message_type = 'error';
            } else {
                // Step 4: Hash the new password for secure storage
                $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

                // Step 5: Update the password in the database using a prepared statement
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_password_hashed, $loggedInUserId);
                $update_stmt->execute();

                $message = "Password updated successfully!";
                $message_type = 'success';
            }
        } else {
            $message = "New passwords do not match or are empty.";
            $message_type = 'error';
        }
    } else {
        $message = "Incorrect current password.";
        $message_type = 'error';
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f7f6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        nav { padding: 15px 0; margin-bottom: 20px; border-bottom: 1px solid #eee; }
        nav a { margin-right: 20px; font-weight: bold; text-decoration: none; color: #3498db; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid; }
        .message.success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .message.error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #3498db; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        button:hover { background-color: #2980b9; }
    </style>
</head>
<body>

<div class="container">
    <h2>User Profile</h2>

    <nav>
        <a href="dashboard1.php">← Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>

    <h3>Change Password</h3>
    
    <!-- Display success or error messages here -->
    <?php if ($message): ?>
        <div class="message <?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <div style="margin-bottom: 15px;">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </div>

        <button type="submit">Update Password</button>
    </form>
</div>

</body>
</html>