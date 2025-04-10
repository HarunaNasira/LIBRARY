<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to access this page";
    redirect("../index.php");
}

// Get user's loan history with pagination
$user_id = $_SESSION['user_id'];
$items_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get total loans
$total_loans_query = "SELECT COUNT(*) as total FROM book_loans WHERE user_id = '$user_id'";
$total_result = $conn->query($total_loans_query);
$total_loans = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_loans / $items_per_page);

// Get loan history
$loan_history_query = "
    SELECT l.loan_id, b.book_id, b.title, b.author, b.isbn, b.cover_image, 
           l.borrow_date, l.due_date, l.return_date, l.status
    FROM book_loans l
    JOIN books b ON l.book_id = b.book_id
    WHERE l.user_id = '$user_id'
    ORDER BY l.borrow_date DESC
    LIMIT $offset, $items_per_page
";
$loan_history_result = $conn->query($loan_history_query);

include('../includes/dashboard_header.php');
?>

<div class="content-wrapper">
    <!-- Page Title -->
    <div class="my-3 d-flex justify-content-between align-items-center">
        <h5>My Loan History</h5>
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

    <!-- Loan History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-3">
                <div class="card-body">
                    <?php if ($loan_history_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Cover</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Borrowed Date</th>
                                        <th>Due Date</th>
                                        <th>Return Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($loan = $loan_history_result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <img src="../assets/images/books/<?php echo $loan['cover_image'] ? $loan['cover_image'] : 'default-book.svg'; ?>" 
                                                        alt="Book Cover" style="width: 40px; height: 60px; object-fit: cover; border-radius: 6px;">
                                            </td>
                                            <td><?php echo $loan['title']; ?></td>
                                            <td><?php echo $loan['author']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($loan['borrow_date'])); ?></td>
                                            <td><?php echo date('d M Y', strtotime($loan['due_date'])); ?></td>
                                            <td>
                                                <?php 
                                                if ($loan['return_date']) {
                                                    echo date('d M Y', strtotime($loan['return_date']));
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                $status_text = '';
                                                
                                                if ($loan['status'] == 'borrowed') {
                                                    $due_date = strtotime($loan['due_date']);
                                                    $today = strtotime(date('Y-m-d'));
                                                    
                                                    if ($due_date < $today) {
                                                        $status_class = 'badge-danger';
                                                        $status_text = 'Overdue';
                                                    } else {
                                                        $status_class = 'badge-primary';
                                                        $status_text = 'Borrowed';
                                                    }
                                                } else {
                                                    $status_class = 'badge-success';
                                                    $status_text = 'Returned';
                                                }
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>">
                                                    <?php echo $status_text; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center">
                            <p>You don't have any loan history.</p>
                            <a href="search_books.php" class="btn btn-primary">Browse Books</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('../includes/dashboard_footer.php'); ?> 