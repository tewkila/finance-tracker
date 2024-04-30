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
    $action = $_POST['action'] ?? '';
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
            if ($action === 'edit' && isset($_POST['budget_id'])) {
                $budget_id = $_POST['budget_id'];
                $stmt = $link->prepare("UPDATE budgets SET amount = ?, category = ?, date = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("dssii", $amount, $category, $date, $budget_id, $user_id);
            } else {
                $stmt = $link->prepare("INSERT INTO budgets (user_id, category, amount, date) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isds", $user_id, $category, $amount, $date);
            }
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute budget operation.");
            }
            $stmt->close();
            $link->commit();
            header("Location: budget.php");
            exit;
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
                <option value="Groceries">Groceries</option>
                <option value="Utilities">Utilities</option>
                <option value="Entertainment">Entertainment</option>
            </select>
        </div>
        <div class="form-row">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" required min="0" step="0.01">
        </div>
        <div class="form-row">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required max="<?= date('Y-m-d'); ?>">
        </div>
        <input type="hidden" name="action" value="<?= isset($_GET['edit']) ? 'edit' : 'add'; ?>">
        <input type="hidden" name="budget_id" value="<?= $_GET['edit'] ?? ''; ?>">
        <button type="submit"><?= isset($_GET['edit']) ? 'Update' : 'Add'; ?></button>
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
                    <a href="?edit=<?= $budget['id']; ?>" class="button">Edit</a>
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
