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

    $categories = ['Groceries', 'Utilities', 'Entertainment'];
    $link->begin_transaction(); // Start a transaction
    $error = false;

    foreach ($categories as $category) {
        $amount = filter_input(INPUT_POST, strtolower($category), FILTER_VALIDATE_FLOAT);
        if ($amount === false) {
            echo "Invalid amount for $category.";
            $error = true;
            break;
        }

        $stmt = $link->prepare("REPLACE INTO budgets (user_id, category, amount) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $user_id, $category, $amount);
        if (!$stmt->execute()) {
            echo "Error updating $category budget: " . $stmt->error;
            $error = true;
            $stmt->close();
            break;
        }
        $stmt->close();
    }

    if (!$error) {
        $link->commit(); // Commit the transaction
        header("Location: ../budget.php");
    } else {
        $link->rollback(); // Rollback if any errors
    }

    $link->close();
    exit;
}
