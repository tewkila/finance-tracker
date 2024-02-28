<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="./css/income-style.css" rel="stylesheet" />
    <title>Finance Dashboard</title>
</head>
<body>
<div class="finance-dashboard">
    <div class="header"></div>
    <div class="user-info"></div>
    <span class="app-title">Finance</span>
    <div class="menu">
        <span class="menu-item">Dashboard</span>
        <span class="menu-item">Income</span>
        <span class="menu-item">Expense</span>
        <span class="menu-item">Budget</span>
    </div>

    <!-- Income Page -->
    <div class="income-page">
        <h2>Income Page</h2>
        <!-- Form to add income details -->
        <form>
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" required>

            <label for="source">Source:</label>
            <input type="text" id="source" name="source" required>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>

            <button type="submit">Add Income</button>
        </form>

        <!-- Table to display existing income entries -->
        <table>
            <thead>
            <tr>
                <th>Amount</th>
                <th>Source</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <!-- Existing income entries will be populated here dynamically -->
            <tr>
                <td>$1000</td>
                <td>Salary</td>
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
</div>
</body>
</html>
