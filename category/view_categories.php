<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

// must include DB connection
require_once __DIR__ . '/../db.php';

// Fetch all categories
$sql = "
    SELECT c.id, 
           c.name, 
           c.description, 
           COALESCE(SUM(b.amount), 0) AS total_amount
    FROM categories c
    LEFT JOIN bills b ON c.id = b.category_id
    GROUP BY c.id, c.name, c.description
    ORDER BY c.id ASC
";


$result = $conn->query($sql);
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Categories</title>
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Categories</h2>
    <div class="mb-3">
        <a href="add_category.php" class="btn btn-primary">+ Add Category</a>
        <a href="../dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
    <table class="table table-bordered table-striped">
        <thead class="thead-light">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th style="width:160px;">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <a href="edit_category.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_category.php?id=<?= $row['id'] ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this category?');">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>