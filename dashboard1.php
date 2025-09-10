<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

-

$loggedInUserId = $_SESSION['user_id'];
$today = date("Y-m-d");

/*
 * Query 1: Get all summary counts in a single, efficient query.
 * This is better for performance than running multiple separate queries.
 */
$stmt_counts = $conn->prepare(
    "SELECT 
        COUNT(*) as total_bills,
        COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_count,
        COUNT(CASE WHEN status = 'unpaid' THEN 1 END) as unpaid_count
     FROM bills WHERE user_id = ?"
);
$stmt_counts->bind_param("i", $loggedInUserId);
$stmt_counts->execute();
$counts = $stmt_counts->get_result()->fetch_assoc();

/*
 * Query 2: Get the total monetary amount of all unpaid bills.
 */
$stmt_unpaid_sum = $conn->prepare(
    "SELECT SUM(amount) as total_unpaid 
     FROM bills WHERE user_id = ? AND status = 'unpaid'"
);
$stmt_unpaid_sum->bind_param("i", $loggedInUserId);
$stmt_unpaid_sum->execute();
$unpaid_sum = $stmt_unpaid_sum->get_result()->fetch_assoc();
// Use the null coalescing operator '?? 0' to handle cases where there are no unpaid bills, preventing errors.
$totalUnpaidAmount = $unpaid_sum['total_unpaid'] ?? 0;

/*
 * Query 3 (Sprint 4): Get all OVERDUE bills.
 * These are bills that are 'unpaid' AND their due date has passed.
 */
$stmt_overdue = $conn->prepare(
    "SELECT id, title, amount, due_date FROM bills 
     WHERE user_id = ? AND status = 'unpaid' AND due_date < ? 
     ORDER BY due_date ASC"
);
$stmt_overdue->bind_param("is", $loggedInUserId, $today);
$stmt_overdue->execute();
$overdue_bills_result = $stmt_overdue->get_result();

/*
 
 * These are bills that are 'unpaid' and due between today and 30 days from now.
 */
$upcoming_date_limit = date("Y-m-d", strtotime("+30 days"));
$stmt_upcoming = $conn->prepare(
    "SELECT id, title, amount, due_date FROM bills 
     WHERE user_id = ? AND status = 'unpaid' AND due_date BETWEEN ? AND ? 
     ORDER BY due_date ASC"
);
$stmt_upcoming->bind_param("iss", $loggedInUserId, $today, $upcoming_date_limit);
$stmt_upcoming->execute();
$upcoming_bills_result = $stmt_upcoming->get_result();


$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f7f6; color: #333; }
        .container { max-width: 1000px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        h2, h3 { color: #2c3e50; }
        nav { padding: 15px 0; margin-bottom: 20px; border-bottom: 1px solid #eee; }
        nav a { margin-right: 20px; font-weight: bold; text-decoration: none; color: #3498db; font-size: 1em; }
        nav a:hover { text-decoration: underline; }
        .summary-grid { display: flex; flex-wrap: wrap; justify-content: space-around; margin-bottom: 20px; }
        .summary-box { border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin: 10px; flex-basis: 22%; text-align: center; background-color: #fafafa; }
        .summary-box h3 { margin-top: 0; color: #555; font-size: 1.1em; }
        .summary-box p { font-size: 2.5em; margin: 0; font-weight: bold; color: #2c3e50; }
        .summary-box.unpaid-amount { background-color: #fff2f2; border-color: #ffb8b8; }
        .summary-box.unpaid-amount p { color: #e74c3c; }
        .bill-section { margin-top: 40px; }
        .bill-section h3 { border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .bill-section.overdue h3 { color: #e74c3c; border-color: #e74c3c; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 15px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:nth-child(even) { background-color: #fdfdfd; }
        .no-bills-message { text-align: center; color: #777; font-style: italic; padding: 20px; }
        .action-link { color: #3498db; text-decoration: none; font-weight: bold; }
        .action-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">

    <h2>Dashboard</h2>
    <p>Welcome, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></strong>!</p>

    <nav>
        <a href="view_bills.php">View All Bills (with Search)</a>
        <a href="category/view_categories.php">Manage Categories</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="summary-grid">
        <div class="summary-box"><h3>Total Bills</h3><p><?= $counts['total_bills'] ?></p></div>
        <div class="summary-box"><h3>Paid Bills</h3><p><?= $counts['paid_count'] ?></p></div>
        <div class="summary-box"><h3>Unpaid Bills</h3><p><?= $counts['unpaid_count'] ?></p></div>
        <div class="summary-box unpaid-amount"><h3>Total Unpaid</h3><p>$<?= number_format($totalUnpaidAmount, 2) ?></p></div>
    </div>

    <div class="bill-section overdue">
        <h3>🚨 Overdue Bills</h3>
        <table>
            <thead><tr><th>Bill Title</th><th>Amount</th><th>Due Date</th><th>Actions</th></tr></thead>
            <tbody>
                <?php if ($overdue_bills_result->num_rows > 0): ?>
                    <?php while ($row = $overdue_bills_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td>$<?= number_format($row['amount'], 2) ?></td>
                        <td><?= htmlspecialchars($row['due_date']) ?></td>
                        <td><a href="edit_bill.php?id=<?= $row['id'] ?>" class="action-link">Pay Now / Edit</a></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="no-bills-message">Excellent! You have no overdue bills.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="bill-section">
        <h3>🗓️ Upcoming Bills (Next 30 Days)</h3>
        <table>
            <thead><tr><th>Bill Title</th><th>Amount</th><th>Due Date</th><th>Actions</th></tr></thead>
            <tbody>
                <?php if ($upcoming_bills_result->num_rows > 0): ?>
                    <?php while ($row = $upcoming_bills_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td>$<?= number_format($row['amount'], 2) ?></td>
                        <td><?= htmlspecialchars($row['due_date']) ?></td>
                        <td><a href="edit_bill.php?id=<?= $row['id'] ?>" class="action-link">Edit / Pay</a></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="no-bills-message">You have no bills due in the next 30 days.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>