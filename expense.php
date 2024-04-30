<?php
session_start();
require_once 'settings/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error_message = $_SESSION['error_message'] ?? ''; // Capture and clear any error message
unset($_SESSION['error_message']);

// Function to fetch expenses from the database
function fetchExpenses($link, $user_id) {
    $stmt = $link->prepare("SELECT id, amount, category, date FROM expenses WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$expenses = fetchExpenses($link, $user_id);

// Check if editing
$editExpense = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    foreach ($expenses as $expense) {
        if ($expense['id'] == $editId) {
            $editExpense = $expense;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="assets/css/expense-style.css" rel="stylesheet">
    <title>Expense</title>
</head>
<body>
<div class="header">
    <a href="index.php" class="app-title">Finanss</a>
</div>
<div class="menu">
    <a href="dashboard.php" class="menu-item">Dashboard</a>
    <a href="income.php" class="menu-item">Income</a>
    <a href="expense.php" class="menu-item">Expense</a>
    <a href="budget.php" class="menu-item">Budget</a>
</div>

<div class="expense-page">
    <h2>Expense Page</h2>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>
    <form action="settings/process_expense.php" method="post">
        <input type="hidden" name="action" value="<?= $editExpense ? 'edit' : 'add'; ?>">
        <input type="hidden" name="expense_id" value="<?= $editExpense['id'] ?? ''; ?>">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" value="<?= $editExpense['amount'] ?? ''; ?>" required min="0" step="0.01">
        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="Groceries" <?= ($editExpense && $editExpense['category'] == 'Groceries') ? 'selected' : ''; ?>>Groceries</option>
            <option value="Utilities" <?= ($editExpense && $editExpense['category'] == 'Utilities') ? 'selected' : ''; ?>>Utilities</option>
            <option value="Entertainment" <?= ($editExpense && $editExpense['category'] == 'Entertainment') ? 'selected' : ''; ?>>Entertainment</option>
        </select>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?= $editExpense['date'] ?? ''; ?>" required max="<?= date('Y-m-d'); ?>">
        <button type="submit"><?= $editExpense ? 'Update' : 'Submit'; ?></button>
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
        <?php foreach ($expenses as $expense): ?>
            <tr>
                <td><?= htmlspecialchars($expense['amount']); ?></td>
                <td><?= htmlspecialchars($expense['category']); ?></td>
                <td><?= htmlspecialchars($expense['date']); ?></td>
                <td>
                    <a href="?edit=<?= $expense['id']; ?>" class="button-link">Edit</a>
                    <form action="settings/process_expense.php" method="post" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="expense_id" value="<?= $expense['id']; ?>">
                        <button type="submit" class="button-link">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
