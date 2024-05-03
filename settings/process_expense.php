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
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $category = $_POST['category'];
    $date = $_POST['date'];

    // Validate non-negative amount
    if ($amount < 0) {
        throw new Exception("Amount must be a non-negative value.");
    }

    // Prevent future dates
    if ($date > date('Y-m-d')) {
        throw new Exception("Future dates are not allowed.");
    }

    if ($action === 'add') {
        // Check if budget is available
        $stmt = $link->prepare("SELECT amount FROM budgets WHERE user_id = ? AND category = ?");
        $stmt->bind_param("is", $user_id, $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $budgetRow = $result->fetch_assoc();
        $stmt->close();

        $availableBudget = $budgetRow ? $budgetRow['amount'] : 0;

        if ($amount > $availableBudget) {
            throw new Exception("This expense exceeds the budget for the selected category.");
        }

        $stmt = $link->prepare("INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $user_id, $amount, $category, $date);

        // Update the budget
        $stmtBudget = $link->prepare("UPDATE budgets SET amount = amount - ? WHERE user_id = ? AND category = ?");
        $stmtBudget->bind_param("dis", $amount, $user_id, $category);
        $stmtBudget->execute();
        $stmtBudget->close();
    } elseif ($action === 'edit' && $expense_id) {
        $stmt = $link->prepare("UPDATE expenses SET amount = ?, category = ?, date = ? WHERE id = ?");
        $stmt->bind_param("dssi", $amount, $category, $date, $expense_id);
    } else if ($action === 'delete' && $expense_id) {
        // Find out the category and amount of the expense before deletion
        $stmt = $link->prepare("SELECT amount, category FROM expenses WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $expense_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $expenseRow = $result->fetch_assoc();
        $stmt->close();

        if ($expenseRow) {
            $stmt = $link->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $expense_id, $user_id);

            // Restore the budget
            $stmtBudget = $link->prepare("UPDATE budgets SET amount = amount + ? WHERE user_id = ? AND category = ?");
            $stmtBudget->bind_param("dis", $expenseRow['amount'], $user_id, $expenseRow['category']);
            $stmtBudget->execute();
            $stmtBudget->close();
        }
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
