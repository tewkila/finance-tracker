<?php
session_start();
require_once '../settings/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "User not logged in.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? null; // Get the action to be performed

$link->begin_transaction();
try {
    switch ($action) {
        case 'add':
            $stmt = $link->prepare("INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $user_id, $_POST['amount'], $_POST['category'], $_POST['date']);
            $stmt->execute();
            $stmt->close();
            break;

        case 'edit':
            $stmt = $link->prepare("UPDATE expenses SET amount = ?, category = ?, date = ? WHERE id = ?");
            $stmt->bind_param("issi", $_POST['amount'], $_POST['category'], $_POST['date'], $_POST['expense_id']);
            $stmt->execute();
            $stmt->close();
            break;

        case 'delete':
            $stmt = $link->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $_POST['expense_id'], $user_id);
            $stmt->execute();
            $stmt->close();
            break;

        default:
            throw new Exception("Invalid action.");
    }
    $link->commit();
    $_SESSION['message'] = "Action successful.";
} catch (Exception $e) {
    $link->rollback();
    $_SESSION['message'] = "Error: " . $e->getMessage();
}
header("Location: ../expense.php");
exit;
