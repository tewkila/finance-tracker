<?php
require_once 'settings/config.php';
session_start();

// Fetch budgets from the database
$budgets = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $link->prepare("SELECT id, category, amount, date FROM budgets WHERE user_id = ? AND date > DATE_SUB(NOW(), INTERVAL 1 MONTH)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $budgets[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="assets/css/budget-style.css" rel="stylesheet" />
    <title>Budget</title>
</head>
<body>
<div class="header"></div>
<div class="user-info"></div>
<a href="index.php" class="app-title">Finanss</a>

<!-- Menu -->
<div class="menu">
    <a href="dashboard.php" class="menu-item">Dashboard</a>
    <a href="income.php" class="menu-item">Income</a>
    <a href="expense.php" class="menu-item">Expense</a>
    <a href="budget.php" class="menu-item">Budget</a>
</div>

<!-- Budget Page -->
<div class="budget-page">
    <h2>Budget Page</h2>
    <!-- Form to set budget for different categories -->
    <form action="settings/process_budget.php" method="post">
        <div class="form-row">
            <label for="groceries">Groceries:</label>
            <input type="number" id="groceries" name="groceries" required>
        </div>
        <div class="form-row">
            <label for="utilities">Utilities:</label>
            <input type="number" id="utilities" name="utilities" required>
        </div>
        <div class="form-row">
            <label for="entertainment">Entertainment:</label>
            <input type="number" id="entertainment" name="entertainment" required>
        </div>
        <button type="submit">Set Budget</button>
    </form>
    <!-- Table to display budgets -->
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
                <td><?= htmlspecialchars($budget['category']) ?></td>
                <td><?= htmlspecialchars($budget['amount']) ?></td>
                <td><?= htmlspecialchars($budget['date']) ?></td>
                <td>
                    <a href="edit_budget.php?id=<?= $budget['id'] ?>">Edit</a>
                    <a href="delete_budget.php?id=<?= $budget['id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
