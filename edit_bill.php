<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/db.php';

$loggedInUserId = $_SESSION['user_id'];
$billId = intval($_GET['id'] ?? 0);

if ($billId === 0) {
    header('Location: list_bills.php?error=Invalid+Bill+ID');
    exit();
}

// Handle the form submission to UPDATE the bill
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $due_date = $_POST['due_date'] ?? '';
    $status = $_POST['status'] ?? 'unpaid';
    // Use 0 for "uncategorized" if nothing is selected
    $category_id = intval($_POST['category_id'] ?? 0) ?: NULL;

    // Validation
    if (empty($title) || empty($due_date) || $amount <= 0) {
        header("Location: edit_bill.php?id=$billId&error=Please+fill+all+required+fields");
        exit();
    }

    $stmt = $conn->prepare("UPDATE bills 
        SET title = ?, amount = ?, due_date = ?, status = ?, category_id = ? 
        WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sdssiii", $title, $amount, $due_date, $status, $category_id, $billId, $loggedInUserId);
    $stmt->execute();

    header('Location: view_bills.php?msg=Bill+updated+successfully');
    exit();
}

// Fetch the current bill details to show in the form
$stmt = $conn->prepare("SELECT * FROM bills WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $billId, $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();
$bill = $result->fetch_assoc();

if (!$bill) {
    header('Location: list_bills.php?error=Bill+not+found');
    exit();
}

// Fetch categories for the dropdown
$categories = $conn->query("SELECT id, name FROM categories WHERE user_id = $loggedInUserId ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Bill</title>
</head>
<body>
    <h2>Edit Bill</h2>
    <a href="list_bills.php">← Back to Bills List</a>
    <hr>

    <!-- Display error message if any -->
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <form method="post">
        Title: <br>
        <input type="text" name="title" value="<?= htmlspecialchars($bill['title']) ?>" required><br><br>

        Amount: <br>
        <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($bill['amount']) ?>" required><br><br>

        Due Date: <br>
        <input type="date" name="due_date" value="<?= htmlspecialchars($bill['due_date']) ?>" required><br><br>

        Status: <br>
        <select name="status">
            <option value="unpaid" <?= $bill['status'] == 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
            <option value="paid" <?= $bill['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
            <option value="overdue" <?= $bill['status'] == 'overdue' ? 'selected' : '' ?>>Overdue</option>
        </select><br><br>

        Category: <br>
        <select name="category_id">
            <option value="">-- Uncategorized --</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>" <?= $bill['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Update Bill</button>
    </form>
</body>
</html>