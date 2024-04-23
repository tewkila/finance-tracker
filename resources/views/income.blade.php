<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="{{ asset('css/income-style.css') }}" rel="stylesheet">
    <title>Income</title>
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

<!-- Income Page -->
<div class="income-page">
    <h2>Income Page</h2>
    <!-- Form to add income details -->
    <form action="#" method="post">
        <input type="hidden" id="edit_key" name="edit_key" value="required">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" value="required">

        <label for="source">Source:</label>
        <input type="text" id="source" name="source" value="required">

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="required">

        <button type="submit" name="submit">submit</button>
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
        </tbody>
    </table>
</div>
</body>
</html>
