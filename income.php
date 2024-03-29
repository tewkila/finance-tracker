<?php
session_start(); // Start the session

// Check if income data exists in the session, if not, initialize it
if (!isset($_SESSION['incomeData'])) {
    $_SESSION['incomeData'] = [];
}

// Check if form is submitted
if(isset($_POST['submit'])){
    // Retrieve form data
    $amount = $_POST['amount'];
    $source = $_POST['source'];
    $date = $_POST['date'];

    if(isset($_POST['edit_key']) && $_POST['edit_key'] !== '') {
        // Edit existing entry
        $editKey = $_POST['edit_key'];
        $_SESSION['incomeData'][$editKey] = [
            'amount' => $amount,
            'source' => $source,
            'date' => $date
        ];
    } else {
        // Add new income to the income data array in the session
        $_SESSION['incomeData'][] = [
            'amount' => $amount,
            'source' => $source,
            'date' => $date
        ];
    }
}

// Check if delete button is clicked
if(isset($_POST['delete'])) {
    $deleteKey = $_POST['delete_key'];
    unset($_SESSION['incomeData'][$deleteKey]); // Delete the entry from the session array
    header("Location: income.php"); // Redirect to refresh the page
}

// Check if edit button is clicked
if(isset($_POST['edit'])) {
    $editKey = $_POST['edit_key'];
    // Populate the form fields with existing data for editing
    $editIncome = $_SESSION['incomeData'][$editKey];
    $editAmount = $editIncome['amount'];
    $editSource = $editIncome['source'];
    $editDate = $editIncome['date'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="./css/income-style.css" rel="stylesheet" />
    <title>Income</title>
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

<!-- Income Page -->
<div class="income-page">
    <h2>Income Page</h2>
    <!-- Form to add income details -->
    <form action="income.php" method="post">
        <input type="hidden" id="edit_key" name="edit_key" value="<?php if(isset($editKey)) echo $editKey; ?>">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" value="<?php if(isset($editAmount)) echo $editAmount; ?>" required>

        <label for="source">Source:</label>
        <input type="text" id="source" name="source" value="<?php if(isset($editSource)) echo $editSource; ?>" required>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php if(isset($editDate)) echo $editDate; ?>" required>

        <button type="submit" name="submit"><?php if(isset($editKey)) echo "Update"; else echo "Submit"; ?></button>
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
        <?php
        // Loop through income data array and display each entry in the table
        foreach ($_SESSION['incomeData'] as $key => $income) {
            echo "<tr>";
            echo "<td>" . $income['amount'] . "</td>";
            echo "<td>" . $income['source'] . "</td>";
            echo "<td>" . $income['date'] . "</td>";
            echo "<td>";
            // Add edit and delete buttons for each entry
            echo "<form action='income.php' method='post' style='display: inline;'>";
            echo "<input type='hidden' name='edit_key' value='$key'>";
            echo "<button type='submit' name='edit'>Edit</button>";
            echo "</form>";
            echo "<form action='income.php' method='post' style='display: inline;'>";
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
