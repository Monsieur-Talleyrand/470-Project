<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    
    header('Location: ../login.php'); 
    exit();
}


require_once __DIR__ . '/../db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $error = '';

    if ($name === '') {
        $error = "Category name is required.";
    } else {
        $loggedInUserId = $_SESSION['user_id'];

        $stmt = $conn->prepare("INSERT INTO categories (name, description, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $desc, $loggedInUserId); 

        if ($stmt->execute()) {
            
            header('Location: view_categories.php?msg=Category+added');
            exit();
        } else {
            $error = "Error adding category.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Category</title>
</head>
<body>
<div class="container mt-4">
    <h2>Add New Category</h2>
    
    <a href="view_categories.php">← Back to Categories</a>
    <hr>
    
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    
    <form method="post">
        <label>Name:<br>
            <input type="text" name="name" required>
        </label><br><br>
        <label>Description:<br>
            <textarea name="description"></textarea>
        </label><br><br>
        <button type="submit">Add Category</button>
    </form>
</div>
</body>
</html>
