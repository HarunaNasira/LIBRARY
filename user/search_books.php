<?php
session_start();
require_once '../config/db_connect.php';

// Check if user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    $_SESSION['error'] = "You must be logged in as a user to access this page";
    redirect("../index.php");
}

// Get all available genres for filter
$genres_query = "SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL AND genre != '' ORDER BY genre ASC";
$genres_result = $conn->query($genres_query);
$genres = array();
while ($genre = $genres_result->fetch_assoc()) {
    $genres[] = $genre['genre'];
}

// Get all available subjects for filter
$subjects_query = "SELECT DISTINCT subject FROM books WHERE subject IS NOT NULL AND subject != '' ORDER BY subject ASC";
$subjects_result = $conn->query($subjects_query);
$subjects = array();
while ($subject = $subjects_result->fetch_assoc()) {
    $subjects[] = $subject['subject'];
}

// Set default values
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$genre = isset($_GET['genre']) ? sanitize($_GET['genre']) : '';
$subject = isset($_GET['subject']) ? sanitize($_GET['subject']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'title_asc';

// Check if search form was submitted
$search_submitted = isset($_GET['search']) || isset($_GET['genre']) || isset($_GET['subject']) || isset($_GET['sort']);

// Pagination
$items_per_page = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Build the query
$where_conditions = array("available_quantity > 0");
$params = array();

if (!empty($search)) {
    $where_conditions[] = "(title LIKE ? OR author LIKE ? OR isbn LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($genre)) {
    $where_conditions[] = "genre = ?";
    $params[] = $genre;
}

if (!empty($subject)) {
    $where_conditions[] = "subject = ?";
    $params[] = $subject;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Sorting
$order_by = "";
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
}

// Count total books matching criteria
$count_query = "SELECT COUNT(*) as total FROM books $where_clause";
$stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_books = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $items_per_page);

// Get books for current page
$books_query = "SELECT * FROM books $where_clause $order_by LIMIT $offset, $items_per_page";
$stmt = $conn->prepare($books_query);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$books_result = $stmt->get_result();

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
        // Check if user has reached the maximum number of books allowed (3)
        $check_limit_query = "SELECT COUNT(*) as count FROM book_loans WHERE user_id = '$user_id' AND status = 'borrowed'";
        $limit_result = $conn->query($check_limit_query);
        $borrowed_count = $limit_result->fetch_assoc()['count'];
        
        if ($borrowed_count >= 3) {
            $_SESSION['error'] = "You have reached the maximum number of books you can borrow (3)";
        } else {
            // Check if book is available
            $check_available_query = "SELECT available_quantity FROM books WHERE book_id = '$book_id'";
            $available_result = $conn->query($check_available_query);
            $available = $available_result->fetch_assoc()['available_quantity'];
            
            if ($available > 0) {
                // Calculate due date (14 days from now)
                $due_date = date('Y-m-d', strtotime('+14 days'));
                
                // Start transaction
                $conn->begin_transaction();
                
                try {
                    // Insert loan record
                    $insert_loan_query = "INSERT INTO book_loans (book_id, user_id, due_date, status) VALUES ('$book_id', '$user_id', '$due_date', 'borrowed')";
                    $conn->query($insert_loan_query);
                    
                    // Update available quantity
                    $update_book_query = "UPDATE books SET available_quantity = available_quantity - 1 WHERE book_id = '$book_id'";
                    $conn->query($update_book_query);
                    
                    // Commit transaction
                    $conn->commit();
                    
                    $_SESSION['success'] = "Book borrowed successfully! Due date: " . date('F j, Y', strtotime($due_date));
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    $_SESSION['error'] = "Error borrowing book: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "This book is not available for borrowing";
            }
        }
    }
    
    // Redirect to remove POST data (to prevent re-borrowing on refresh)
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="font-weight-bold mb-0">Search Books</h4>
                    </div>
                </div>
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
        
        <!-- Search and filters -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="GET" class="form">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="search">Search</label>
                                        <input type="text" class="form-control py-3" id="search" name="search" placeholder="Search by title, author, ISBN, or description" value="<?php echo $search; ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="genre">Genre</label>
                                        <select class="form-control" id="genre" name="genre">
                                            <option value="">All Genres</option>
                                            <?php foreach ($genres as $g): ?>
                                                <option value="<?php echo $g; ?>" <?php echo ($genre == $g) ? 'selected' : ''; ?>><?php echo $g; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="subject">Subject</label>
                                        <select class="form-control" id="subject" name="subject">
                                            <option value="">All Subjects</option>
                                            <?php foreach ($subjects as $s): ?>
                                                <option value="<?php echo $s; ?>" <?php echo ($subject == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="sort">Sort By</label>
                                        <select class="form-control" id="sort" name="sort">
                                            <option value="title_asc" <?php echo ($sort == 'title_asc') ? 'selected' : ''; ?>>Title (A-Z)</option>
                                            <option value="title_desc" <?php echo ($sort == 'title_desc') ? 'selected' : ''; ?>>Title (Z-A)</option>
                                            <option value="author_asc" <?php echo ($sort == 'author_asc') ? 'selected' : ''; ?>>Author (A-Z)</option>
                                            <option value="author_desc" <?php echo ($sort == 'author_desc') ? 'selected' : ''; ?>>Author (Z-A)</option>
                                            <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                                            <option value="oldest" <?php echo ($sort == 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
                                        </select>
                                    </div>
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
        <?php if ($search_submitted): ?>
        <div class="row">
            <?php if ($books_result->num_rows > 0): ?>
                <!-- Header -->
                <div class="row my-3">
                    <h5>Search Results:</h5>
                </div>
                <?php while ($book = $books_result->fetch_assoc()): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img class="card-img-top" src="../assets/images/books/<?php echo $book['cover_image'] ? $book['cover_image'] : 'default-book.jpg'; ?>" 
                                    alt="<?php echo $book['title']; ?>" style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $book['title']; ?></h5>
                                <p class="card-text">
                                    <small class="text-muted">By <?php echo $book['author']; ?></small><br>
                                    <?php if ($book['genre']): ?>
                                        <span class="badge badge-primary"><?php echo $book['genre']; ?></span>
                                    <?php endif; ?>
                                    <?php if ($book['subject']): ?>
                                        <span class="badge badge-info"><?php echo $book['subject']; ?></span>
                                    <?php endif; ?>
                                    <?php if ($book['publication_year']): ?>
                                        <span class="badge badge-secondary"><?php echo $book['publication_year']; ?></span>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text">
                                    <strong>Available:</strong> <?php echo $book['available_quantity']; ?> of <?php echo $book['quantity']; ?>
                                </p>
                                <div class="d-flex justify-content-between">
                                    <a href="../pages/book_details.php?id=<?php echo $book['book_id']; ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                                    <?php if ($book['available_quantity'] > 0): ?>
                                        <form method="POST">
                                            <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                            <button type="submit" name="borrow" class="btn btn-success btn-sm">Borrow</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>Unavailable</button>
                                    <?php endif; ?>
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
        <div class="row my-3">
            <h5>Search Results:</h5>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>&genre=<?php echo $genre; ?>&subject=<?php echo $subject; ?>&sort=<?php echo $sort; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&genre=<?php echo $genre; ?>&subject=<?php echo $subject; ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>&genre=<?php echo $genre; ?>&subject=<?php echo $subject; ?>&sort=<?php echo $sort; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="row">
            <div class="col-12 text-center my-4">
                <h4>Welcome to the Nas (Nasira's) Library Manager</h4>
                <p>Use the search form above to find books.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php include_once('../includes/dashboard_footer.php'); ?>