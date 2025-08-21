<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../db.php';

// Validate & fetch
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: view_categories.php?msg=Invalid+ID');
    exit();
}

$stmt = $conn->prepare("SELECT name, description FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$category) {
    header('Location: view_categories.php?msg=Category+not+found');
    exit();
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['name'] ?? '');
    $newDesc = trim($_POST['description'] ?? '');

    if ($newName === '') {
        $error = "Name cannot be empty.";
    } else {
        $u = $conn->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
        $u->bind_param("ssi", $newName, $newDesc, $id);

        if ($u->execute()) {
            header('Location: view_categories.php?msg=Category+updated');
            exit();
        } else {
            $error = "Error updating: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
</head>
<body>
<div class="container mt-4">
    <h2>Edit Category</h2>
    <a href="view_categories.php">← Back to Categories</a>
    <hr>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Name:<br>
            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
        </label><br><br>
        <label>Description:<br>
            <textarea name="description"><?= htmlspecialchars($category['description']) ?></textarea>
        </label><br><br>
        <button type="submit">Update Category</button>
    </form>
</div>
</body>
</html>