<?php
session_start();
require_once '../config/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_reminder = isset($_POST['email_reminder']) ? (int)$_POST['email_reminder'] : 0;
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE users SET email_reminder = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $email_reminder, $user_id);

    if ($stmt->execute()) {
        $_SESSION['email_reminder'] = $email_reminder;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
} 