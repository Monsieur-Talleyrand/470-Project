
<!DOCTYPE html>
<html>
<head>
    <title>Add Bill</title>
</head>
<body>
    <h2>Add New Bill</h2>
    <form action="insert_bill.php" method="POST">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Amount:</label><br>
        <input type="number" step="0.01" name="amount" required><br><br>

        <label>Due Date:</label><br>
        <input type="date" name="due_date" required><br><br>

        <input type="submit" value="Add Bill">
    </form>
</body>
</html>
