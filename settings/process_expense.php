<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../settings/config.php';

if (!isset($_SESSION['user_id'])) {
    die('User not logged in.');
}

$user_id = $_SESSION['user_id'];

try {
    if (isset($_POST['submit'])) {
        $amount = $_POST['amount'];
        $category = $_POST['category'];
        $date = $_POST['date'];
        $expense_id = $_POST['expense_id'] ?? null;

        if ($expense_id) {
            $sql = "UPDATE expenses SET amount = ?, category = ?, date = ? WHERE id = ? AND user_id = ?";
            if (!$stmt = $link->prepare($sql)) throw new Exception($link->error);
            $stmt->bind_param("dssii", $amount, $category, $date, $expense_id, $user_id);
        } else {
            $sql = "INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)";
            if (!$stmt = $link->prepare($sql)) throw new Exception($link->error);
            $stmt->bind_param("idss", $user_id, $amount, $category, $date);
        }
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $expense_id = $_POST['delete'];
        $sql = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
        if (!$stmt = $link->prepare($sql)) throw new Exception($link->error);
        $stmt->bind_param("ii", $expense_id, $user_id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
    }
} catch (Exception $e) {
    die("Error occurred: " . $e->getMessage());
}

$link->close();
header("Location: ../expense.php"); // Redirect back to the expense page
exit;
