<?php
session_start();
require_once 'settings/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission for adding or updating income
if (isset($_POST['submit'])) {
    // Explicitly replace commas, trim spaces, and validate the input
    $amountInput = trim(str_replace(',', '.', $_POST['amount']));
    $amount = filter_var($amountInput, FILTER_VALIDATE_FLOAT, ["flags" => FILTER_FLAG_ALLOW_FRACTION]);

    // Check for a valid, non-negative float
    if ($amount === false || $amount < 0) {
        echo "Invalid amount format. Please enter a valid, non-negative number (e.g., 10.67).";
        exit;
    }

    $source = htmlspecialchars($_POST['source']);
    $date = $_POST['date'];

    // Prevent future dates
    $currentDate = date('Y-m-d');
    if ($date > $currentDate) {
        echo "Future dates are not allowed.";
        exit;
    }

    $editKey = $_POST['edit_key'] ?? '';

    if ($editKey) {
        // Update existing income
        $stmt = $link->prepare("UPDATE incomes SET amount = ?, source = ?, date = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->bind_param("dssii", $amount, $source, $date, $editKey, $user_id);
    } else {
        // Insert new income
        $stmt = $link->prepare("INSERT INTO incomes (user_id, amount, source, date, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("idss", $user_id, $amount, $source, $date);
    }

    if ($stmt->execute()) {
        header("Location: income.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle delete request
if (isset($_POST['delete'])) {
    $deleteKey = $_POST['delete_key'];
    $stmt = $link->prepare("DELETE FROM incomes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $deleteKey, $user_id);
    if ($stmt->execute()) {
        header("Location: income.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch existing income entries from the database
$incomeEntries = [];
$stmt = $link->prepare("SELECT id, amount, source, date FROM incomes WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $incomeEntries[] = $row;
    }
}
$stmt->close();

// Check if edit button is clicked and prepare data for editing
if (isset($_POST['edit'])) {
    $editKey = $_POST['edit_key'];
    $stmt = $link->prepare("SELECT amount, source, date FROM incomes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $editKey, $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $editIncome = $result->fetch_assoc();
        } else {
            echo "No record found for ID: $editKey";
        }
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="assets/css/income-style.css" rel="stylesheet" />
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

<div class="income-page">
    <h2>Income Page</h2>
    <form action="income.php" method="post">
        <input type="hidden" id="edit_key" name="edit_key" value="<?= $editKey ?>">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" value="<?= isset($editIncome['amount']) ? $editIncome['amount'] : '' ?>" required min="0" step="0.01">


        <label for="source">Source:</label>
        <input type="text" id="source" name="source" value="<?= $editIncome['source'] ?>" required>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?= $editIncome['date'] ?>" required>

        <button type="submit" name="submit"><?= $editKey ? "Update" : "Submit" ?></button>
    </form>

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
        <?php foreach ($incomeEntries as $income): ?>
            <tr>
                <td><?= htmlspecialchars($income['amount']) ?></td>
                <td><?= htmlspecialchars($income['source']) ?></td>
                <td><?= htmlspecialchars($income['date']) ?></td>
                <td>
                    <form action="income.php" method="post" style="display: inline;">
                        <input type="hidden" name="edit_key" value="<?= $income['id'] ?>">
                        <button type="submit" name="edit">Edit</button>
                    </form>
                    <form action="income.php" method="post" style="display: inline;">
                        <input type="hidden" name="delete_key" value="<?= $income['id'] ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>