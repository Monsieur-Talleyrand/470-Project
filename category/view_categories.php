<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    
    header('Location: ../login.php');
    exit();
}


require_once __DIR__ . '/../db.php'; 

$loggedInUserId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, name FROM categories WHERE user_id = ? ORDER BY name ASC");
$stmt->bind_param("i", $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Categories</title>
</head>
<body>
    <h2>Manage Categories</h2>
    
    <a href="add_category.php"><b>+ Add New Category</b></a> | 
    
    <a href="../dashboard1.php">← Back to Dashboard</a>
    <hr>

    <table border="1" cellpadding="5" cellspacing="0">
        
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td>
                    
                    <a href="edit_category.php?id=<?= $row['id'] ?>">Edit</a>
                    <a href="delete_category.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>