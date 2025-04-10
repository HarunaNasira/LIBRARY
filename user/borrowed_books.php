<?php
session_start();
require_once '../config/db_connect.php';
require_once '../config/constants.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to access this page";
    redirect("../index.php");
}

// Get user's borrowed books
$user_id = $_SESSION['user_id'];
$borrowed_books_query = "
    SELECT l.loan_id, b.book_id, b.title, b.author, b.isbn, b.cover_image, 
           l.borrow_date, l.due_date, l.status
    FROM book_loans l
    JOIN books b ON l.book_id = b.book_id
    WHERE l.user_id = '$user_id' AND l.status IN ('borrowed', 'pending')
    ORDER BY 
        CASE 
            WHEN l.status = 'pending' THEN 1
            WHEN l.status = 'borrowed' THEN 2
        END,
        l.due_date ASC
";
$borrowed_books_result = $conn->query($borrowed_books_query);

include('../includes/dashboard_header.php');
?>

<div class="content-wrapper">
    <!-- Page Title -->
    <div class="my-3 d-flex justify-content-between align-items-center">
        <h5>My Books</h5>
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
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Request Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($book = $borrowed_books_result->fetch_assoc()): ?>
                                        <?php
                                        $due_date = strtotime($book['due_date']);
                                        $today = strtotime(date('Y-m-d'));
                                        $is_overdue = $due_date < $today && $book['status'] == 'borrowed';
                                        $days_left = ceil(($due_date - $today) / (60 * 60 * 24));
                                        ?>
                                        <tr>
                                            <td>
                                                <img src="../assets/images/books/<?php echo $book['cover_image'] ? $book['cover_image'] : 'default-book.svg'; ?>" 
                                                     alt="Book Cover" style="width: 40px; height: 60px; object-fit: cover; border-radius: 6px;">
                                            </td>
                                            <td><?php echo $book['title']; ?></td>
                                            <td><?php echo $book['author']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($book['borrow_date'])); ?></td>
                                            <td>
                                                <?php if ($book['status'] == 'borrowed'): ?>
                                                    <?php echo date('d M Y', $due_date); ?><br>
                                                    <?php if ($is_overdue): ?>
                                                        <span class="badge badge-danger">Overdue by <?php echo abs($days_left); ?> days</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-info"><?php echo $days_left; ?> days left</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                $status_text = '';
                                                
                                                if ($book['status'] == 'pending') {
                                                    $status_class = 'badge-warning';
                                                    $status_text = 'Pending Approval';
                                                } elseif ($book['status'] == 'borrowed') {
                                                    if ($is_overdue) {
                                                        $status_class = 'badge-danger';
                                                        $status_text = 'Overdue';
                                                    } else {
                                                        $status_class = 'badge-primary';
                                                        $status_text = 'Borrowed';
                                                    }
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
                    <?php else: ?>
                        <div class="text-center">
                            <p>You don't have any borrowed or pending books.</p>
                            <a href="search_books.php" class="btn btn-primary">Browse Books</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('../includes/dashboard_footer.php'); ?> 