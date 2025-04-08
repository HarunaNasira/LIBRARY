<?php
session_start();
require_once '../config/db_connect.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $role = sanitize($_POST['role']);
    
    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username and password are required";
        redirect("../index.php");
    }
    
    // Verify credentials
    $sql = "SELECT * FROM users WHERE username = '$username' AND role = '$role'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Create session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                // redirect("../admin/dashboard.php");
                redirect("../pages/dashboard.php");
            } else {
                redirect("../user/dashboard.php");
            }
        } else {
            $_SESSION['error'] = "Invalid password";
            redirect("../index.php");
        }
    } else {
        $_SESSION['error'] = "User not found or incorrect role";
        redirect("../index.php");
    }
} else {
    // Redirect to login page if accessed directly
    redirect("../index.php");
}
?>
