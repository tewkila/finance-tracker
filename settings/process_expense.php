<?php
session_start();
require_once '../settings/config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$user_id = $_SESSION['user_id'] ?? null; // Fallback if not set
if (!$user_id) {
    $_SESSION['message'] = "User not logged in.";
    header("Location: ../login.php");
    exit;
}

$action = $_POST['action'] ?? null; // Get the action to be performed

switch ($action) {
    case 'add':
    case 'edit':
        $categories = ['Groceries', 'Utilities', 'Entertainment'];
        $link->begin_transaction(); // Start a transaction
        $error = false;

        foreach ($categories as $category) {
            $amount = filter_input(INPUT_POST, strtolower($category), FILTER_VALIDATE_FLOAT);
            if ($amount === false) {
                $_SESSION['message'] = "Invalid amount for $category.";
                $error = true;
                break;
            }

            if ($action == 'edit') {
                $budget_id = $_POST['budget_id'][$category] ?? null;
                $stmt = $link->prepare("UPDATE budgets SET amount = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("dii", $amount, $budget_id, $user_id);
            } else {
                $stmt = $link->prepare("REPLACE INTO budgets (user_id, category, amount) VALUES (?, ?, ?)");
                $stmt->bind_param("isd", $user_id, $category, $amount);
            }

            if (!$stmt->execute()) {
                $_SESSION['message'] = "Error updating $category budget: " . $stmt->error;
                $error = true;
                $stmt->close();
                break;
            }
            $stmt->close();
        }

        if (!$error) {
            $link->commit(); // Commit the transaction
            $_SESSION['message'] = "Budget updated successfully.";
        } else {
            $link->rollback(); // Rollback if any errors
        }
        break;

    case 'delete':
        $budget_id = $_POST['budget_id'] ?? null;
        if (!$budget_id) {
            $_SESSION['message'] = "No budget specified for deletion.";
            break;
        }
        $stmt = $link->prepare("DELETE FROM budgets WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $budget_id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Budget deleted successfully.";
        } else {
            $_SESSION['message'] = "Failed to delete budget.";
        }
        $stmt->close();
        break;

    default:
        $_SESSION['message'] = "Invalid action.";
}

$link->close();
header("Location: ../budget.php");
exit;
?>
