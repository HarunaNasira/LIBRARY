<?php 
	
	include('../includes/dashboard_header.php'); 
	
?>

	<div class="content-wrapper">

        <!-- Page Title and Actions -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>
                <a class="text-dark" href="/">Manage Books</a> / Book Info
            </h5>
            <div class="d-inline-flex gap-2">
                <a href="booklist.php" class="mr-2 p-2 text-dark">
                    View All Books
                </a>
                <button class="btn btn-primary px-3 py-2 rounded-2">
                    Edit Book
                </button>
            </div>
        </div>

        <!-- Book Info -->
        <section class="p-4 my-4">
            <div class="d-flex gap-4">
                <div class="col-lg-2">
                    <img src="../assets/images/books/game_of_thrones.jpg" class="img-fluid rounded" alt="Book Cover">
                </div>
                <div class="col-lg-8 py-1">
                    <h2 class="card-title mb-4">A Game of Thrones</h2>
                    <p class="text-muted mb-4">By George R. R. Martin</p>
                    <div class="mb-4">
                        <h5>Summary</h5>
                        <p>A Game of Thrones is the first novel in A Song of Ice and Fire, a series of fantasy novels by American author George R. R. Martin. It was published in 1996. The novel follows the perspectives of nine main characters, including the members of House Stark, House Lannister, and House Targaryen, as they navigate the complex and treacherous landscape of the Seven Kingdoms.</p>
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
                                <p class="mb-0">978-0553103540</p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Category</label>
                                <p class="mb-0">Fantasy</p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Language</label>
                                <p class="mb-0">English</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="text-muted">Total Copies</label>
                                <p class="mb-0">10</p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Available Copies</label>
                                <p class="mb-0">8</p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Published Year</label>
                                <p class="mb-0">1996</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="text-muted">Shelf Location</label>
                                <p class="mb-0">FAN-MAR-001</p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Status</label>
                                <p class="mb-0"><span class="badge badge-success">Available</span></p>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted">Added Date</label>
                                <p class="mb-0">February 10, 2023</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
        
<?php include_once('../includes/dashboard_footer.php'); ?>
