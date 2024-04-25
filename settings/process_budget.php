<?php
require_once 'settings/config.php'; // Ensure this file contains the correct database connection setup

session_start(); // Start or resume a session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groceries = $link->real_escape_string($_POST['groceries']);
    $utilities = $link->real_escape_string($_POST['utilities']);
    $entertainment = $link->real_escape_string($_POST['entertainment']);

    $user_id = $_SESSION['user_id']; // Assumes user_id is stored in session

    $sql = "REPLACE INTO budgets (user_id, category, amount) VALUES
            ($user_id, 'Groceries', $groceries),
            ($user_id, 'Utilities', $utilities),
            ($user_id, 'Entertainment', $entertainment)";

    if ($link->query($sql) === TRUE) {
        echo "Budget updated successfully";
    } else {
        echo "Error updating record: " . $link->error;
    }

    $link->close();
    header("Location: dashboard.php"); // Redirects to dashboard after successful update
    exit();
}