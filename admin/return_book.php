<?php
session_start();
require_once '../config/db_connect.php';
require_once '../config/constants.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "You must be logged in as an admin to access this page";
    redirect("../index.php");
}

// Handle book return
if (isset($_POST['return_book'])) {
    $loan_id = sanitize($_POST['loan_id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get book_id from loan
        $get_book_query = "SELECT book_id FROM book_loans WHERE loan_id = '$loan_id' AND status = 'borrowed'";
        $book_result = $conn->query($get_book_query);
        $book = $book_result->fetch_assoc();
        
        if ($book) {
            // Update loan status to returned
            $return_date = date('Y-m-d');
            $update_loan = "UPDATE book_loans SET status = 'returned', return_date = '$return_date' 
                          WHERE loan_id = '$loan_id' AND status = 'borrowed'";
            $conn->query($update_loan);
            
            // Update book quantity
            $update_quantity = "UPDATE books SET available_quantity = available_quantity + 1 
                              WHERE book_id = '{$book['book_id']}'";
            $conn->query($update_quantity);
            
            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = "Book returned successfully.";
        } else {
            throw new Exception("Invalid loan or book already returned");
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error returning book: " . $e->getMessage();
    }
    
    // Redirect to remove POST data
    redirect("return_book.php");
}

// Get borrowed books
$borrowed_books_query = "
    SELECT l.loan_id, l.borrow_date, l.due_date,
           b.book_id, b.title, b.author, b.cover_image,
           u.user_id, u.full_name, u.email
    FROM book_loans l
    JOIN books b ON l.book_id = b.book_id
    JOIN users u ON l.user_id = u.user_id
    WHERE l.status = 'borrowed'
    ORDER BY l.due_date ASC
";
$borrowed_books_result = $conn->query($borrowed_books_query);

include('../includes/dashboard_header.php');
?>

<div class="content-wrapper">
    <!-- Page Title -->
    <div class="my-3 d-flex justify-content-between align-items-center">
        <h5>Return Books</h5>
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

    <!-- Borrowed Books Table -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-3">
                <div class="card-body">
                    <?php if ($borrowed_books_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Cover</th>
                                        <th>Book Details</th>
                                        <th>User Details</th>
                                        <th>Borrowed Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($book = $borrowed_books_result->fetch_assoc()): ?>
                                        <?php
                                        $due_date = strtotime($book['due_date']);
                                        $today = strtotime(date('Y-m-d'));
                                        $is_overdue = $due_date < $today;
                                        $days_left = ceil(($due_date - $today) / (60 * 60 * 24));
                                        ?>
                                        <tr>
                                            <td>
                                                <img src="../assets/images/books/<?php echo $book['cover_image'] ? $book['cover_image'] : 'default-book.svg'; ?>" 
                                                     alt="Book Cover" style="width: 40px; height: 60px; object-fit: cover; border-radius: 6px;">
                                            </td>
                                            <td>
                                                <strong><?php echo $book['title']; ?></strong><br>
                                                <small class="text-muted">By <?php echo $book['author']; ?></small>
                                            </td>
                                            <td>
                                                <strong><?php echo $book['full_name']; ?></strong><br>
                                                <small class="text-muted"><?php echo $book['email']; ?></small>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($book['borrow_date'])); ?></td>
                                            <td>
                                                <?php echo date('d M Y', $due_date); ?><br>
                                                <?php if ($is_overdue): ?>
                                                    <span class="badge badge-danger">Overdue by <?php echo abs($days_left); ?> days</span>
                                                <?php else: ?>
                                                    <span class="badge badge-info"><?php echo $days_left; ?> days left</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $is_overdue ? 'danger' : 'primary'; ?>">
                                                    <?php echo $is_overdue ? 'Overdue' : 'Borrowed'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="loan_id" value="<?php echo $book['loan_id']; ?>">
                                                    <button type="submit" name="return_book" class="btn btn-success btn-sm">Return Book</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <p>No books currently borrowed.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('../includes/dashboard_footer.php'); ?> 