<?php
session_start();
require_once '../config/db_connect.php';
require_once '../config/constants.php';

// Check if user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    $_SESSION['error'] = "You must be logged in as a user to access this page";
    redirect("../index.php");
}

// Get all available genres for filter
$genres_query = "SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL AND genre != '' ORDER BY genre ASC";
$genres_result = $conn->query($genres_query);
$genres = array();
while ($genre_row = $genres_result->fetch_assoc()) {
    $genres[] = $genre_row['genre'];
}

// Get all available subjects for filter
$subjects_query = "SELECT DISTINCT subject FROM books WHERE subject IS NOT NULL AND subject != '' ORDER BY subject ASC";
$subjects_result = $conn->query($subjects_query);
$subjects = array();
while ($subject_row = $subjects_result->fetch_assoc()) {
    $subjects[] = $subject_row['subject'];
}

// Set default values for filters and sorting from GET parameters
$search  = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$genre   = isset($_GET['genre']) ? sanitize($_GET['genre']) : '';
$subject = isset($_GET['subject']) ? sanitize($_GET['subject']) : '';
$sort    = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'title_asc';

// Pagination variables
$items_per_page = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Build the WHERE clause
// Use "1=1" so that we can safely append additional conditions
$where_conditions = array("1=1");
$params = array();
$types = "";

if (!empty($search)) {
    $where_conditions[] = "(title LIKE ? OR author LIKE ? OR isbn LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, array($search_param, $search_param, $search_param, $search_param));
    $types .= "ssss";
}

if (!empty($genre)) {
    $where_conditions[] = "genre = ?";
    $params[] = $genre;
    $types .= "s";
}

if (!empty($subject)) {
    $where_conditions[] = "subject = ?";
    $params[] = $subject;
    $types .= "s";
}

$where_clause = " WHERE " . implode(" AND ", $where_conditions);

// Sorting: Determine ORDER BY clause based on the $sort parameter
switch ($sort) {
    case 'title_asc':
        $order_by = "ORDER BY title ASC";
        break;
    case 'title_desc':
        $order_by = "ORDER BY title DESC";
        break;
    case 'author_asc':
        $order_by = "ORDER BY author ASC";
        break;
    case 'author_desc':
        $order_by = "ORDER BY author DESC";
        break;
    case 'newest':
        $order_by = "ORDER BY publication_year DESC";
        break;
    case 'oldest':
        $order_by = "ORDER BY publication_year ASC";
        break;
    default:
        $order_by = "ORDER BY title ASC";
        break;
}


$user_id = $_SESSION['user_id'];
$base_query = "SELECT b.*, 
    (SELECT COUNT(*) 
     FROM book_loans l 
     WHERE l.book_id = b.book_id 
       AND l.user_id = '$user_id' 
       AND l.status IN ('borrowed', 'pending')
    ) AS user_borrowed_count
FROM books b";

$books_query = "$base_query $where_clause $order_by LIMIT $offset, $items_per_page";

// Prepare and execute the statement for books retrieval
$stmt = $conn->prepare($books_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$books_result = $stmt->get_result();

// Count total books for pagination
$count_query = "SELECT COUNT(*) as total FROM books b $where_clause";
$stmt_count = $conn->prepare($count_query);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_result = $stmt_count->get_result();
$total_books = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $items_per_page);

// Handle borrow action
if (isset($_POST['borrow']) && isset($_POST['book_id'])) {
    $book_id = sanitize($_POST['book_id']);
    $user_id = $_SESSION['user_id'];
    
    // Check if user has already borrowed this book
    $check_borrowed_query = "SELECT * FROM book_loans WHERE user_id = '$user_id' AND book_id = '$book_id' AND status = 'borrowed'";
    $check_result = $conn->query($check_borrowed_query);
    
    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "You have already borrowed this book";
    } else {
        // Check if user has reached the maximum number of books allowed
        $check_limit_query = "SELECT COUNT(*) as count FROM book_loans WHERE user_id = '$user_id' AND status IN ('borrowed', 'pending')";
        $limit_result = $conn->query($check_limit_query);
        $borrowed_count = $limit_result->fetch_assoc()['count'];
        
        if ($borrowed_count >= MAX_BOOKS_PER_USER) {
            $_SESSION['error'] = "You have reached the maximum number of books you can borrow (". MAX_BOOKS_PER_USER .")";
        } else {
            // Get current available quantity for the book
            $check_available_query = "SELECT available_quantity FROM books WHERE book_id = '$book_id'";
            $available_result = $conn->query($check_available_query);
            $available = $available_result->fetch_assoc()['available_quantity'];
            
            if ($available <= 0) {
                $_SESSION['error'] = "This book is not available for borrowing";
            } else {
                // Calculate due date
                $due_date = date('Y-m-d', strtotime('+' . LOAN_DURATION_DAYS . ' days'));
                
                // Start transaction
                $conn->begin_transaction();
                
                try {
                    // Insert new loan record with status 'borrowed'
                    $insert_loan_query = "INSERT INTO book_loans (book_id, user_id, due_date, status) VALUES ('$book_id', '$user_id', '$due_date', 'borrowed')";
                    $conn->query($insert_loan_query);
                    
                    // Update available quantity in books table
                    $update_book_query = "UPDATE books SET available_quantity = available_quantity - 1 WHERE book_id = '$book_id'";
                    $conn->query($update_book_query);
                    
                    // Commit the transaction
                    $conn->commit();
                    
                    $_SESSION['success'] = "Book borrowed successfully! Due date: " . date('F j, Y', strtotime($due_date));
                } catch (Exception $e) {
                    $conn->rollback();
                    $_SESSION['error'] = "Error borrowing book: " . $e->getMessage();
                }
            }
        }
    }
    
    // Redirect to remove POST data to avoid double submission
    $redirect_url = "search_books.php?" . http_build_query($_GET);
    redirect($redirect_url);
}

include('../includes/dashboard_header.php');
?>

<!-- Page content -->
<div class="content-wrapper">
    <!-- Page header -->
    <div class="row">
        <div class="col-md-12 grid-margin">
            <h4 class="font-weight-bold mb-0">Search Books</h4>
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
    
    <!-- Search and Filters Form -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="search">Search</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Title, author, ISBN or description" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="genre">Genre</label>
                                <select name="genre" id="genre" class="form-control">
                                    <option value="">All Genres</option>
                                    <?php foreach ($genres as $g): ?>
                                        <option value="<?php echo htmlspecialchars($g); ?>" <?php echo ($genre == $g) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($g); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="subject">Subject</label>
                                <select name="subject" id="subject" class="form-control">
                                    <option value="">All Subjects</option>
                                    <?php foreach ($subjects as $s): ?>
                                        <option value="<?php echo htmlspecialchars($s); ?>" <?php echo ($subject == $s) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($s); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="sort">Sort By</label>
                                <select name="sort" id="sort" class="form-control">
                                    <option value="title_asc" <?php echo ($sort == 'title_asc') ? 'selected' : ''; ?>>Title (A-Z)</option>
                                    <option value="title_desc" <?php echo ($sort == 'title_desc') ? 'selected' : ''; ?>>Title (Z-A)</option>
                                    <option value="author_asc" <?php echo ($sort == 'author_asc') ? 'selected' : ''; ?>>Author (A-Z)</option>
                                    <option value="author_desc" <?php echo ($sort == 'author_desc') ? 'selected' : ''; ?>>Author (Z-A)</option>
                                    <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                                    <option value="oldest" <?php echo ($sort == 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Books Grid -->
    <div class="row">
        <?php if ($books_result->num_rows > 0): ?>
            <div class="row my-3">
                <h5>Books:</h5>
            </div>
            <?php while ($book = $books_result->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="../assets/images/books/<?php echo htmlspecialchars($book['cover_image'] ? $book['cover_image'] : 'default-book.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($book['title']); ?>" class="card-img-top" style="height:250px; object-fit:cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="card-text">
                                <small class="text-muted">By <?php echo htmlspecialchars($book['author']); ?></small><br>
                                <?php if ($book['genre']): ?>
                                    <span class="badge badge-primary"><?php echo htmlspecialchars($book['genre']); ?></span>
                                <?php endif; ?>
                                <?php if ($book['subject']): ?>
                                    <span class="badge badge-info"><?php echo htmlspecialchars($book['subject']); ?></span>
                                <?php endif; ?>
                                <?php if ($book['publication_year']): ?>
                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($book['publication_year']); ?></span>
                                <?php endif; ?>
                            </p>
                            <p class="card-text">
                                <strong>Available:</strong> <?php echo htmlspecialchars($book['available_quantity']); ?> of <?php echo htmlspecialchars($book['quantity']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="./book_details.php?id=<?php echo htmlspecialchars($book['book_id']); ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                                <?php 
                                    // If the book has no available copies, show "Not Available"
                                    // Else if available and user already requested/borrowed, show "Already Requested"
                                    // Otherwise, show a "Borrow" button
                                    if ($book['available_quantity'] <= 0) {
                                        echo '<button class="btn btn-secondary btn-sm" disabled>Not Available</button>';
                                    } elseif ($book['user_borrowed_count'] > 0) {
                                        echo '<button class="btn btn-secondary btn-sm" disabled>Already Requested</button>';
                                    } else {
                                        ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                            <button type="submit" name="borrow" class="btn btn-success btn-sm">Borrow</button>
                                        </form>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center my-4">
                <h4>No books found</h4>
                <p>Try adjusting your search criteria.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="row mt-4">
        <div class="col-12">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&genre=<?php echo urlencode($genre); ?>&subject=<?php echo urlencode($subject); ?>&sort=<?php echo urlencode($sort); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&genre=<?php echo urlencode($genre); ?>&subject=<?php echo urlencode($subject); ?>&sort=<?php echo urlencode($sort); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&genre=<?php echo urlencode($genre); ?>&subject=<?php echo urlencode($subject); ?>&sort=<?php echo urlencode($sort); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include_once('../includes/dashboard_footer.php'); ?>
