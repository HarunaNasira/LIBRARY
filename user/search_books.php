<?php
session_start();
require_once '../config/db_connect.php';
require_once '../config/constants.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to access this page";
    redirect("../index.php");
}

// Get search parameters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$genre = isset($_GET['genre']) ? sanitize($_GET['genre']) : '';
$subject = isset($_GET['subject']) ? sanitize($_GET['subject']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'title_asc';

// Build the base query
$query = "SELECT b.*, 
          (SELECT COUNT(*) FROM book_loans l WHERE l.book_id = b.book_id AND l.user_id = '" . $_SESSION['user_id'] . "' AND l.status IN ('borrowed', 'pending')) as user_borrowed_count
          FROM books b";

// Add search conditions
$where_conditions = array();
$params = array();

if (!empty($search)) {
    $where_conditions[] = "(b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($genre)) {
    $where_conditions[] = "b.genre = ?";
    $params[] = $genre;
}

if (!empty($subject)) {
    $where_conditions[] = "b.subject = ?";
    $params[] = $subject;
}

// Add where clause if there are conditions
if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}

// Add sorting
switch ($sort) {
    case 'title_asc':
        $query .= " ORDER BY b.title ASC";
        break;
    case 'title_desc':
        $query .= " ORDER BY b.title DESC";
        break;
    case 'author_asc':
        $query .= " ORDER BY b.author ASC";
        break;
    case 'author_desc':
        $query .= " ORDER BY b.author DESC";
        break;
    case 'newest':
        $query .= " ORDER BY b.publication_year DESC";
        break;
    case 'oldest':
        $query .= " ORDER BY b.publication_year ASC";
        break;
    default:
        $query .= " ORDER BY b.title ASC";
}

// Handle book request
if (isset($_POST['request_book'])) {
    $book_id = sanitize($_POST['book_id']);
    $user_id = $_SESSION['user_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check if user has reached the maximum number of books
        $borrowed_count_query = "SELECT COUNT(*) as count FROM book_loans WHERE user_id = '$user_id' AND status IN ('borrowed', 'pending')";
        $borrowed_count_result = $conn->query($borrowed_count_query);
        $borrowed_count = $borrowed_count_result->fetch_assoc()['count'];
        
        if ($borrowed_count >= MAX_BOOKS_PER_USER) {
            throw new Exception("You have reached the maximum number of books you can borrow (" . MAX_BOOKS_PER_USER . ")");
        }
        
        // Check if book is already borrowed or requested by user
        $already_borrowed_query = "SELECT COUNT(*) as count FROM book_loans WHERE user_id = '$user_id' AND book_id = '$book_id' AND status IN ('borrowed', 'pending')";
        $already_borrowed_result = $conn->query($already_borrowed_query);
        $already_borrowed = $already_borrowed_result->fetch_assoc()['count'];
        
        if ($already_borrowed > 0) {
            throw new Exception("You have already requested or borrowed this book");
        }
        
        // Check if book is available
        $book_query = "SELECT available_quantity FROM books WHERE book_id = '$book_id'";
        $book_result = $conn->query($book_query);
        $book = $book_result->fetch_assoc();
        
        if ($book['available_quantity'] <= 0) {
            throw new Exception("This book is not available for borrowing");
        }
        
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
        $_SESSION['error'] = $e->getMessage();
    }
    
    // Redirect to remove POST data
    redirect("search_books.php?" . http_build_query($_GET));
}

// Execute the query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

include('../includes/dashboard_header.php');
?>

<div class="content-wrapper">
    <!-- Page Title -->
    <div class="my-3 d-flex justify-content-between align-items-center">
        <h5>Search Books</h5>
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

    <!-- Search Form -->
    <div class="card rounded-3 mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search by title, author, or ISBN" value="<?php echo $search; ?>">
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="genre">
                        <option value="">All Genres</option>
                        <?php
                        $genres_query = "SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL ORDER BY genre";
                        $genres_result = $conn->query($genres_query);
                        while ($g = $genres_result->fetch_assoc()) {
                            $selected = ($genre == $g['genre']) ? 'selected' : '';
                            echo "<option value='{$g['genre']}' $selected>{$g['genre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="subject">
                        <option value="">All Subjects</option>
                        <?php
                        $subjects_query = "SELECT DISTINCT subject FROM books WHERE subject IS NOT NULL ORDER BY subject";
                        $subjects_result = $conn->query($subjects_query);
                        while ($s = $subjects_result->fetch_assoc()) {
                            $selected = ($subject == $s['subject']) ? 'selected' : '';
                            echo "<option value='{$s['subject']}' $selected>{$s['subject']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="sort">
                        <option value="title_asc" <?php echo $sort == 'title_asc' ? 'selected' : ''; ?>>Title (A-Z)</option>
                        <option value="title_desc" <?php echo $sort == 'title_desc' ? 'selected' : ''; ?>>Title (Z-A)</option>
                        <option value="author_asc" <?php echo $sort == 'author_asc' ? 'selected' : ''; ?>>Author (A-Z)</option>
                        <option value="author_desc" <?php echo $sort == 'author_desc' ? 'selected' : ''; ?>>Author (Z-A)</option>
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($book = $result->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="../assets/images/books/<?php echo $book['cover_image'] ? $book['cover_image'] : 'default-book.svg'; ?>" 
                             class="card-img-top" alt="<?php echo $book['title']; ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $book['title']; ?></h5>
                            <p class="card-text">
                                <small class="text-muted">By <?php echo $book['author']; ?></small><br>
                                <span class="badge badge-primary"><?php echo $book['genre']; ?></span>
                                <?php if ($book['subject']): ?>
                                    <span class="badge badge-info"><?php echo $book['subject']; ?></span>
                                <?php endif; ?>
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
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No books found matching your search criteria.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once('../includes/dashboard_footer.php'); ?>