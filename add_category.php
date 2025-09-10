<?php
/**
 * add_category.php
 * Form to add a new category and insert into DB.
 */

require_once 'includes/db.php';
session_start();

$name = $description = "";
$name_err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (empty($name)) {
        $name_err = "Category name is required.";
    } else {
        // Insert category into DB
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        if ($stmt->execute()) {
            header("Location: categories_list.php");
            exit();
        } else {
            $name_err = "Database error: could not add category.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Category</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h2>Add New Category</h2>
    <form method="POST" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Category Name *</label>
            <input type="text" class="form-control <?= $name_err ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
            <div class="invalid-feedback"><?= $name_err ?></div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description (optional)</label>
            <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($description) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Category</button>
        <a href="categories_list.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
</body>
</html>