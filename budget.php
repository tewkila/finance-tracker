<?php
session_start();
require_once 'settings/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $category = $_POST['category'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');

    if ($amount === false || $amount < 0) {
        $error_message = "Invalid amount. Please enter a non-negative decimal number.";
    } elseif (new DateTime($date) > new DateTime()) {
        $error_message = "Future dates are not allowed.";
    } else {
        $link->begin_transaction();
        try {
            $budget_id = $_POST['budget_id'] ?? null;
            if ($action === 'edit' && $budget_id) {
                $stmt = $link->prepare("UPDATE budgets SET amount = ?, category = ?, date = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("dssii", $amount, $category, $date, $budget_id, $user_id);
            } elseif ($action === 'add') {
                // Prevent adding multiple budgets for the same category
                $checkStmt = $link->prepare("SELECT id FROM budgets WHERE category = ? AND user_id = ?");
                $checkStmt->bind_param("si", $category, $user_id);
                $checkStmt->execute();
                if ($checkStmt->get_result()->fetch_assoc()) {
                    throw new Exception("A budget for this category already exists. Please edit the existing budget.");
                }
                $checkStmt->close();
                $stmt = $link->prepare("INSERT INTO budgets (user_id, category, amount, date) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isds", $user_id, $category, $amount, $date);
            } elseif ($action === 'delete' && $budget_id) {
                $stmt = $link->prepare("DELETE FROM budgets WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $budget_id, $user_id);
            } else {
                throw new Exception("Invalid action or missing budget ID.");
            }

            if (!$stmt->execute()) {
                throw new Exception("Failed to execute budget operation: " . $stmt->error);
            }
            $stmt->close();
            $link->commit();
        } catch (Exception $e) {
            $link->rollback();
            $error_message = $e->getMessage();
        }
    }
}


// Fetch budgets from the database
$stmt = $link->prepare("SELECT id, category, amount, date FROM budgets WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$budgets = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Determine if we are editing an existing budget
$editMode = isset($_GET['edit']);
$editBudgetId = $editMode ? $_GET['edit'] : null;
$editBudget = null;

if ($editMode) {
    foreach ($budgets as $budget) {
        if ($budget['id'] == $editBudgetId) {
            $editBudget = $budget;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="assets/css/budget-style.css" rel="stylesheet">
    <title>Budget</title>
</head>
<body>
<div class="header">
    <a href="index.php" class="app-title">Finanss</a>
</div>
<div class="menu">
    <a href="dashboard.php" class="menu-item">Dashboard</a>
    <a href="income.php" class="menu-item">Income</a>
    <a href="expense.php" class="menu-item">Expense</a>
    <a href="budget.php" class="menu-item">Budget</a>
</div>
<div class="budget-page">
    <h2>Budget Page</h2>
    <?php if ($error_message): ?>
        <p class="error"><?= $error_message; ?></p>
    <?php endif; ?>
    <form action="budget.php" method="post">
        <div class="form-row">
            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="Groceries" <?= $editBudget && $editBudget['category'] == 'Groceries' ? 'selected' : ''; ?>>Groceries</option>
                <option value="Utilities" <?= $editBudget && $editBudget['category'] == 'Utilities' ? 'selected' : ''; ?>>Utilities</option>
                <option value="Entertainment" <?= $editBudget && $editBudget['category'] == 'Entertainment' ? 'selected' : ''; ?>>Entertainment</option>
            </select>
        </div>
        <div class="form-row">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" required min="0" step="0.01" value="<?= $editBudget ? htmlspecialchars($editBudget['amount']) : ''; ?>">
        </div>
        <div class="form-row">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required max="<?= date('Y-m-d'); ?>" value="<?= $editBudget ? $editBudget['date'] : ''; ?>">
        </div>
        <input type="hidden" name="action" value="<?= $editMode ? 'edit' : 'add'; ?>">
        <input type="hidden" name="budget_id" value="<?= $editBudgetId; ?>">
        <button type="submit"><?= $editMode ? 'Update' : 'Add'; ?></button>
    </form>
    <table>
        <thead>
        <tr>
            <th>Category</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($budgets as $budget): ?>
            <tr>
                <td><?= htmlspecialchars($budget['category']); ?></td>
                <td><?= htmlspecialchars($budget['amount']); ?></td>
                <td><?= htmlspecialchars($budget['date']); ?></td>
                <td>
                    <form action="budget.php" method="get" style="display: inline;">
                        <input type="hidden" name="edit" value="<?= $budget['id']; ?>">
                        <button type="submit" class="button-link">Edit</button>
                    </form>

                    <form action="budget.php" method="post" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="budget_id" value="<?= $budget['id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
