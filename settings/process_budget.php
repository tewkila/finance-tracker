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
    switch ($action) {
        case 'add':
        case 'edit':
            $budget_id = $_POST['budget_id'] ?? null;
            $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
            $category = $_POST['category'] ?? '';
            $date = $_POST['date'] ?? date('Y-m-d');

            if ($amount === false || $amount < 0) {
                throw new Exception("Invalid amount. Amount must be a non-negative number.");
            }
            if (new DateTime($date) > new DateTime()) {
                throw new Exception("Future dates are not allowed.");
            }

            if ($action === 'edit' && $budget_id) {
                $stmt = $link->prepare("UPDATE budgets SET amount = ?, category = ?, date = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("dssii", $amount, $category, $date, $budget_id, $user_id);
            } else {
                $stmt = $link->prepare("INSERT INTO budgets (user_id, category, amount, date) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isds", $user_id, $category, $amount, $date);
            }

            if (!$stmt->execute()) {
                throw new Exception("Failed to process budget operation.");
            }
            $stmt->close();
            break;

        case 'delete':
            $budget_id = $_POST['budget_id'];
            if (!$budget_id) {
                throw new Exception("No budget specified for deletion.");
            }
            $stmt = $link->prepare("DELETE FROM budgets WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $budget_id, $user_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete budget.");
            }
            $stmt->close();
            break;

        default:
            throw new Exception("Invalid action.");
    }
    $link->commit();
    $_SESSION['message'] = "Budget updated successfully.";
} catch (Exception $e) {
    $link->rollback();
    $_SESSION['message'] = "Error: " . $e->getMessage();
}
header("Location: ../budget.php");
exit;
