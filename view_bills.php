<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Bills</title>
</head>
<body>
    <h2>All Bills</h2>
    <a href="add_bill.php">Add New Bill</a>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Amount</th>
            <th>Due Date</th>
            <th>Action</th>
        </tr>
        <?php
        $sql = "SELECT * FROM bills ORDER BY due_date ASC";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row["id"]) . "</td>
                    <td>" . htmlspecialchars($row["title"]) . "</td>
                    <td>" . number_format($row["amount"], 2) . "</td>
                    <td>" . htmlspecialchars($row["due_date"]) . "</td>
                    <td><a href='delete_bill.php?id=" . urlencode($row["id"]) . "' onclick=\"return confirm('Delete this bill?')\">Delete</a></td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center;'>No bills found.</td></tr>";
        }
        $conn->close();
        ?>
    </table>
    <a href="logout.php">Logout</a>
</body>
</html>