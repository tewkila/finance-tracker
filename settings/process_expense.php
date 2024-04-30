<?php
session_start();
require_once '../settings/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "User not logged in.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? null;
$confirm = $_POST['confirm'] ?? null; // Check if this is a confirmation of budget exceedance

try {
    $link->begin_transaction();

    if ($action === 'add' || $action === 'edit') {
        $expense_id = $_POST['expense_id'] ?? null;
        $amount = $_POST['amount'];
        $category = $_POST['category'];
        $date = $_POST['date'];

        // Sanitize inputs to avoid XSS when echoed back in HTML
        $amount = htmlspecialchars($amount);
        $category = htmlspecialchars($category);
        $date = htmlspecialchars($date);

        // Check current budget for the category if not confirming an override
        if (!$confirm) {
            $stmt = $link->prepare("SELECT amount FROM budgets WHERE user_id = ? AND category = ?");
            $stmt->bind_param("is", $user_id, $category);
            $stmt->execute();
            $budget_result = $stmt->get_result();
            $budget = $budget_result->fetch_assoc();

            if ($budget && ($budget['amount'] < $amount)) {
                // Budget exceeded and not yet confirmed by user
                $_SESSION['over_budget'] = [
                    'action' => $action,
                    'expense_id' => $expense_id,
                    'amount' => $amount,
                    'category' => $category,
                    'date' => $date
                ];
                $_SESSION['message'] = "Warning: Budget exceeded for $category. Do you want to continue anyway?";
                header("Location: ../expense.php"); // Redirect to expense page to show confirmation message
                exit;
            }
        }

        // Proceed with adding or editing the expense
        if ($action === 'add') {
            $stmt = $link->prepare("INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $user_id, $amount, $category, $date);
        } else {
            $stmt = $link->prepare("UPDATE expenses SET amount = ?, category = ?, date = ? WHERE id = ?");
            $stmt->bind_param("issi", $amount, $category, $date, $expense_id);
        }
        $stmt->execute();
    } elseif ($action === 'delete') {
        $expense_id = $_POST['expense_id'];
        $stmt = $link->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $expense_id, $user_id);
        $stmt->execute();
    } else {
        throw new Exception("Invalid action.");
    }

    $link->commit();
    $_SESSION['message'] = "Expense processed successfully.";
} catch (Exception $e) {
    $link->rollback();
    $_SESSION['message'] = "Error: " . $e->getMessage();
}

header("Location: ../expense.php");
exit;
