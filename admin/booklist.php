<?php 
	session_start();
	require_once '../config/db_connect.php';
  
	// Check if user is logged in and is an admin
	if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
		$_SESSION['error'] = "You must be logged in as an admin to access this page";
		redirect("../index.php");
	}

	// Handle delete button click
	if (isset($_GET['delete'])) {
		$book_id = sanitize($_GET['delete']);
		
		// Delete the book
		$delete_query = "DELETE FROM books WHERE book_id = '$book_id'";
		if ($conn->query($delete_query)) {
			$_SESSION['success'] = "Book deleted successfully!";
		} else {
			$_SESSION['error'] = "Error deleting book: " . $conn->error;
		}
		
		redirect("booklist.php");
	}

	// Handle edit form submission
	if (isset($_POST['edit_book'])) {
		$book_id = sanitize($_POST['book_id']);
		$title = sanitize($_POST['title']);
		$author = sanitize($_POST['author']);
		$isbn = sanitize($_POST['isbn']);
		$genre = sanitize($_POST['genre']);
		$subject = sanitize($_POST['subject']);
		$quantity = (int)sanitize($_POST['quantity']);
		$publication_year = sanitize($_POST['publication_year']);
		$shelf_code = sanitize($_POST['shelf_code']);
		$description = sanitize($_POST['description']);
		
		// Handle cover image upload
		$cover_image = '';
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
					
					// Add cover_image to update query
					$update_query = "UPDATE books SET 
						title = '$title',
						author = '$author',
						isbn = '$isbn',
						genre = '$genre',
						subject = '$subject',
						quantity = $quantity,
						publication_year = '$publication_year',
						shelf_code = '$shelf_code',
						description = '$description',
						cover_image = '$cover_image'
						WHERE book_id = $book_id";
				} else {
					$_SESSION['error'] = "Failed to upload image";
					redirect("booklist.php");
				}
			} else {
				$_SESSION['error'] = "Invalid file type. Only JPG, JPEG and PNG are allowed";
				redirect("booklist.php");
			}
		} else {
			// Update without changing the cover image
			$update_query = "UPDATE books SET 
				title = '$title',
				author = '$author',
				isbn = '$isbn',
				genre = '$genre',
				subject = '$subject',
				quantity = $quantity,
				publication_year = '$publication_year',
				shelf_code = '$shelf_code',
				description = '$description'
				WHERE book_id = $book_id";
		}
			
		if ($conn->query($update_query)) {
			$_SESSION['success'] = "Book updated successfully!";
		} else {
			$_SESSION['error'] = "Error updating book: " . $conn->error;
		}
		
		redirect("booklist.php");
	}

	// Get all books
	$books_query = "SELECT * FROM books ORDER BY title ASC";
	$books_result = $conn->query($books_query);

    // Include the header after all redirects
	include('../includes/dashboard_header.php'); 
?>

	<div class="content-wrapper">

		<!-- Page Title and Actions -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>Manage Books</h5>
            <div class="d-inline-flex gap-2">
                <a href="addbook.php" class="btn btn-primary mr-2 py-3 px-5 rounded-2">
                    +  Add New Book
                </a>
            </div>
        </div>

        <?php
            // Display success message
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            
            // Display error message
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
        ?>

        <!-- List of Books -->
        <div class="row">
            <div class="col-12">
              <div class="card rounded-3">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                        <table id="book-listing" class="table">
                          <thead>
                            <tr class="bg-primary text-white">
                                <th>#ID</th>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Genre</th>
                                <th>Subject</th>
                                <th>Quantity</th>
                                <th>Available</th>
                                <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
						  	<?php
								if ($books_result->num_rows > 0) {
									while ($book = $books_result->fetch_assoc()) {
										echo '<tr>
											<td>' . $book['book_id'] . '</td>
											<td><img src="../assets/images/books/' . ($book['cover_image'] ? $book['cover_image'] : 'default-book.svg') . '" alt="Book Cover" style="width: 30px; height: 50px; object-fit: cover; border-radius: 6px;"></td>
											<td>' . $book['title'] . '</td>
											<td>' . $book['author'] . '</td>
											<td>' . $book['isbn'] . '</td>
											<td>' . $book['genre'] . '</td>
											<td>' . $book['subject'] . '</td>
											<td>' . $book['quantity'] . '</td>
											<td>' . $book['available_quantity'] . '</td>
											<td>
												<a href="../pages/book_details.php?id=' . $book['book_id'] . '" class="btn btn-sm">
													<i data-feather="eye" style="width: 14px;"></i>
												</a>
												<button onclick="editBook(' . $book['book_id'] . ')" class="btn btn-sm btn-info">
													<i data-feather="edit" style="width: 14px;"></i>
												</button>
												<a href="javascript:void(0);" onclick="confirmDelete(' . $book['book_id'] . ')" class="btn btn-sm btn-danger">
													<i data-feather="trash-2" style="width: 14px;"></i>
												</a>
											</td>
										</tr>';
									}
								} else {
									echo '<tr><td colspan="9" class="text-center">No books found</td></tr>';
								}
							?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

	</div>

	<!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this book? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

	<!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1" role="dialog" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="book_id" id="edit_book_id">
                        
                        <div class="form-group row">
                            <div class="col">
                                <label>Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                            <div class="col">
                                <label>Author <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_author" name="author" required>
                            </div>
                            <div class="col">
                                <label>ISBN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_isbn" name="isbn" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <label>Genre <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_genre" name="genre" required>
                                    <option value="">Select Genre</option>
                                    <option value="Fiction">Fiction</option>
                                    <option value="Non-Fiction">Non-Fiction</option>
                                    <option value="Mystery">Mystery</option>
                                    <option value="Science Fiction">Science Fiction</option>
                                    <option value="Fantasy">Fantasy</option>
                                    <option value="Romance">Romance</option>
                                    <option value="Thriller">Thriller</option>
                                    <option value="Biography">Biography</option>
                                    <option value="History">History</option>
                                    <option value="Self-Help">Self-Help</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col">
                                <label>Subject/Topic <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_subject" name="subject" required>
                                    <option value="">Select Subject/Topic</option>
                                    <option value="Art">Art</option>
                                    <option value="Business">Business</option>
                                    <option value="Computer Science">Computer Science</option>
                                    <option value="Education">Education</option>
                                    <option value="Engineering">Engineering</option>
                                    <option value="Health">Health</option>
                                    <option value="History">History</option>
                                    <option value="Law">Law</option>
                                    <option value="Literature">Literature</option>
                                    <option value="Mathematics">Mathematics</option>
                                    <option value="Philosophy">Philosophy</option>
                                    <option value="Psychology">Psychology</option>
                                    <option value="Science">Science</option>
                                    <option value="Social Sciences">Social Sciences</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col">
                                <label>Total Copies <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_quantity" name="quantity" min="1" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <label>Published Year</label>
                                <input type="number" class="form-control" id="edit_publication_year" name="publication_year" min="1800" max="<?php echo date('Y'); ?>">
                            </div>
                            <div class="col">
                                <label>Shelf/Location Code</label>
                                <input type="text" class="form-control" id="edit_shelf_code" name="shelf_code">
                            </div>
                            <div class="col">
                                <label for="edit_cover_image">Upload Book Cover</label>
                                <input type="file" class="form-control" id="edit_cover_image" name="cover_image">
                                <small class="form-text text-muted">Leave empty to keep current cover image</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_description">Book Summary <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_description" name="description" rows="6" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_book" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
	<script>
        function confirmDelete(bookId) {
            document.getElementById('confirmDeleteBtn').href = 'booklist.php?delete=' + bookId;
            $('#deleteModal').modal('show');
        }

        function editBook(bookId) {
            // Show the modal first
            $('#editBookModal').modal('show');
            
            // Fetch book details via AJAX
            $.ajax({
                url: 'get_book_details.php',
                type: 'POST',
                data: { book_id: bookId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Populate the form with book details
                        $('#edit_book_id').val(response.book.book_id);
                        $('#edit_title').val(response.book.title);
                        $('#edit_author').val(response.book.author);
                        $('#edit_isbn').val(response.book.isbn);
                        $('#edit_genre').val(response.book.genre);
                        $('#edit_subject').val(response.book.subject);
                        $('#edit_quantity').val(response.book.quantity);
                        $('#edit_publication_year').val(response.book.publication_year);
                        $('#edit_shelf_code').val(response.book.shelf_code);
                        $('#edit_description').val(response.book.description);
                    } else {
                        alert('Error fetching book details: ' + response.message);
                        $('#editBookModal').modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error fetching book details: ' + error);
                    $('#editBookModal').modal('hide');
                }
            });
        }
    </script>

<?php include_once('../includes/dashboard_footer.php'); ?>
