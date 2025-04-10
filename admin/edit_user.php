<?php 
	session_start();
    require_once '../config/db_connect.php';
    
    // Check if user is logged in and is an admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['error'] = "You must be logged in as an admin to access this page";
        redirect("../index.php");
    }
    
    // Get user ID from URL
    if (!isset($_GET['id'])) {
        $_SESSION['error'] = "No user ID provided";
        redirect("view_users.php");
    }
    
    $user_id = sanitize($_GET['id']);
    
    // Get user data
    $user_query = "SELECT * FROM users WHERE user_id = '$user_id'";
    $user_result = $conn->query($user_query);
    
    if ($user_result->num_rows == 0) {
        $_SESSION['error'] = "User not found";
        redirect("view_users.php");
    }
    
    $user = $user_result->fetch_assoc();
    
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $username = sanitize($_POST['username']);
        $full_name = sanitize($_POST['full_name']);
        $email = sanitize($_POST['email']);
        $role = sanitize($_POST['role']);
        
        // Validate input
        $errors = [];
        
        if (empty($username)) {
            $errors[] = "Username is required";
        }
        
        if (empty($full_name)) {
            $errors[] = "Full name is required";
        }
        
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        $check_username_query = "SELECT * FROM users WHERE username = '$username' AND user_id != '$user_id'";
        $result = $conn->query($check_username_query);
        if ($result->num_rows > 0) {
            $errors[] = "Username already exists";
        }
        
        $check_email_query = "SELECT * FROM users WHERE email = '$email' AND user_id != '$user_id'";
        $result = $conn->query($check_email_query);
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists";
        }
        
        if (empty($errors)) {
            $update_query = "UPDATE users SET 
                            username = '$username',
                            full_name = '$full_name',
                            email = '$email',
                            role = '$role'
                            WHERE user_id = '$user_id'";
            
            if ($conn->query($update_query)) {
                // If current user changed their own role from admin to user
                if ($user_id == $_SESSION['user_id'] && $role == 'user' && $user['role'] == 'admin') {
                    $_SESSION['role_change_message'] = "Your role has been changed. Please sign in again.";
                    session_destroy();
                    redirect("../index.php");
                } else {
                    $_SESSION['success'] = "User updated successfully!";
                    redirect("view_users.php");
                }
            } else {
                $errors[] = "Error updating user: " . $conn->error;
            }
        }
    }

    // Include the header after all redirects
	include('../includes/dashboard_header.php'); 
?>

	<div class="content-wrapper">

		<!-- Page Title and Actions -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>Edit User</h5>
            <div class="d-flex gap-2">
                <a href="view_users.php" class="btn btn-secondary btn-icon-text">
                    Back to Users
                </a>
            </div>
        </div>

        <!-- Messages -->
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($errors) && !empty($errors)) {
            echo '<div class="alert alert-danger"><ul>';
            foreach ($errors as $error) {
                echo '<li>' . $error . '</li>';
            }
            echo '</ul></div>';
        }
        ?>

        <!-- Form -->
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">User Information</h4>
                        <form class="forms-sample" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Username *</label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                                        <small class="form-text text-muted">Username must be unique</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="full_name">Full Name *</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                                        <small class="form-text text-muted">Email must be unique</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">Role *</label>
                                        <select class="form-control" id="role" name="role" required>
                                            <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary mr-2">Update User</button>
                            <a href="view_users.php" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

	</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const roleSelect = document.getElementById('role');
            const currentUserId = <?php echo $_SESSION['user_id']; ?>;
            const editingUserId = <?php echo $user_id; ?>;
            
            form.addEventListener('submit', function(e) {
                // Check if admin is editing their own account and changing role to user
                if (currentUserId === editingUserId && roleSelect.value === 'user') {
                    e.preventDefault();
                    if (confirm('Warning: Changing your role to user will log you out. Are you sure you want to proceed?')) {
                        form.submit();
                    }
                }
            });
        });
    </script>

<?php include_once('../includes/dashboard_footer.php'); ?> 