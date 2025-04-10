<?php 
	session_start();
    require_once '../config/db_connect.php';
    
    // Check if user is logged in and is an admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['error'] = "You must be logged in as an admin to access this page";
        redirect("../index.php");
    }
    
    // Handle delete request
    if (isset($_GET['delete'])) {
        $user_id = sanitize($_GET['delete']);
        
        // Prevent deleting self
        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['error'] = "You cannot delete your own account!";
        } else {
            // Delete the user
            $delete_query = "DELETE FROM users WHERE user_id = '$user_id' AND role != 'admin'";
            if ($conn->query($delete_query)) {
                $_SESSION['success'] = "User deleted successfully!";
            } else {
                $_SESSION['error'] = "Error deleting user: " . $conn->error;
            }
        }
        
        redirect("view_users.php");
    }
    
    // Get all users with pagination
    $items_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $items_per_page;
    
    // Search functionality
    $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
    $search_condition = '';
    if (!empty($search)) {
        $search_condition = "WHERE (username LIKE '%$search%' OR full_name LIKE '%$search%' OR email LIKE '%$search%')";
    }
    
    // Get total users
    $total_users_query = "SELECT COUNT(*) as total FROM users $search_condition";
    $total_result = $conn->query($total_users_query);
    $total_users = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_users / $items_per_page);
    
    // Get users for current page
    $users_query = "SELECT * FROM users $search_condition ORDER BY full_name ASC LIMIT $offset, $items_per_page";
    $users_result = $conn->query($users_query);

    // Include the header after all redirects
	include('../includes/dashboard_header.php'); 
?>

	<div class="content-wrapper">

		<!-- Page Title and Actions -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>Manage Users</h5>
            <div class="d-flex gap-2">
                <a href="add_user.php" class="btn btn-primary mr-2 py-3 px-5 rounded-2">
                    +  Add New User
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

        <!-- List of Users -->
        <div class="row">
            <div class="col-12">
              <div class="card rounded-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <div class="py-2 d-flex justify-content-cennter align-items-center gap-1">
                                    <button class="btn btn-primary" onclick="toggleUsers('admin')">View Only Admins</button>
                                    <button class="btn btn-primary" onclick="toggleUsers('users')">View Only Users</button>
                                </div>
                                <table id="book-listing" class="table">
                                <thead>
                                    <tr class="bg-primary text-white">
                                        <th>#ID</th>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Added Date</th>
                                        <th>Books Borrowed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if ($users_result->num_rows > 0) {
                                            while ($user = $users_result->fetch_assoc()) {

                                                // Number of books borrowed
                                                $borrowed_query = "SELECT COUNT(*) as count FROM book_loans WHERE user_id = '{$user['user_id']}' AND status = 'borrowed'";
                                                $borrowed_result = $conn->query($borrowed_query);
                                                $borrowed_count = $borrowed_result->fetch_assoc()['count'];
                                                
                                                echo '<tr>
                                                    <td>' . $user['user_id'] . '</td>
                                                    <td>' . $user['username'] . '</td>
                                                    <td>' . $user['full_name'] . '</td>
                                                    <td>' . $user['email'] . '</td>
                                                    <td><span class="badge position-relative badge-' . ($user['role'] == 'admin' ? 'primary' : 'info') . '">' . ucfirst($user['role']) . 
                                                        ($user['user_id'] == $_SESSION['user_id'] ? ' 
                                                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                                                                <span class="visually-hidden">New alerts</span>
                                                            </span>
                                                            ' : '') . 
                                                    '</span></td>
                                                    <td>' . date('d M Y', strtotime($user['created_at'])) . '</td>
                                                    <td>' . $borrowed_count . '</td>
                                                    <td>';
                                                    
                                                    // Don't show delete option for current user or other admins
                                                    if ($user['user_id'] != $_SESSION['user_id'] && $user['role'] != 'admin') {
                                                        echo '<a href="edit_user.php?id=' . $user['user_id'] . '" class="btn btn-sm">
                                                                <i data-feather="edit" style="width: 14px;"></i>
                                                            </a>';
                                                        echo '<a href="javascript:void(0);" onclick="confirmDelete(' . $user['user_id'] . ')" class="btn btn-sm text-danger ml-1">
                                                                <i data-feather="trash-2" style="width: 14px;"></i>
                                                            </a>';
                                                    } elseif ($user['user_id'] == $_SESSION['user_id']) {
                                                        echo '<a href="edit_user.php?id=' . $user['user_id'] . '" class="btn btn-sm">
                                                                <i data-feather="edit" style="width: 14px;"></i>
                                                            </a>';
                                                    } else {
                                                        echo '<a href="edit_user.php?id=' . $user['user_id'] . '" class="btn btn-sm">
                                                                <i data-feather="edit" style="width: 14px;"></i>
                                                            </a>';
                                                    }
                                                    
                                                echo '</td>
                                                </tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="8" class="text-center">No users found</td></tr>';
                                        }
                                    ?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>

	</div>

	<!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this User? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
    
	<script>
        function confirmDelete(userId) {
            document.getElementById('confirmDeleteBtn').href = 'view_users.php?delete=' + userId;
            $('#deleteModal').modal('show');
        }

        // Function to toggle user visibility based on role
        function toggleUsers(role) {
            // Get all user rows
            const userRows = document.querySelectorAll('#book-listing tbody tr');
            
            // Loop through each row
            userRows.forEach(row => {
                // Get the role cell (5th column)
                const roleCell = row.cells[4];
                
                // Check if the role cell contains the text we're looking for
                if (roleCell) {
                    const roleText = roleCell.textContent.toLowerCase();
                    
                    if (role === 'admin') {
                        // Show only admin rows
                        if (roleText.includes('admin')) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } else if (role === 'users') {
                        // Show only regular user rows
                        if (roleText.includes('user')) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        // Show all rows
                        row.style.display = '';
                    }
                }
            });
            
            // Update button styles to show which filter is active
            const adminBtn = document.querySelector('button[onclick="toggleUsers(\'admin\')"]');
            const usersBtn = document.querySelector('button[onclick="toggleUsers(\'users\')"]');
            
            if (role === 'admin') {
                adminBtn.classList.add('btn-success');
                adminBtn.classList.remove('btn-primary');
                usersBtn.classList.add('btn-primary');
                usersBtn.classList.remove('btn-success');
            } else if (role === 'users') {
                usersBtn.classList.add('btn-success');
                usersBtn.classList.remove('btn-primary');
                adminBtn.classList.add('btn-primary');
                adminBtn.classList.remove('btn-success');
            } else {
                adminBtn.classList.add('btn-primary');
                adminBtn.classList.remove('btn-success');
                usersBtn.classList.add('btn-primary');
                usersBtn.classList.remove('btn-success');
            }
        }
        
        // Add a button to show all users
        document.addEventListener('DOMContentLoaded', function() {
            const buttonContainer = document.querySelector('.py-2.d-flex');
            const allUsersBtn = document.createElement('button');
            allUsersBtn.className = 'btn btn-primary';
            allUsersBtn.textContent = 'Show All Users';
            allUsersBtn.onclick = function() { toggleUsers('all'); };
            buttonContainer.appendChild(allUsersBtn);
        });
    </script>

<?php include_once('../includes/dashboard_footer.php'); ?>

<style>
    .dot-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-left: 5px;
        vertical-align: middle;
    }
    
    .badge {
        display: inline-flex;
        align-items: center;
    }
</style>

</body>
</html>
