<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

$loggedInUserId = $_SESSION['user_id'];
$error_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $due_date = $_POST['due_date'] ?? '';
    
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : NULL;
    $new_category_name = trim($_POST['new_category_name'] ?? '');

    
    if (!empty($new_category_name)) {
        $stmt_cat = $conn->prepare("INSERT INTO categories (user_id, name) VALUES (?, ?)");
        $stmt_cat->bind_param("is", $loggedInUserId, $new_category_name);
        $stmt_cat->execute();
        
        $category_id = $conn->insert_id; 
    }

    
    if (!empty($title) && !empty($due_date) && $amount > 0) {
        $stmt = $conn->prepare(
            "INSERT INTO bills (user_id, title, amount, due_date, status, category_id) 
             VALUES (?, ?, ?, ?, 'unpaid', ?)"
        );
        $stmt->bind_param("isdsi", $loggedInUserId, $title, $amount, $due_date, $category_id);
        
        if ($stmt->execute()) {
            header('Location: view_bills.php?msg=Bill+added+successfully');
            exit();
        } else {
            $error_message = "Error adding bill: " . $stmt->error;
        }
    } else {
        $error_message = "Please fill in all required fields correctly.";
    }
}


$categoryResult = $conn->query("SELECT id, name FROM categories WHERE user_id = $loggedInUserId ORDER BY name ASC");
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Bill</title>
    <style>
        
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f7f6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        h2 { color: #2c3e50; }
        nav { padding: 15px 0; margin-bottom: 20px; border-bottom: 1px solid #eee; }
        nav a { margin-right: 20px; font-weight: bold; text-decoration: none; color: #3498db; }
        .error-message { padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #f5c6cb; color: #721c24; background-color: #f8d7da; }
        .form-group { margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input[type="text"], input[type="number"], input[type="date"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button { background-color: #27ae60; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1.1em; font-weight: bold; width: 100%; }
        button:hover { background-color: #229954; }
        .separator { text-align: center; margin: 20px 0; font-weight: bold; color: #777; }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Bill</h2>
    
    <nav>
        <a href="dashboard1.php">← Back to Dashboard</a>
        <a href="view_bills.php">View All Bills</a>
    </nav>

    <?php if (!empty($error_message)): ?>
        <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" step="0.01" name="amount" id="amount" required>
        </div>

        <div class="form-group">
            <label for="due_date">Due Date:</label>
            <input type="date" name="due_date" id="due_date" required>
        </div>

        <div class="form-group">
            <label for="category_id">Select Existing Category:</label>
            <select name="category_id" id="category_id">
                <option value="">-- Optional --</option>
                <?php while ($cat = $categoryResult->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="separator">OR</div>

        <div class="form-group">
            <label for="new_category_name">Add a New Category:</label>
            <input type="text" name="new_category_name" id="new_category_name" placeholder="e.g., Entertainment, Utilities...">
        </div>

        <button type="submit">Add Bill</button>
    </form>
</div>

</body>
</html>