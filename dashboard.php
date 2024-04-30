<?php
session_start();
require_once 'settings/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);

function calculateTotalExpense() {
    global $link; // Ensure your database connection variable is accessible
    $user_id = $_SESSION['user_id']; // User ID from session
    $stmt = $link->prepare("SELECT SUM(amount) FROM expenses WHERE user_id = ? AND date >= ?");
    $today = date('Y-m-d');
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_column() ?: 0;
}

function calculateTotalIncome() {
    global $link;
    $user_id = $_SESSION['user_id'];
    $stmt = $link->prepare("SELECT SUM(amount) FROM income WHERE user_id = ? AND date >= ?");
    $today = date('Y-m-d');
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_column() ?: 0;
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
            <span class="ellipsis">...</span>
        </div>
        <div class="expense-info">
            <div class="icon"></div>
            <span class="info-label">Expense</span>
            <span class="expense-amount"><?= calculateTotalExpense(); ?>$</span>
            <span class="ellipsis">...</span>
        </div>
        <div class="income-info">
            <div class="icon"></div>
            <span class="info-label">Income</span>
            <span class="income-amount"><?= calculateTotalIncome(); ?>$</span>
            <span class="ellipsis">...</span>
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
