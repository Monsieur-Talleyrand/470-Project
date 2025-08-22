<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../db.php';
$loggedInUserId = $_SESSION['user_id'];
$categoryId = intval($_GET['id'] ?? 0);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    
    $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $name, $desc, $categoryId, $loggedInUserId);
    $stmt->execute();
    header('Location: view_categories.php?msg=Category+updated');
    exit();
}


$stmt = $conn->prepare("SELECT name, description FROM categories WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $categoryId, $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();


if (!$category) {
    header('Location: view_categories.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
</head>
<body>
    <h2>Edit Category</h2>
    <form method="post">
        Name:<br>
        <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required><br><br>
        Description:<br>
        <textarea name="description"><?= htmlspecialchars($category['description']) ?></textarea><br><br>
        <button type="submit">Update Category</button>
    </form>
</body>
</html>