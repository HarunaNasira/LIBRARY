<?php 
	session_start();
    require_once '../config/db_connect.php';
    
    // Check if user is logged in and is an admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['error'] = "You must be logged in as an admin to access this page";
        redirect("../index.php");
    }
    
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
        
        // Check if username already exists
        $check_username_query = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($check_username_query);
        if ($result->num_rows > 0) {
            $errors[] = "Username already exists";
        }
        
        // Check if email already exists
        $check_email_query = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($check_email_query);
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists";
        }
        
        // Insert user if there's no error
        if (empty($errors)) {
            // Default password
            $password = '123456';
            $hashed_password = password_hash($password, PASSWORD_ARGON2I);
            
            $insert_query = "INSERT INTO users (username, password, full_name, email, role) 
                             VALUES ('$username', '$hashed_password', '$full_name', '$email', '$role')";
            
            if ($conn->query($insert_query)) {
                $_SESSION['success'] = "User added successfully!";
                redirect("view_users.php");
            } else {
                $errors[] = "Error adding user: " . $conn->error;
            }
        }
    }

    // Include the header after all redirects
	include('../includes/dashboard_header.php'); 
?>

	<div class="content-wrapper">

		<!-- Page Title and Actions -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>Manage Users</h5>
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
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" required>
                                        <small class="form-text text-muted">Username must be unique</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="full_name">Full Name *</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($_POST['full_name']) ? $_POST['full_name'] : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                                        <small class="form-text text-muted">Email must be unique</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">Role *</label>
                                        <select class="form-control" id="role" name="role" required>
                                            <option value="user" <?php echo (isset($_POST['role']) && $_POST['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                            <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Password *</label>
                                        <input type="text" class="form-control" id="password" name="password" value="123456" disabled>
                                        <label class="text-warning fw-medium">(Default: 123456. User can change later)</label>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary mr-2">Add User</button>
                            <a href="view_users.php" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

	</div>


<?php include_once('../includes/dashboard_footer.php'); ?>
