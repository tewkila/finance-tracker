<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="./css/budget-style.css" rel="stylesheet" />
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
    <form>
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

    <!-- Visualization of budget allocation -->
    <div class="budget-chart">
        <!-- Chart visualization will be displayed here -->
        <p>This is where the budget chart will be displayed.</p>
    </div>

    <!-- Alerts or notifications when nearing or exceeding budget limits -->
    <div class="budget-alerts">
        <!-- Alerts or notifications will be displayed here -->
        <p>This is where the budget alerts or notifications will be displayed.</p>
    </div>
</div>

</body>
</html>
