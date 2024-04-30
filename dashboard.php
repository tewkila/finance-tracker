<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'settings/config.php';

require_once 'settings/config.php';

// After verifying login credentials
$_SESSION['username'] = $usernameFromDatabase;
$username = $_SESSION['username'] ?? 'User';

function calculateTotalExpense() {
    global $link;
    $user_id = $_SESSION['user_id'];
    $today = date('Y-m-d');
    $stmt = $link->prepare("SELECT SUM(amount) AS total FROM expenses WHERE user_id = ? AND date >= ?");
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['total'] : 0;
}

function calculateTotalIncome() {
    global $link;
    $user_id = $_SESSION['user_id'];
    $today = date('Y-m-d');
    $stmt = $link->prepare("SELECT SUM(amount) AS total FROM incomes WHERE user_id = ? AND date >= ?");
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['total'] : 0;
}



function calculateBalance() {
    $totalIncome = calculateTotalIncome();
    $totalExpense = calculateTotalExpense();
    return $totalIncome - $totalExpense;
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
    <div class="welcome-message">Welcome back, <?= htmlspecialchars($username); ?>!</div>
    <div class="info-container">
        <div class="balance-info">
            <div class="icon"></div>
            <span class="info-label">Balance</span>
            <span class="balance-amount"><?= calculateBalance(); ?>$</span>
        </div>
        <div class="expense-info">
            <div class="icon"></div>
            <span class="info-label">Expenses</span>
            <span class="expense-amount"><?= calculateTotalExpense(); ?>$</span>
        </div>
        <div class="income-info">
            <div class="icon"></div>
            <span class="info-label">Income</span>
            <span class="income-amount"><?= calculateTotalIncome(); ?>$</span>
        </div>
    </div>

    <span class="recent">Recent</span>
    <table>
        <thead>
        <tr>
            <th>Type</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
</body>
</html>
