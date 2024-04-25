<?php
session_start(); // Start the session

// Check if income data exists in the session, if not, initialize it
if (!isset($_SESSION['incomeData'])) {
    $_SESSION['incomeData'] = [];
}

// Check if expense data exists in the session, if not, initialize it
if (!isset($_SESSION['expenseData'])) {
    $_SESSION['expenseData'] = [];
}

// Function to calculate total income for the day
function calculateTotalIncome() {
    $totalIncome = 0;
    foreach ($_SESSION['incomeData'] as $income) {
        if ($income['date'] == date("Y-m-d")) {
            $totalIncome += $income['amount'];
        }
    }
    return $totalIncome;
}

// Function to calculate total expense for the day
function calculateTotalExpense() {
    $totalExpense = 0;
    foreach ($_SESSION['expenseData'] as $expense) {
        if ($expense['date'] == date("Y-m-d")) {
            $totalExpense += $expense['amount'];
        }
    }
    return $totalExpense;
}

// Function to calculate balance
function calculateBalance() {
    return calculateTotalIncome() - calculateTotalExpense();
}

// Function to get recent transactions
function getRecentTransactions() {
    $recentTransactions = [];

    // Combine income and expense data
    $combinedData = array_merge($_SESSION['incomeData'], $_SESSION['expenseData']);

    // Sort combined data by date (newest first)
    usort($combinedData, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    // Get the latest 10 transactions
    $count = 0;
    foreach ($combinedData as $transaction) {
        if ($count >= 10) {
            break;
        }
        $recentTransactions[] = $transaction;
        $count++;
    }

    return $recentTransactions;
}

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Process form data here if needed
}

?>

<!DOCTYPE html>
<html>
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
    <div class="welcome-message">Welcome back, user!</div>
    <div class="info-container">
        <div class="balance-info">
            <div class="icon"></div>
            <span class="info-label">Balance</span>
            <span class="balance-amount"><?php echo calculateBalance(); ?>$</span>
            <span class="ellipsis">...</span>
        </div>
        <div class="expense-info">
            <div class="icon"></div>
            <span class="info-label">Expense</span>
            <span class="expense-amount"><?php echo calculateTotalExpense(); ?>$</span>
            <span class="ellipsis">...</span>
        </div>
        <div class="income-info">
            <div class="icon"></div>
            <span class="info-label">Income</span>
            <span class="income-amount"><?php echo calculateTotalIncome(); ?>$</span>
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
        <?php
        $recentTransactions = getRecentTransactions();
        foreach ($recentTransactions as $transaction) {
            echo "<tr>";
            echo "<td>" . (isset($transaction['source']) ? "Income" : "Expense") . "</td>";
            echo "<td>" . $transaction['amount'] . "</td>";
            echo "<td>" . $transaction['date'] . "</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
