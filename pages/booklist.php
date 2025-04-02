<?php 
	
	include('../includes/dashboard_header.php'); 
	
?>

	<div class="content-wrapper">

		<!-- Page Title and Actions -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>Manage Books</h5>
            <div class="d-inline-flex gap-2">
                <a href="addbook.php" class="btn btn-primary mr-2 p-3 rounded-2">
                    +  Add Books
                </a>
                <button class="btn btn-primary p-3 rounded-2">
                    +  Add Book Category
                </button>
            </div>
        </div>

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
                                <th>Book ID</th>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Genre</th>
                                <th>Copies</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                                <td>BK001</td>
                                <td><img src="../assets/images/books/in_the_chest_of_a_woman.png" style="height: 50px;" class="rounded" alt="Book Cover"></td>
                                <td><a href="./book-details.php" class="text-primary">In the Chest of a Woman</a></td>
                                <td>F. Scott Fitzgerald</td>
                                <td>Classic Literature</td>
                                <td>5</td>
                                <td><span class="badge badge-success">Available</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <img src="../assets/images/icons/more-horizontal.svg" width="10px">
                                  </button>
                                        <div class="dropdown-menu dropdown-menu-right p-1" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#"> View Details</a>
                                            <a class="dropdown-item" href="#"> Edit</a>
                                            <a class="dropdown-item text-danger" href="#"> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
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
                            </tr>
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
        
<?php include_once('../includes/dashboard_footer.php'); ?>
