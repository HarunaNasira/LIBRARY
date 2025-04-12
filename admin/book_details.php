<?php 
	session_start();
	require_once '../config/db_connect.php';
	require_once '../config/constants.php';
	
	// Check if user is logged in
	if (!isset($_SESSION['user_id'])) {
		$_SESSION['error'] = "You must be logged in to access this page";
		redirect("../index.php");
	}
	
	// Get user role
	$user_role = $_SESSION['role'];
	$is_admin = ($user_role === 'admin');
	
	// Get book ID from URL
	$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	
	// Fetch book details
	$book_query = "SELECT * FROM books WHERE book_id = $book_id";
	$book_result = $conn->query($book_query);
	
	if ($book_result->num_rows === 0) {
		$_SESSION['error'] = "Book not found";
		redirect($is_admin ? "booklist.php" : "search_books.php");
	}
	
	$book = $book_result->fetch_assoc();

	// Check if user can borrow this book
	$can_borrow = false;
	$borrow_message = '';

	if (!$is_admin) {
		// Check if user has reached book limit
		$user_id = $_SESSION['user_id'];
		$borrowed_count_query = "SELECT COUNT(*) as count FROM book_loans WHERE user_id = '$user_id' AND status IN ('borrowed', 'pending')";
		$borrowed_count_result = $conn->query($borrowed_count_query);
		$borrowed_count = $borrowed_count_result->fetch_assoc()['count'];

		// Check if book is already borrowed or requested by user
		$already_borrowed_query = "SELECT COUNT(*) as count FROM book_loans WHERE user_id = '$user_id' AND book_id = $book_id AND status IN ('borrowed', 'pending')";
		$already_borrowed_result = $conn->query($already_borrowed_query);
		$already_borrowed = $already_borrowed_result->fetch_assoc()['count'];

		if ($already_borrowed > 0) {
			$borrow_message = 'You have already requested or borrowed this book';
		} elseif ($borrowed_count >= MAX_BOOKS_PER_USER) {
			$borrow_message = 'You have reached the maximum number of books you can borrow (' . MAX_BOOKS_PER_USER . ')';
		} elseif ($book['available_quantity'] <= 0) {
			$borrow_message = 'This book is not available for borrowing';
		} else {
			$can_borrow = true;
		}
	}

	// Handle borrow request
	if (isset($_POST['borrow']) && $can_borrow) {
		$user_id = $_SESSION['user_id'];
		$borrow_date = date('Y-m-d');
		$due_date = date('Y-m-d', strtotime('+' . LOAN_DURATION_DAYS . ' days'));

		// Start transaction
		$conn->begin_transaction();

		try {
			// Insert loan record with pending status
			$insert_loan = "INSERT INTO book_loans (user_id, book_id, borrow_date, due_date, status) 
						   VALUES ('$user_id', $book_id, '$borrow_date', '$due_date', 'pending')";
			$conn->query($insert_loan);

			// Update book quantity
			$update_quantity = "UPDATE books SET available_quantity = available_quantity - 1 WHERE book_id = $book_id";
			$conn->query($update_quantity);

			// Commit transaction
			$conn->commit();

			$_SESSION['success'] = "Book request submitted successfully. Please wait for admin approval.";
			redirect("book_details.php?id=$book_id");
		} catch (Exception $e) {
			// Rollback transaction on error
			$conn->rollback();
			$_SESSION['error'] = "Error requesting book: " . $e->getMessage();
		}
	}

    include('../includes/dashboard_header.php'); 
?>

	<div class="content-wrapper">

        <!-- Page Title and Actions -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>
                <a class="text-dark" href="dashboard.php">Manage Books</a> / Book Details
            </h5>
            <div class="d-inline-flex gap-2">
                <?php if ($is_admin): ?>
                    <a href="../admin/booklist.php" class="btn btn-primary px-3 py-2 rounded-2">
                        View All Book
                    </a>
                <?php else: ?>
                    <?php if ($can_borrow): ?>
                        <form method="POST" action="book_details.php?id=<?php echo $book_id; ?>">
                            <input type="hidden" name="borrow" value="1">
                            <button type="submit" class="btn btn-success px-3 py-2 rounded-2">
                                Request Book
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-secondary px-3 py-2 rounded-2" disabled>
                            <?php echo $borrow_message; ?>
                        </button>
                    <?php endif; ?>
                    <a href="../user/search_books.php" class="btn btn-primary px-3 py-2 rounded-2">
                        Back to Search
                    </a>
                <?php endif; ?>
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

        <!-- Book Info -->
        <section class="p-4 my-4">
            <div class="d-flex gap-4">
                <div class="col-lg-2">
                    <img src="../assets/images/books/<?php echo $book['cover_image'] ? $book['cover_image'] : 'default-book.svg'; ?>" 
                         class="img-fluid rounded" alt="<?php echo $book['title']; ?>">
                </div>
                <div class="col-lg-8 py-1">
                    <h2 class="card-title mb-4"><?php echo $book['title']; ?></h2>
                    <p class="text-muted mb-4">By <?php echo $book['author']; ?></p>
                    <div class="mb-4">
                        <h5>Summary</h5>
                        <p><?php echo $book['description']; ?></p>
                    </div>
                </div>
            </div>

            <div class="row mt-4 p-4">
                <div class="col-12">
                    <h4 class="card-title mb-4">Book Details</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="text-muted">ISBN</label>
                                <p class="mb-0"><?php echo $book['isbn']; ?></p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Genre</label>
                                <p class="mb-0"><?php echo $book['genre']; ?></p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Subject</label>
                                <p class="mb-0"><?php echo $book['subject']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="text-muted">Total Copies</label>
                                <p class="mb-0"><?php echo $book['quantity']; ?></p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Available Copies</label>
                                <p class="mb-0"><?php echo $book['available_quantity']; ?></p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Published Year</label>
                                <p class="mb-0"><?php echo $book['publication_year']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="text-muted">Shelf Location</label>
                                <p class="mb-0"><?php echo $book['shelf_code']; ?></p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Status</label>
                                <p class="mb-0">
                                    <?php if ($book['available_quantity'] > 0): ?>
                                        <span class="badge badge-success">Available</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Not Available</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Added Date</label>
                                <p class="mb-0"><?php echo date('F j, Y', strtotime($book['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
        
<?php include_once('../includes/dashboard_footer.php'); ?>
