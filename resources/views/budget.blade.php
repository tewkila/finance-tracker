<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="{{ asset('css/budget-style.css') }}" rel="stylesheet">
    <title>Budget</title>
</head>
<body>
<div class="header"></div>
<div class="user-info"></div>
<a href="#" class="app-title">Finanss</a>

<!-- Menu -->
<div class="menu">
    <a href="{{ route('dashboard') }}" class="menu-item">Dashboard</a>
    <a href="{{ route('income') }}" class="menu-item">Income</a>
    <a href="{{ route('expense') }}" class="menu-item">Expense</a>
    <a href="{{ route('budget') }}" class="menu-item">Budget</a>
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
</div>

</body>
</html>
