<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'settings/config.php';

// Function to calculate total expense for the current month
function calculateTotalExpenseThisMonth() {
    global $link;
    $user_id = $_SESSION['user_id'];
    $startDate = date('Y-m-01');
    $stmt = $link->prepare("SELECT SUM(amount) AS total FROM expenses WHERE user_id = ? AND date >= ?");
    $stmt->bind_param("is", $user_id, $startDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['total'] : 0;
}

// Function to calculate total income for the current month
function calculateTotalIncomeThisMonth() {
    global $link;
    $user_id = $_SESSION['user_id'];
    $startDate = date('Y-m-01');
    $stmt = $link->prepare("SELECT SUM(amount) AS total FROM incomes WHERE user_id = ? AND date >= ?");
    $stmt->bind_param("is", $user_id, $startDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['total'] : 0;
}

// Function to calculate the current balance
function calculateBalance() {
    $totalIncome = calculateTotalIncomeThisMonth();
    $totalExpense = calculateTotalExpenseThisMonth();
    return $totalIncome - $totalExpense;
}

// Function to fetch recent transactions
function fetchRecentTransactions($limit = 10) {
    global $link;
    $user_id = $_SESSION['user_id'];
    $stmt = $link->prepare("
        (SELECT 'Income' AS type, amount, date FROM incomes WHERE user_id = ?)
        UNION
        (SELECT 'Expense' AS type, amount, date FROM expenses WHERE user_id = ?)
        ORDER BY date DESC LIMIT ?");
    $stmt->bind_param("iii", $user_id, $user_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="assets/css/dashboard-style.css" rel="stylesheet" />
    <title>Finance Dashboard</title>
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
<div class="finance-dashboard">
    <div class="welcome-message">Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>!</div>
    <div class="info-container">
        <div class="balance-info">
            <div class="icon"></div>
            <span class="info-label">Balance</span>
            <span class="balance-amount"><?= calculateBalance(); ?>$</span>
        </div>
        <div class="expense-info">
            <div class="icon"></div>
            <span class="info-label">Expenses</span>
            <span class="expense-amount"><?= calculateTotalExpenseThisMonth(); ?>$</span>
        </div>
        <div class="income-info">
            <div class="icon"></div>
            <span class="info-label">Income</span>
            <span class="income-amount"><?= calculateTotalIncomeThisMonth(); ?>$</span>
        </div>
    </div>

    <div class="recent-transactions">
        <h3>Recent Transactions</h3>
        <table>
            <thead>
            <tr>
                <th>Type</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach (fetchRecentTransactions() as $transaction): ?>
                <tr>
                    <td><?= htmlspecialchars($transaction['type']); ?></td>
                    <td><?= htmlspecialchars($transaction['amount']); ?>$</td>
                    <td><?= htmlspecialchars($transaction['date']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
