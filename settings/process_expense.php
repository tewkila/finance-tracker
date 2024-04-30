<?php
session_start();
require_once '../settings/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "User not logged in.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$link->begin_transaction();
try {
    $action = $_POST['action'] ?? null;
    $expense_id = $_POST['expense_id'] ?? null;
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];

    // Validate non-negative amount
    if (floatval($amount) < 0) {
        throw new Exception("Amount must be a non-negative value.");
    }

    // Prevent future dates
    if ($date > date('Y-m-d')) {
        throw new Exception("Future dates are not allowed.");
    }

    if ($action === 'add') {
        $stmt = $link->prepare("INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $user_id, $amount, $category, $date);
    } elseif ($action === 'edit' && $expense_id) {
        $stmt = $link->prepare("UPDATE expenses SET amount = ?, category = ?, date = ? WHERE id = ?");
        $stmt->bind_param("dssi", $amount, $category, $date, $expense_id);
    } else if ($action === 'delete' && $expense_id) {
        $stmt = $link->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $expense_id, $user_id);
    } else {
        throw new Exception("Invalid action or expense ID.");
    }

    if (!$stmt->execute()) {
        throw new Exception("Error executing SQL statement: " . $stmt->error);
    }

    $stmt->close();
    $link->commit();
    $_SESSION['message'] = "Expense processed successfully.";
    header("Location: ../expense.php");
    exit;
} catch (Exception $e) {
    $link->rollback();
    $_SESSION['message'] = "Error: " . $e->getMessage();
    header("Location: ../expense.php");
    exit;
}
