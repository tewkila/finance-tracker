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

$link->begin_transaction();
$error = false;

try {
    switch ($action) {
        case 'add':
        case 'edit':
            $expense_id = $_POST['expense_id'] ?? null;
            $amount = $_POST['amount'] ?? 0;
            $category = $_POST['category'];
            $date = $_POST['date'];

            // Check if budget allows for this expense
            $stmt = $link->prepare("SELECT amount FROM budgets WHERE user_id = ? AND category = ?");
            $stmt->bind_param("is", $user_id, $category);
            $stmt->execute();
            $budget_result = $stmt->get_result();
            $budget = $budget_result->fetch_assoc();

            if ($budget['amount'] < $amount) {
                $_SESSION['message'] = "Warning: Budget exceeded for $category.";
                $error = true;
                break;
            }

            if ($action == 'add') {
                $stmt = $link->prepare("INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)");
            } else {
                $stmt = $link->prepare("UPDATE expenses SET amount = ?, category = ?, date = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("issii", $amount, $category, $date, $expense_id, $user_id);
            }

            if (!$stmt->execute()) {
                throw new Exception("Error processing expense: " . $stmt->error);
            }
            break;

        case 'delete':
            $expense_id = $_POST['expense_id'] ?? null;
            if (!$expense_id) {
                throw new Exception("No expense specified for deletion.");
            }
            $stmt = $link->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $expense_id, $user_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete expense.");
            }
            break;

        default:
            throw new Exception("Invalid action.");
    }

    if (!$error) {
        $link->commit();
        $_SESSION['message'] = "Expense processed successfully.";
    } else {
        $link->rollback();
    }
} catch (Exception $e) {
    $link->rollback();
    $_SESSION['message'] = $e->getMessage();
}

$link->close();
header("Location: ../expense.php");
exit;