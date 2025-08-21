<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../db.php';

$result = $conn->query("SELECT id, name, description FROM categories ORDER BY id ASC");
if (!$result) {
    die("DB query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>All Categories</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
  <h2>All Categories</h2>
  <div class="mb-3">
    <a href="categories_add.php" class="btn btn-primary">+ Add Category</a>
    <a href="../dashboard.php" class="btn btn-secondary">← Dashboard</a>
  </div>
  <table class="table table-bordered table-striped">
    <thead class="thead-light">
      <tr><th>ID</th><th>Name</th><th>Description</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td>
          <a href="categories_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="categories_delete.php?id=<?= $row['id'] ?>"
             class="btn btn-sm btn-danger"
             onclick="return confirm('Delete this category?');">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>