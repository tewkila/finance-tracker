<?php
session_start();
require_once '../settings/config.php';

$user_id = $_SESSION['user_id'];

if (isset($_POST['submit'])) {
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $expense_id = $_POST['expense_id'];

    if ($expense_id) {
        // Update an existing expense
        $sql = "UPDATE expenses SET amount = ?, category = ?, date = ? WHERE id = ? AND user_id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("dssii", $amount, $category, $date, $expense_id, $user_id);
    } else {
        // Insert a new expense
        $sql = "INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("idss", $user_id, $amount, $category, $date);
    }
    $stmt->execute();
    $stmt->close();
} elseif (isset($_POST['delete'])) {
    $expense_id = $_POST['delete'];
    $sql = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ii", $expense_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

$link->close();
header("Location: ../expense.php"); // Redirect back to the expense page
exit;
