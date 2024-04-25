<?php
require_once 'settings/config.php';
session_start();

if (isset($_GET['category']) && !empty($_SESSION['user_id'])) {
    $category = $_GET['category'];
    $user_id = $_SESSION['user_id'];  // Make sure user_id is securely retrieved

    $query = "SELECT amount FROM budgets WHERE user_id = ? AND category = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("is", $user_id, $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $stmt->close();
    $link->close();

    echo json_encode($data); // Return the amount as a JSON object
} else {
    echo json_encode(['error' => 'Invalid request']); // Error handling for invalid access
}
