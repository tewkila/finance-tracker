<?php
session_start();
require_once '../settings/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "User not logged in.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $budget_id = $_POST['budget_id'] ?? null;
    $category = $_POST['category'] ?? null;
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $date = $_POST['date'];

    if ($amount === false || $amount < 0) {
        $_SESSION['message'] = "Invalid amount. Please enter a non-negative decimal number.";
    } elseif (new DateTime($date) > new DateTime()) {
        $_SESSION['message'] = "Future dates are not allowed.";
    } else {
        $link->begin_transaction();
        try {
            if ($action === 'edit' && $budget_id) {
                $stmt = $link->prepare("UPDATE budgets SET amount = ?, category = ?, date = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("dssii", $amount, $category, $date, $budget_id, $user_id);
            } elseif ($action === 'add') {
                // Check for existing category
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
                throw new Exception("Database error: " . $stmt->error);
            }
            $stmt->close();
            $link->commit();
            $_SESSION['message'] = "Budget operation successful.";
        } catch (Exception $e) {
            $link->rollback();
            $_SESSION['message'] = $e->getMessage();
        }
    }
    header("Location: ../budget.php");
    exit;
}
