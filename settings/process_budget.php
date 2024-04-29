<?php
session_start();
require_once '../settings/config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo "User not logged in.";
        exit;
    }

    $categories = ['Groceries', 'Utilities', 'Entertainment'];
    $link->begin_transaction();
    $error = false;

    foreach ($categories as $category) {
        $amount = filter_input(INPUT_POST, strtolower($category), FILTER_VALIDATE_FLOAT);
        if ($amount === false) {
            echo "Invalid amount for $category.";
            $error = true;
            break;
        }

        $stmt = $link->prepare("REPLACE INTO budgets (user_id, category, amount) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isd", $user_id, $category, $amount);
            if (!$stmt->execute()) {
                echo "Error updating $category budget: " . $stmt->error;
                $error = true;
            }
            $stmt->close();
        } else {
            echo "Failed to prepare statement";
            $error = true;
            break;
        }
    }

    if (!$error) {
        $link->commit();
        header("Location: ../budget.php");
        exit;
    } else {
        $link->rollback();
    }

    $link->close();
}
