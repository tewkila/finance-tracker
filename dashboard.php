<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="./css/dashboard-style.css" rel="stylesheet" />
    <title>Finance Dashboard</title>
</head>
<body>
<div class="header">
    <a href="index.php" class="app-title">Finanss</a>
    <div class="user-info">user</div>
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
            <span class="balance-amount">12560$</span>
            <span class="ellipsis">...</span>
        </div>
        <div class="expense-info">
            <div class="icon"></div>
            <span class="info-label">Expense</span>
            <span class="expense-amount">-6784$</span>
            <span class="ellipsis">...</span>
        </div>
        <div class="income-info">
            <div class="icon"></div>
            <span class="info-label">Income</span>
            <span class="income-amount">+1024$</span>
            <span class="ellipsis">...</span>
        </div>
    </div>
    <span class="recent">Recent</span>
</div>
</body>
</html>
