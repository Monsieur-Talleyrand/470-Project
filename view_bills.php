<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

$loggedInUserId = $_SESSION['user_id'];

// --- Handle Delete Bill ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM bills WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $loggedInUserId);
    $stmt->execute();
    header("Location: view_bills.php?msg=Bill+deleted+successfully");
    exit();
}

// --- Build the SQL Query with Filters ---
$sql = "SELECT b.*, c.name AS category_name 
        FROM bills b 
        LEFT JOIN categories c ON b.category_id = c.id 
        WHERE b.user_id = ?";
$params = [$loggedInUserId];
$types = "i";

// Add filters to the query if they are present
if (!empty($_GET['category_id'])) {
    $sql .= " AND b.category_id = ?";
    $params[] = intval($_GET['category_id']);
    $types .= "i";
}
if (!empty($_GET['status'])) {
    $sql .= " AND b.status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}
$searchQuery = '';
if (!empty($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $sql .= " AND b.title LIKE ?";
    $params[] = "%" . $searchQuery . "%";
    $types .= "s";
}

$sql .= " ORDER BY b.due_date DESC";

// --- Execute the Query using Prepared Statements ---
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// --- Fetch Categories for the filter dropdown ---
$categoryResult = $conn->query("SELECT id, name FROM categories WHERE user_id = $loggedInUserId ORDER BY name ASC");

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Bills</title>
    <style>
        /* Using the same professional styles from your other pages */
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f7f6; color: #333; }
        .container { max-width: 1000px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        h2 { color: #2c3e50; }
        nav { padding: 15px 0; margin-bottom: 20px; border-bottom: 1px solid #eee; }
        nav a { margin-right: 20px; font-weight: bold; text-decoration: none; color: #3498db; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb; color: #155724; background-color: #d4edda; }
        .filter-form { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center; }
        .filter-form input, .filter-form select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .filter-form button { background-color: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .filter-form .reset-link { color: #777; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 15px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:nth-child(even) { background-color: #fdfdfd; }
        .no-bills-message { text-align: center; color: #777; font-style: italic; padding: 20px; }
        .action-link { color: #3498db; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>All Bills</h2>
    
    <!-- MODIFIED NAVIGATION BAR -->
    <nav>
        <a href="dashboard1.php">← Back to Dashboard</a>
        <a href="add_bill.php">+ Add New Bill</a>
        <a href="logout.php">Logout</a>
    </nav>

    <?php if (isset($_GET['msg'])): ?>
        <p class="message"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif; ?>

    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Search by title..." value="<?= htmlspecialchars($searchQuery) ?>">
        
        <select name="category_id">
            <option value="">Filter by Category</option>
            <?php while ($cat = $categoryResult->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>" <?= (($_GET['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="status">
            <option value="">Filter by Status</option>
            <option value="unpaid" <?= (($_GET['status'] ?? '') == 'unpaid') ? 'selected' : '' ?>>Unpaid</option>
            <option value="paid" <?= (($_GET['status'] ?? '') == 'paid') ? 'selected' : '' ?>>Paid</option>
            <option value="overdue" <?= (($_GET['status'] ?? '') == 'overdue') ? 'selected' : '' ?>>Overdue</option>
        </select>
        
        <button type="submit">Apply</button>
        <a href="view_bills.php" class="reset-link">Reset</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["title"]) ?></td>
                <td>$<?= number_format($row["amount"], 2) ?></td>
                <td><?= htmlspecialchars($row["due_date"]) ?></td>
                <td><?= ucfirst(htmlspecialchars($row["status"])) ?></td>
                <td><?= htmlspecialchars($row["category_name"] ?? 'Uncategorized') ?></td>
                <td>
                    <a href='edit_bill.php?id=<?= $row["id"] ?>' class="action-link">Edit</a> | 
                    <a href='?delete=<?= $row["id"] ?>' onclick="return confirm('Are you sure?');" class="action-link" style="color:#e74c3c;">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" class="no-bills-message">No bills found. Try adjusting your filters or adding a new bill.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>