<?php
session_start();
require_once 'settings/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch budgets from the database
function fetchBudgets($link, $user_id) {
    $stmt = $link->prepare("SELECT id, category, amount, date FROM budgets WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$budgets = fetchBudgets($link, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="assets/css/budget-style.css" rel="stylesheet">
    <title>Budget</title>
</head>
<body>
<div class="menu">
    <a href="dashboard.php" class="menu-item">Dashboard</a>
    <a href="income.php" class="menu-item">Income</a>
    <a href="expense.php" class="menu-item">Expense</a>
    <a href="budget.php" class="menu-item">Budget</a>
</div>

<div class="budget-page">
    <h2>Budget Page</h2>
    <form action="settings/process_budget.php" method="post">
        <input type="hidden" name="action" value="add">
        <label for="category">Category:</label>
        <input type="text" id="category" name="category" required>
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required>
        <button type="submit">Submit</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Amount</th>
                <th>Date Set</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($budgets as $budget): ?>
                <tr>
                    <td><?= htmlspecialchars($budget['category']); ?></td>
                    <td><?= htmlspecialchars($budget['amount']); ?></td>
                    <td><?= htmlspecialchars($budget['date']); ?></td>
                    <td>
                        <a href="?edit=<?= $budget['id']; ?>" class="button-link">Edit</a>
                        <form action="settings/process_budget.php" method="post" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="budget_id" value="<?= $budget['id']; ?>">
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
