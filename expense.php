<?php
session_start();
require_once 'settings/config.php';

// Function to fetch expenses from the database
function fetchExpenses($link) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM expenses WHERE user_id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $expenses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $expenses;
}

// Retrieve expenses to display
$expenses = fetchExpenses($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/ajax/libs/poppins/1.0.0/css/poppins.css" rel="stylesheet">
    <link href="assets/css/expense-style.css" rel="stylesheet">
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

<div class="expense-page">
    <h2>Expense Page</h2>
    <form action="settings/process_expense.php" method="post">
        <input type="hidden" name="expense_id" id="expense_id">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" min="0" required>
        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="Groceries">Groceries</option>
            <option value="Utilities">Utilities</option>
            <option value="Entertainment">Entertainment</option>
        </select>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required>
        <button type="submit" name="submit">Submit</button>
    </form>

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
                <td><?= htmlspecialchars($expense['amount']); ?></td>
                <td><?= htmlspecialchars($expense['category']); ?></td>
                <td><?= htmlspecialchars($expense['date']); ?></td>
                <td>
                    <button type="submit" onclick="editExpense('<?= $expense['id']; ?>', '<?= $expense['amount']; ?>', '<?= $expense['category']; ?>', '<?= $expense['date']; ?>')">Edit</button>
                    <form action="settings/process_expense.php" method="post">
                        <input type="hidden" name="delete" value="<?= $expense['id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script>
    function editExpense(id, amount, category, date) {
        document.getElementById('expense_id').value = id;
        document.getElementById('amount').value = amount;
        document.getElementById('category').value = category;
        document.getElementById('date').value = date;
    }
</script>

</body>
</html>