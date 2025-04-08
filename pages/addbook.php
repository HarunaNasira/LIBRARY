<?php 
	session_start();
    require_once '../config/db_connect.php';

    // Check if user is logged in and is an admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['error'] = "You must be logged in as an admin to access this page";
        redirect("../index.php");
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $isbn = sanitize($_POST['isbn']);
        $title = sanitize($_POST['title']);
        $author = sanitize($_POST['author']);
        $shelf_code = sanitize($_POST['shelfCode']);
        $publication_year = sanitize($_POST['publication_year']);
        $genre = sanitize($_POST['genre']);
        $subject = sanitize($_POST['subject']);
        $description = sanitize($_POST['description']);
        $quantity = sanitize($_POST['quantity']);
        
        // Validate input
        $errors = [];
        
        if (empty($isbn)) {
            $errors[] = "ISBN is required";
        }
        
        if (empty($title)) {
            $errors[] = "Title is required";
        }
        
        if (empty($author)) {
            $errors[] = "Author is required";
        }
        
        if (empty($quantity) || !is_numeric($quantity) || $quantity <= 0) {
            $errors[] = "Quantity must be a positive number";
        }
        
        // Check if ISBN already exists
        $check_isbn_query = "SELECT * FROM books WHERE isbn = '$isbn'";
        $result = $conn->query($check_isbn_query);
        if ($result->num_rows > 0) {
            $errors[] = "ISBN already exists";
        }
        
        // Process file upload if there's no error
        $cover_image = '';
        if (empty($errors)) {
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
                $file_type = $_FILES['cover_image']['type'];
                
                if (in_array($file_type, $allowed_types)) {
                    $file_name = time() . '_' . $_FILES['cover_image']['name'];
                    $upload_dir = '../assets/images/books/';
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $upload_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_path)) {
                        $cover_image = $file_name;
                    } else {
                        $errors[] = "Failed to upload image";
                    }
                } else {
                    $errors[] = "Invalid file type. Only JPG, JPEG and PNG are allowed";
                }
            }
        }
        
        // Insert book if there's no error
        if (empty($errors)) {
            $insert_query = "INSERT INTO books (isbn, title, author, shelf_code, publication_year, genre, subject, description, quantity, available_quantity, cover_image) 
                            VALUES ('$isbn', '$title', '$author', '$shelf_code', '$publication_year', '$genre', '$subject', '$description', '$quantity', '$quantity', '$cover_image')";
            
            if ($conn->query($insert_query)) {
                $_SESSION['success'] = "Book added successfully!";
                redirect("booklist.php");
            } else {
                $errors[] = "Error adding book: " . $conn->error;
            }
        }
    }
	
	// Include the header after all redirects
	include('../includes/dashboard_header.php'); 
?>

	<div class="content-wrapper">

        <!-- Page Title and Actions -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>
                <a class="text-dark" href="/">Manage Books</a> / Add New Book
            </h5>
            <div class="d-inline-flex gap-2">
                </a>
                <a href="booklist.php" class="btn btn-primary p-3 rounded-2">
                    View All Books
                </a>
            </div>
        </div>

        <!-- Display errors if any -->
        <?php
            if (isset($errors) && !empty($errors)) {
                echo '<div class="alert alert-danger"><ul>';
                foreach ($errors as $error) {
                    echo '<li>' . $error . '</li>';
                }
                echo '</ul></div>';
            }
        ?>

        <!-- Form -->
        <div class="row">
            <div class="card rounded-3">
                <div class="card-body">
                    <h4 class="card-title">Book Information</h4>

                    <form class="forms-sample" method="POST" enctype="multipart/form-data">
                        <div class="form-group row">
                            <div class="col">
                                <label>Title <span class="text-danger">*</span></label>
                                <input class="form-control" name="title" value="<?php echo isset($_POST['title']) ? $_POST['title'] : ''; ?>" required placeholder="In the Chest of a Woman"/>
                            </div>
                            <div class="col">
                                <label>Author <span class="text-danger">*</span></label>
                                <input class="form-control" name="author" value="<?php echo isset($_POST['author']) ? $_POST['author'] : ''; ?>" required placeholder="Efo Kodjo Mawugbe"/>
                            </div>
                            <div class="col">
                                <label>ISBN <span class="text-danger">*</span></label>
                                <input class="form-control" name="isbn" required value="<?php echo isset($_POST['isbn']) ? $_POST['isbn'] : ''; ?>" placeholder="___-____-___-_"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label>Genre <span class="text-danger">*</span></label>
                                <select class="form-control" id="genre" name="genre">
                                    <option value="">Select Genre</option>
                                    <option value="Fiction" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'Fiction') ? 'selected' : ''; ?>>Fiction</option>
                                    <option value="Non-Fiction" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'Non-Fiction') ? 'selected' : ''; ?>>Non-Fiction</option>
                                    <option value="Mystery" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'Mystery') ? 'selected' : ''; ?>>Mystery</option>
                                    <option value="Science Fiction" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'Science Fiction') ? 'selected' : ''; ?>>Science Fiction</option>
                                    <option value="Fantasy" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'Fantasy') ? 'selected' : ''; ?>>Fantasy</option>
                                    <option value="Romance" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'Romance') ? 'selected' : ''; ?>>Romance</option>
                                    <option value="Thriller" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'Thriller') ? 'selected' : ''; ?>>Thriller</option>
                                    <option value="Biography" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'Biography') ? 'selected' : ''; ?>>Biography</option>
                                    <option value="History" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'History') ? 'selected' : ''; ?>>History</option>
                                    <option value="Self-Help" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'Self-Help') ? 'selected' : ''; ?>>Self-Help</option>
                                    <option value="Other" <?php echo (isset($_POST['genre']) && $_POST['genre'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col">
                                <label>Subject/Topic <span class="text-danger">*</span></label>
                                <select class="form-control" id="subject" name="subject">
                                    <option value="">Select Subject/Topic</option>
                                    <option value="Art" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Art') ? 'selected' : ''; ?>>Art</option>
                                    <option value="Business" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Business') ? 'selected' : ''; ?>>Business</option>
                                    <option value="Computer Science" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                                    <option value="Education" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Education') ? 'selected' : ''; ?>>Education</option>
                                    <option value="Engineering" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Engineering') ? 'selected' : ''; ?>>Engineering</option>
                                    <option value="Health" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Health') ? 'selected' : ''; ?>>Health</option>
                                    <option value="History" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'History') ? 'selected' : ''; ?>>History</option>
                                    <option value="Law" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Law') ? 'selected' : ''; ?>>Law</option>
                                    <option value="Literature" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Literature') ? 'selected' : ''; ?>>Literature</option>
                                    <option value="Mathematics" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Mathematics') ? 'selected' : ''; ?>>Mathematics</option>
                                    <option value="Philosophy" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Philosophy') ? 'selected' : ''; ?>>Philosophy</option>
                                    <option value="Psychology" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Psychology') ? 'selected' : ''; ?>>Psychology</option>
                                    <option value="Science" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Science') ? 'selected' : ''; ?>>Science</option>
                                    <option value="Social Sciences" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Social Sciences') ? 'selected' : ''; ?>>Social Sciences</option>
                                    <option value="Technology" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Technology') ? 'selected' : ''; ?>>Technology</option>
                                    <option value="Other" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col">
                                <label>Total Copies <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : '1'; ?>" min="1" required>
                            </div>
                        </div>
                        <div class="my-3"></div>

                        <h4 class="card-title nav-underline">Book Features</h4>
                        <div class="form-group row">
                            <div class="col">
                                <label>Published Year</label>
                                <input type="number" class="form-control" id="publication_year" name="publication_year" value="<?php echo isset($_POST['publication_year']) ? $_POST['publication_year'] : ''; ?>" min="1800" max="<?php echo date('Y'); ?>">
                            </div>
                            <div class="col">
                                <label>Shelf/Location Code</label>
                                <input class="form-control" name="shelfCode" value="<?php echo isset($_POST['shelfCode']) ? $_POST['shelfCode'] : ''; ?>" />
                            </div>
                            <div class="col">
                                <label for="formFile" class="form-label">Upload Book Cover <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="cover_image" name="cover_image">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="floatingTextarea2">Book Summary <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="6"><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mr-2">Add Book</button>
                        <a href="manage_books.php" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

    </div>
        
<?php include_once('../includes/dashboard_footer.php'); ?>
