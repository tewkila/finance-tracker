<?php
session_start();
require_once 'settings/config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming user_id is stored in session upon login
    $user_id = $_SESSION['user_id'];

    // Prepare and bind
    $stmt = $link->prepare("REPLACE INTO budgets (user_id, category, amount) VALUES (?, ?, ?), (?, ?, ?), (?, ?, ?)");

    // Sanitize and validate inputs
    $groceries = filter_input(INPUT_POST, 'groceries', FILTER_VALIDATE_FLOAT);
    $utilities = filter_input(INPUT_POST, 'utilities', FILTER_VALIDATE_FLOAT);
    $entertainment = filter_input(INPUT_POST, 'entertainment', FILTER_VALIDATE_FLOAT);

    // Execute statement multiple times for different categories
    $stmt->bind_param("isdisdisd",
        $user_id, "Groceries", $groceries,
        $user_id, "Utilities", $utilities,
        $user_id, "Entertainment", $entertainment);

    // Execute and check for errors
    if ($stmt->execute()) {
        echo "Budget updated successfully.";
        // Redirect back to budget page or to a confirmation page
        header("Location: budget.php");
        exit;
    } else {
        echo "Error updating record: " . $link->error;
    }

    $stmt->close();
    $link->close();
}