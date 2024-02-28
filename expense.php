<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="./css/expense-style.css" rel="stylesheet" />
    <title>Expense</title>
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

<!-- Expense Page -->
<div class="expense-page">
    <h2>Expense Page</h2>
    <!-- Form to add expense details -->
    <form>
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required>

        <label for="category">Category:</label>
        <input type="text" id="category" name="category" required>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required>

        <button type="submit">Add Expense</button>
    </form>

    <!-- Table to display existing expense entries -->
    <table>
        <thead>
        <tr>
            <th>Amount</th>
            <th>Category</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <!-- Existing expense entries will be populated here dynamically -->
        <tr>
            <td>$50</td>
            <td>Groceries</td>
            <td>2024-02-26</td>
            <td>
                <button>Edit</button>
                <button>Delete</button>
            </td>
        </tr>
        <!-- Add more rows as needed -->
        </tbody>
    </table>
</div>

</body>
</html>
