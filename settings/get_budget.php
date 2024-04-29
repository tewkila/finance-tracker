<?php
require_once 'settings/config.php';
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['category']) && !empty($_SESSION['user_id'])) {
    $category = $_GET['category'];
    $user_id = $_SESSION['user_id'];

    $query = "SELECT amount FROM budgets WHERE user_id = ? AND category = ?";
    $stmt = $link->prepare($query);
    if ($stmt) {
        $stmt->bind_param("is", $user_id, $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $stmt->close();
        $link->close();

        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Failed to prepare statement']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
