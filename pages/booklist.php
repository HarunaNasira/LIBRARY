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
												<a href="view-book-details.php?id=' . $book['book_id'] . '" class="btn btn-sm">
													<i data-feather="eye" style="width: 14px;"></i>
												</a>
												<a href="edit_book.php?id=' . $book['book_id'] . '" class="btn btn-sm btn-info">
													<i data-feather="edit" style="width: 14px;"></i>
												</a>
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
                            <!-- <tr>
                                <td>BK002</td>
                                <td><img src="../assets/images/books/game_of_thrones.jpg" style="height: 50px;" class="rounded" alt="Book Cover"></td>
                                <td><a href="./book-details.php" class="text-primary">A song of Fire and Ice</a></td>
                                <td>George Orwell</td>
                                <td>Science Fiction</td>
                                <td>3</td>
                                <td><span class="badge badge-danger">Out</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <img src="../assets/images/icons/more-horizontal.svg" alt="">
                                  </button>
                                        <div class="dropdown-menu dropdown-menu-right p-1" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#"> View Details</a>
                                            <a class="dropdown-item" href="#"> Edit</a>
                                            <a class="dropdown-item text-danger" href="#"> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr> -->
                            <!-- More Rows -->
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
    
	<script>
        function confirmDelete(bookId) {
            document.getElementById('confirmDeleteBtn').href = 'booklist.php?delete=' + bookId;
            $('#deleteModal').modal('show');
        }
    </script>

<?php include_once('../includes/dashboard_footer.php'); ?>
