<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}


$conn = new mysqli('localhost', 'root', '', 'bill_reminder470');


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
    
    
    $sql = "INSERT INTO bills (title, amount, due_date) VALUES ('$title', '$amount', '$due_date')";
    $conn->query($sql);
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM bills WHERE id=$id");
}


$result = $conn->query("SELECT * FROM bills");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p><a href="logout.php">Logout</a></p>
    
    <h3>Add New Bill</h3>
    <form method="POST" action="">
        Title: <input type="text" name="title" required><br><br>
        Amount: <input type="number" step="0.01" name="amount" required><br><br>
        Due Date: <input type="date" name="due_date" required><br><br>
        <input type="submit" value="Add Bill">
    </form>

    <h3>All Bills</h3>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Amount</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['amount']; ?></td>
            <td><?php echo $row['due_date']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this bill?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>