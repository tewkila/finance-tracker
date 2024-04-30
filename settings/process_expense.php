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

$link->begin_transaction();
try {
    if ($action === 'add' || $action === 'edit') {
        $expense_id = $_POST['expense_id'] ?? null;
        $amount = $_POST['amount'];
        $category = $_POST['category'];
        $date = $_POST['date'];

        if ($action === 'add') {
            $stmt = $link->prepare("INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $user_id, $amount, $category, $date);
        } elseif ($action === 'edit') {
            $stmt = $link->prepare("UPDATE expenses SET amount = ?, category = ?, date = ? WHERE id = ?");
            $stmt->bind_param("issi", $amount, $category, $date, $expense_id);
        }
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'delete') {
        $expense_id = $_POST['expense_id'];
        $stmt = $link->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $expense_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    $link->commit();
    $_SESSION['message'] = "Expense processed successfully.";
} catch (Exception $e) {
    $link->rollback();
    $_SESSION['message'] = "Error: " . $e->getMessage();
}
header("Location: ../expense.php");
exit;
