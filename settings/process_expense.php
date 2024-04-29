<?php
session_start();
require_once '../settings/config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "User not logged in.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? null; // Get the action to be performed

switch ($action) {
    case 'add':
    case 'edit':
        $expense_id = $_POST['expense_id'] ?? null;
        $amount = $_POST['amount'];
        $category = $_POST['category'];
        $date = $_POST['date'];

        // Check if budget allows for this expense
        $stmt = $link->prepare("SELECT amount FROM budgets WHERE user_id = ? AND category = ?");
        $stmt->bind_param("is", $user_id, $category);
        $stmt->execute();
        $budget_result = $stmt->get_result();
        $budget = $budget_result->fetch_assoc();

        if ($budget && $budget['amount'] < $amount) {
            $_SESSION['message'] = "Warning: Budget exceeded for $category.";
            header("Location: ../expense.php");  // Redirect to a warning page or back to form
            exit;
        }

        if ($action == 'add') {
            $stmt = $link->prepare("INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $user_id, $amount, $category, $date);
        } else {
            $stmt = $link->prepare("UPDATE expenses SET amount = ?, category = ?, date = ? WHERE id = ?");
            $stmt->bind_param("issi", $amount, $category, $date, $expense_id);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Expense saved successfully.";
        } else {
            $_SESSION['message'] = "Error saving expense: " . $stmt->error;
        }
        $stmt->close();
        break;

    case 'delete':
        $expense_id = $_POST['expense_id'];
        $stmt = $link->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $expense_id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Expense deleted successfully.";
        } else {
            $_SESSION['message'] = "Failed to delete expense.";
        }
        $stmt->close();
        break;

    default:
        $_SESSION['message'] = "Invalid action.";
}

header("Location: ../expense.php");
exit;
