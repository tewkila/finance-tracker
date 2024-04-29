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
$action = $_POST['action'] ?? null;

switch ($action) {
    case 'add':
    case 'edit':
        $link->begin_transaction();
        $error = false;

        $categories = ['Groceries', 'Utilities', 'Entertainment'];
        foreach ($categories as $category) {
            $amount = filter_input(INPUT_POST, strtolower($category), FILTER_VALIDATE_FLOAT);
            if ($amount === false) {
                $_SESSION['message'] = "Invalid amount for $category.";
                $error = true;
                break;
            }

            $stmt = $link->prepare("REPLACE INTO budgets (user_id, category, amount) VALUES (?, ?, ?)");
            if (!$stmt || !$stmt->bind_param("isd", $user_id, $category, $amount) || !$stmt->execute()) {
                $_SESSION['message'] = "Error updating $category budget: " . ($stmt->error ?? 'Prepare failed');
                $error = true;
                $stmt->close();
                break;
            }
            $stmt->close();
        }

        if (!$error) {
            $link->commit();
            $_SESSION['message'] = "Budget updated successfully.";
        } else {
            $link->rollback();
        }
        break;

    case 'delete':
        $budget_id = $_POST['budget_id'] ?? null;
        if (!$budget_id) {
            $_SESSION['message'] = "No budget specified for deletion.";
            break;
        }
        $stmt = $link->prepare("DELETE FROM budgets WHERE id = ? AND user_id = ?");
        if ($stmt && $stmt->bind_param("ii", $budget_id, $user_id) && $stmt->execute()) {
            $_SESSION['message'] = "Budget deleted successfully.";
        } else {
            $_SESSION['message'] = "Failed to delete budget.";
        }
        $stmt->close();
        break;

    default:
        $_SESSION['message'] = "Invalid action.";
}

header("Location: ../budget.php");
exit;
