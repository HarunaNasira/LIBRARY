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

// Get statistics
$total_borrowed = $borrowed_books_result->num_rows;
$overdue_books_query = "
    SELECT COUNT(*) as count
    FROM book_loans
    WHERE user_id = '$user_id' AND status = 'borrowed' AND due_date < CURDATE()
";
$overdue_books_result = $conn->query($overdue_books_query);
$overdue_books = $overdue_books_result->fetch_assoc()['count'];

// Get overdue book details
$overdue_details_query = "
    SELECT l.loan_id, b.book_id, b.title, b.author, b.cover_image, l.borrow_date, l.due_date,
           DATEDIFF(CURDATE(), l.due_date) as days_overdue
    FROM book_loans l
    JOIN books b ON l.book_id = b.book_id
    WHERE l.user_id = '$user_id' AND l.status = 'borrowed' AND l.due_date < CURDATE()
    ORDER BY l.due_date ASC
";
$overdue_details_result = $conn->query($overdue_details_query);

// Get user's loan history
$loan_history_query = "
    SELECT l.loan_id, b.book_id, b.title, b.author, l.borrow_date, l.due_date, l.return_date, l.status
    FROM book_loans l
    JOIN books b ON l.book_id = b.book_id
    WHERE l.user_id = '$user_id' AND l.status = 'returned'
    ORDER BY l.return_date DESC
    LIMIT 5
";
$loan_history_result = $conn->query($loan_history_query);

// Get recommended books based on user's borrowing history (if any)
$recommended_books = array();
if ($total_borrowed > 0) {
    $get_genres_query = "
        SELECT DISTINCT b.genre
        FROM book_loans l
        JOIN books b ON l.book_id = b.book_id
        WHERE l.user_id = '$user_id'
        LIMIT 3
    ";
    $genres_result = $conn->query($get_genres_query);
    
    if ($genres_result->num_rows > 0) {
        $genres = array();
        while ($genre_row = $genres_result->fetch_assoc()) {
            $genres[] = "'" . $genre_row['genre'] . "'";
        }
        
        $genres_str = implode(',', $genres);
        $recommended_query = "
            SELECT b.*, 
                   (SELECT COUNT(*) FROM book_loans l WHERE l.book_id = b.book_id AND l.user_id = '$user_id' AND l.status IN ('borrowed', 'pending')) as user_borrowed_count
            FROM books b
            WHERE b.genre IN ($genres_str)
            AND b.book_id NOT IN (
                SELECT book_id
                FROM book_loans
                WHERE user_id = '$user_id'
            )
            AND b.available_quantity > 0
            ORDER BY RAND()
            LIMIT 4
        ";
        $recommended_result = $conn->query($recommended_query);
        
        if ($recommended_result->num_rows > 0) {
            while ($book = $recommended_result->fetch_assoc()) {
                $recommended_books[] = $book;
            }
        }
    }
}

// If we don't have recommended books, get some random available books
if (empty($recommended_books)) {
    $random_books_query = "
        SELECT b.*, 
               (SELECT COUNT(*) FROM book_loans l WHERE l.book_id = b.book_id AND l.user_id = '$user_id' AND l.status IN ('borrowed', 'pending')) as user_borrowed_count
        FROM books b
        WHERE b.available_quantity > 0
        ORDER BY RAND()
        LIMIT 4
    ";
    $random_books_result = $conn->query($random_books_query);
    
    if ($random_books_result->num_rows > 0) {
        while ($book = $random_books_result->fetch_assoc()) {
            $recommended_books[] = $book;
        }
    }
}

// Handle book request
if (isset($_POST['request_book'])) {
    $book_id = sanitize($_POST['book_id']);
    
    // Check if user has reached the maximum number of books
    $borrowed_count_query = "SELECT COUNT(*) as count FROM book_loans WHERE user_id = '$user_id' AND status IN ('borrowed', 'pending')";
    $borrowed_count_result = $conn->query($borrowed_count_query);
    $borrowed_count = $borrowed_count_result->fetch_assoc()['count'];
    
    if ($borrowed_count >= MAX_BOOKS_PER_USER) {
        $_SESSION['error'] = "You have reached the maximum number of books you can borrow (" . MAX_BOOKS_PER_USER . ")";
    } else {
        // Check if book is already borrowed or requested by user
        $already_borrowed_query = "SELECT COUNT(*) as count FROM book_loans WHERE user_id = '$user_id' AND book_id = '$book_id' AND status IN ('borrowed', 'pending')";
        $already_borrowed_result = $conn->query($already_borrowed_query);
        $already_borrowed = $already_borrowed_result->fetch_assoc()['count'];
        
        if ($already_borrowed > 0) {
            $_SESSION['error'] = "You have already requested or borrowed this book";
        } else {
            // Check if book is available
            $book_query = "SELECT available_quantity FROM books WHERE book_id = '$book_id'";
            $book_result = $conn->query($book_query);
            $book = $book_result->fetch_assoc();
            
            if ($book['available_quantity'] <= 0) {
                $_SESSION['error'] = "This book is not available for borrowing";
            } else {
                // Start transaction
                $conn->begin_transaction();
                
                try {
                    // Insert loan record with pending status
                    $borrow_date = date('Y-m-d');
                    $due_date = date('Y-m-d', strtotime('+' . LOAN_DURATION_DAYS . ' days'));
                    
                    $insert_loan = "INSERT INTO book_loans (user_id, book_id, borrow_date, due_date, status) 
                                  VALUES ('$user_id', '$book_id', '$borrow_date', '$due_date', 'pending')";
                    $conn->query($insert_loan);
                    
                    // Update book quantity
                    $update_quantity = "UPDATE books SET available_quantity = available_quantity - 1 WHERE book_id = '$book_id'";
                    $conn->query($update_quantity);
                    
                    // Commit transaction
                    $conn->commit();
                    
                    $_SESSION['success'] = "Book request submitted successfully. Please wait for admin approval.";
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    $_SESSION['error'] = "Error requesting book: " . $e->getMessage();
                }
            }
        }
    }
    
    // Redirect to remove POST data
    redirect("dashboard.php");
}

include('../includes/dashboard_header.php');
?>
            
    <!-- Page content -->
    <div class="content-wrapper">
        <!-- Welcome -->
        <section class="my-4 d-flex justify-content-between">
            <h2 class="font-weight-bold">Hello, 
                <span class="welcome-text"><?php echo $_SESSION['full_name']; ?> |</span>
            </h2>
            <h6><?php echo date('l | F jS, Y'); ?></h6>
        </section>
        
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
        
        <!-- Statistics -->
        <section class="row">
            <div class="col-12">
                <div class="row">
                    <!-- Total Borrowed -->
                    <div class="col-12 col-sm-6 col-md-6 grid-margin stretch-card rounded-md">
                        <div class="card rounded-3 border <?php echo $total_borrowed > 0 ? 'bg-primary text-white' : ''; ?>">
                            <div class="card-body">
                                <h6 class="card-title <?php echo $total_borrowed > 0 ? 'text-white' : ''; ?>">Currently Borrowed</h6>
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-text"><?php echo $total_borrowed; ?> Book(s)</h3>
                                    <i data-feather="book-open"></i>
                                </div>
                                <small>Maximum <?php echo MAX_BOOKS_PER_USER; ?> books allowed</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overdue Books -->
                    <div class="col-12 col-sm-6 col-md-6 grid-margin stretch-card rounded-md">
                        <div class="card rounded-3 border <?php echo $overdue_books > 0 ? 'bg-danger text-white' : ''; ?>">
                            <div class="card-body">
                                <h6 class="card-title <?php echo $overdue_books > 0 ? 'text-white' : ''; ?>">Overdue Books</h6>
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-text"><?php echo $overdue_books; ?> Books</h3>
                                    <i data-feather="alert-octagon"></i>
                                </div>
                                <?php if ($overdue_books > 0): ?>
                                    <small>Fine: $<?php echo OVERDUE_FINE_PER_DAY; ?> per day</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Overdue Books Details -->
        <?php if ($overdue_books > 0): ?>
        <section class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Overdue Books</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Cover</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Due Date</th>
                                        <th>Days Overdue</th>
                                        <th>Fine</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($book = $overdue_details_result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <img src="../assets/images/books/<?php echo $book['cover_image'] ? $book['cover_image'] : 'default-book.svg'; ?>" 
                                                        alt="Book Cover" style="width: 40px; height: 60px; object-fit: cover; border-radius: 6px;">
                                            </td>
                                            <td><?php echo $book['title']; ?></td>
                                            <td><?php echo $book['author']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($book['due_date'])); ?></td>
                                            <td><?php echo $book['days_overdue']; ?> days</td>
                                            <td>$<?php echo $book['days_overdue'] * OVERDUE_FINE_PER_DAY; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Quick Actions -->
        <section class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Quick Actions</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <a href="search_books.php" class="btn btn-primary btn-block">
                                    <i data-feather="zoom-in" style="width: 16px;"></i> Search Books
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="borrowed_books.php" class="btn btn-info btn-block">
                                    <i data-feather="book" style="width: 16px;"></i> View Borrowed Books
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="../pages/profile.php" class="btn btn-success btn-block">
                                    <i data-feather="user" style="width: 16px;"></i> My Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Borrowed Books -->
        <section class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">My Books</h4>
                            <a href="borrowed_books.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        
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
        </section>
        
        <!-- Recommended Books -->
        <section class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card-body">
                    <h4 class="card-title">Recommended for You</h4>
                    <div class="row">
                        <?php foreach ($recommended_books as $book): ?>
                            <div class="col-md-3 mb-4">
                                <div class="card h-100">
                                    <img class="card-img-top" src="../assets/images/books/<?php echo $book['cover_image'] ? $book['cover_image'] : 'default-book.svg'; ?>" 
                                            alt="<?php echo $book['title']; ?>" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $book['title']; ?></h5>
                                        <p class="card-text">
                                            <small class="text-muted">By <?php echo $book['author']; ?></small><br>
                                            <span class="badge badge-primary"><?php echo $book['genre']; ?></span>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Available: <?php echo $book['available_quantity']; ?> of <?php echo $book['quantity']; ?>
                                            </small>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="book_details.php?id=<?php echo $book['book_id']; ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                                            <?php if ($book['available_quantity'] > 0 && $book['user_borrowed_count'] == 0): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                                    <button type="submit" name="request_book" class="btn btn-success btn-sm">Request Book</button>
                                                </form>
                                            <?php elseif ($book['user_borrowed_count'] > 0): ?>
                                                <button class="btn btn-secondary btn-sm" disabled>Already Requested</button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled>Unavailable</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Recent Loan History -->
        <?php if ($loan_history_result->num_rows > 0): ?>
        <section class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Recent Loan History</h4>
                            <a href="loan_history.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Borrowed Date</th>
                                        <th>Return Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($loan = $loan_history_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $loan['title']; ?></td>
                                            <td><?php echo $loan['author']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($loan['borrow_date'])); ?></td>
                                            <td><?php echo date('d M Y', strtotime($loan['return_date'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </div>
                
<?php include_once('../includes/dashboard_footer.php'); ?>