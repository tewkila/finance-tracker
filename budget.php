<?php
session_start();
require_once 'settings/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $link->prepare("SELECT id, category, amount, date FROM budgets WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$budgets = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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
                <td><?= htmlspecialchars(date('Y-m-d', strtotime($budget['date']))) ?></td>
                <td>
                    <!-- Edit/Delete Actions if necessary -->
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
