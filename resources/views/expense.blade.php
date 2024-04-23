<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="{{ asset('css/expense-style.css') }}" rel="stylesheet">
    <title>Expense</title>
</head>
<body>
<div class="header"></div>
<div class="user-info"></div>
<a href="#" class="app-title">Finanss</a>
<div class="menu">
    <a href="{{ route('dashboard') }}" class="menu-item">Dashboard</a>
    <a href="{{ route('income') }}" class="menu-item">Income</a>
    <a href="{{ route('expense') }}" class="menu-item">Expense</a>
    <a href="{{ route('budget') }}" class="menu-item">Budget</a>
</div>

<!-- Expense Page -->
<div class="expense-page">
    <h2>Expense Page</h2>
    <!-- Form to add expense details -->
    <form action="#" method="post">
        <input type="hidden" id="edit_key" name="edit_key" >
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" value="required">

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="Groceries">Groceries</option>
            <option value="Utilities">Utilities</option>
            <option value="Entertainment">Entertainment</option>
        </select>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="required">

        <button type="submit" name="submit">submit</button>
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
        </tbody>
    </table>
</div>
</body>
</html>
