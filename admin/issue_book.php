<?php
session_start();
require_once '../config/db_connect.php';
require_once '../config/constants.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "You must be logged in as an admin to access this page";
    redirect("../index.php");
}

// Handle book issuing
if (isset($_POST['issue_book'])) {
    $loan_id = sanitize($_POST['loan_id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update loan status to borrowed
        $update_loan = "UPDATE book_loans SET status = 'borrowed' WHERE loan_id = '$loan_id' AND status = 'pending'";
        $conn->query($update_loan);
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success'] = "Book issued successfully.";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error issuing book: " . $e->getMessage();
    }
    
    // Redirect to remove POST data
    redirect("issue_book.php");
}

// Get pending book requests
$pending_requests_query = "
    SELECT l.loan_id, l.borrow_date, l.due_date,
           b.book_id, b.title, b.author, b.cover_image,
           u.user_id, u.full_name, u.email
    FROM book_loans l
    JOIN books b ON l.book_id = b.book_id
    JOIN users u ON l.user_id = u.user_id
    WHERE l.status = 'pending'
    ORDER BY l.borrow_date ASC
";
$pending_requests_result = $conn->query($pending_requests_query);

include('../includes/dashboard_header.php');
?>

<div class="content-wrapper">
    <!-- Page Title -->
    <div class="my-3 d-flex justify-content-between align-items-center">
        <h5>Issue Books</h5>
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

    <!-- Pending Requests Table -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-3">
                <div class="card-body">
                    <?php if ($pending_requests_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Cover</th>
                                        <th>Book Details</th>
                                        <th>User Details</th>
                                        <th>Request Date</th>
                                        <th>Due Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($request = $pending_requests_result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <img src="../assets/images/books/<?php echo $request['cover_image'] ? $request['cover_image'] : 'default-book.svg'; ?>" 
                                                     alt="Book Cover" style="width: 40px; height: 60px; object-fit: cover; border-radius: 6px;">
                                            </td>
                                            <td>
                                                <strong><?php echo $request['title']; ?></strong><br>
                                                <small class="text-muted">By <?php echo $request['author']; ?></small>
                                            </td>
                                            <td>
                                                <strong><?php echo $request['full_name']; ?></strong><br>
                                                <small class="text-muted"><?php echo $request['email']; ?></small>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($request['borrow_date'])); ?></td>
                                            <td><?php echo date('d M Y', strtotime($request['due_date'])); ?></td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="loan_id" value="<?php echo $request['loan_id']; ?>">
                                                    <button type="submit" name="issue_book" class="btn btn-success btn-sm">Issue Book</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <p>No pending book requests.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('../includes/dashboard_footer.php'); ?> 