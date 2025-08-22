<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'bill_reminder470');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');
    $loggedInUserId = $_SESSION['user_id']; 

    if (!empty($title) && !empty($amount) && !empty($due_date)) {
        $stmt = $conn->prepare("INSERT INTO bills (title, amount, due_date, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $title, $amount, $due_date, $loggedInUserId);

        if ($stmt->execute()) {
            
            header('Location: dashboard1.php?msg=Bill+added');
            exit();
        } else {
            $error = "Error adding bill.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add a New Bill</title>
</head>
<body>
    <h2>Add a New Bill</h2>
    
    <a href="dashboard1.php">← Back to Dashboard</a>
    <hr>
    
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        Bill Title: <input type="text" name="title" required><br><br>
        Amount: <input type="number" step="0.01" name="amount" required><br><br>
        Due Date: <input type="date" name="due_date" required><br><br>
        <button type="submit">Save Bill</button>
    </form>
</body>
</html>
