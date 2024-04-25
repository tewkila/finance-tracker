<?php
session_start(); // Start the session

require_once 'settings/config.php';

// Handle form submission for adding or updating expenses
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $expense_id = $_POST['expense_id'] ?? null;

    if ($expense_id) {
        // Update existing expense
        $sql = "UPDATE expenses SET amount = ?, category = ?, date = ? WHERE id = ? AND user_id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("dssii", $amount, $category, $date, $expense_id, $_SESSION['user_id']);
    } else {
        // Add new expense
        $sql = "INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("idss", $_SESSION['user_id'], $amount, $category, $date);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: expense.php"); // Redirect to refresh the page
    exit;
}

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['expense_id'])) {
    $expense_id = $_POST['expense_id'];
    $sql = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ii", $expense_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    header("Location: expense.php"); // Redirect to refresh the page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="assets/css/expense-style.css" rel="stylesheet" />
    <title>Expense</title>
</head>
<body>
<div class="header"></div>
<div class="user-info"></div>
<a href="index.php" class="app-title">Finanss</a>
<div class="menu">
    <a href="dashboard.php" class="menu-item">Dashboard</a>
    <a href="income.php" class="menu-item">Income</a>
    <a href="expense.php" class="menu-item">Expense</a>
    <a href="budget.php" class="menu-item">Budget</a>
</div>

<!-- Expense Page -->
<div class="expense-page">
    <h2>Expense Page</h2>
    <form action="settings/process_expense.php" method="post">
        <input type="hidden" name="expense_id" value="<?php echo isset($editKey) ? $editKey : ''; ?>">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required min="0" value="<?php echo isset($editAmount) ? $editAmount : ''; ?>">

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="Groceries">Groceries</option>
            <option value="Utilities">Utilities</option>
            <option value="Entertainment">Entertainment</option>
        </select>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required value="<?php echo isset($editDate) ? $editDate : ''; ?>">

        <button type="submit" name="submit">Submit</button>
    </form>


    <!-- Table to display existing expense entries -->
    <table>
        <thead>
        <tr>
            <th>Amount</th>
            <th>Category</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($expenses as $expense) { ?>
            <tr>
                <td><?php echo htmlspecialchars($expense['amount']); ?></td>
                <td><?php echo htmlspecialchars($expense['category']); ?></td>
                <td><?php echo htmlspecialchars($expense['date']); ?></td>
                <td>
                    <a href="expense.php?edit=<?php echo $expense['id']; ?>">Edit</a>
                    <form action="expense.php" method="post" style="display:inline;">
                        <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
