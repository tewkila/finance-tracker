<?php
session_start();
require_once 'settings/config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

if (isset($_GET['category'])) {
    $category = $_GET['category'];
    $user_id = $_SESSION['user_id'];

    $stmt = $link->prepare("SELECT amount FROM budgets WHERE user_id = ? AND category = ?");
    if (!$stmt) {
        echo json_encode(['error' => 'Database error: Unable to prepare statement']);
        exit;
    }
    $stmt->bind_param("is", $user_id, $category);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Query failed: ' . $stmt->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

