<?php
session_start();
require_once 'settings/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch budgets from the database
$link = new mysqli("hostname", "username", "password", "database"); // Ensure database credentials are correct
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}

$stmt = $link->prepare("SELECT id, category, amount, DATE_FORMAT(date, '%Y-%m-%d') as date FROM budgets WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$budgets = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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

<div class="menu">
    <a href="dashboard.php" class="menu-item">Dashboard</a>
    <a href="income.php" class="menu-item">Income</a>
    <a href="expense.php" class="menu-item">Expense</a>
    <a href="budget.php" class="menu-item">Budget</a>
</div>

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
                    <a href="?edit=<?= $budget['id']; ?>" class="button-link">Edit</a>
                    <form action="settings/process_budget.php" method="post" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="budget_id" value="<?= $budget['id']; ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this budget?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
