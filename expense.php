<?php
session_start(); // Start the session

// Check if expense data exists in the session, if not, initialize it
if (!isset($_SESSION['expenseData'])) {
    $_SESSION['expenseData'] = [];
}

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Retrieve form data
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];

    if (isset($_POST['edit_key']) && $_POST['edit_key'] !== '') {
        // Edit existing entry
        $editKey = $_POST['edit_key'];
        $_SESSION['expenseData'][$editKey] = [
            'amount' => $amount,
            'category' => $category,
            'date' => $date
        ];
    } else {
        // Add new expense to the expense data array in the session
        $_SESSION['expenseData'][] = [
            'amount' => $amount,
            'category' => $category,
            'date' => $date
        ];
    }
}

// Check if delete button is clicked
if (isset($_POST['delete'])) {
    $deleteKey = $_POST['delete_key'];
    unset($_SESSION['expenseData'][$deleteKey]); // Delete the entry from the session array
    header("Location: expense.php"); // Redirect to refresh the page
}

// Check if edit button is clicked
if (isset($_POST['edit'])) {
    $editKey = $_POST['edit_key'];
    // Populate the form fields with existing data for editing
    $editExpense = $_SESSION['expenseData'][$editKey];
    $editAmount = $editExpense['amount'];
    $editCategory = $editExpense['category'];
    $editDate = $editExpense['date'];
}
?>

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
    <form action="expense.php" method="post">
        <input type="hidden" id="edit_key" name="edit_key" value="<?php if(isset($editKey)) echo $editKey; ?>">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" value="<?php if(isset($editAmount)) echo $editAmount; ?>" required>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="Groceries" <?php if(isset($editCategory) && $editCategory == "Groceries") echo "selected"; ?>>Groceries</option>
            <option value="Utilities" <?php if(isset($editCategory) && $editCategory == "Utilities") echo "selected"; ?>>Utilities</option>
            <option value="Entertainment" <?php if(isset($editCategory) && $editCategory == "Entertainment") echo "selected"; ?>>Entertainment</option>
        </select>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php if(isset($editDate)) echo $editDate; ?>" required>

        <button type="submit" name="submit"><?php if(isset($editKey)) echo "Update"; else echo "Submit"; ?></button>
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
        <?php
        // Loop through expense data array and display each entry in the table
        foreach ($_SESSION['expenseData'] as $key => $expense) {
            echo "<tr>";
            echo "<td>" . $expense['amount'] . "</td>";
            echo "<td>" . $expense['category'] . "</td>";
            echo "<td>" . $expense['date'] . "</td>";
            echo "<td>";
            // Add edit and delete buttons for each entry
            echo "<form action='expense.php' method='post' style='display: inline;'>";
            echo "<input type='hidden' name='edit_key' value='$key'>";
            echo "<button type='submit' name='edit'>Edit</button>";
            echo "</form>";
            echo "<form action='expense.php' method='post' style='display: inline;'>";
            echo "<input type='hidden' name='delete_key' value='$key'>";
            echo "<button type='submit' name='delete'>Delete</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
