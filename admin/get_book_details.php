<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if book_id is provided
if (!isset($_POST['book_id'])) {
    echo json_encode(['success' => false, 'message' => 'Book ID is required']);
    exit;
}

$book_id = sanitize($_POST['book_id']);

// Fetch book details
$query = "SELECT * FROM books WHERE book_id = '$book_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $book = $result->fetch_assoc();
    echo json_encode(['success' => true, 'book' => $book]);
} else {
    echo json_encode(['success' => false, 'message' => 'Book not found']);
} 