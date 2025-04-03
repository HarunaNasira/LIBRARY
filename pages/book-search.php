<?php 
	
	include('../includes/dashboard_header.php'); 
	
?>

	<div class="content-wrapper" style="background-image: url('../assets/images/login_bg.svg'); background-size: cover;">

		<!-- Search Form -->
        <div class="row">
            <div class="col-lg-12">
                <div class="search-field p-2">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control" placeholder="What do you want to read today?">
                        <button class="btn btn-rounded btn-primary font-weight-bold">Search</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="d-flex justify-content-between align-items-center my-2 p-2">
            <h6>Filter</h6>
            <div>-++-  All Results</div>
            <!-- <li>Title</li>
            <li>Author</li>
            <li>Genre</li> -->
        </div>

        <!-- Search Results -->
        <div class="d-flex my-5 text-white">
            <div class="col-lg-2">
                <img src="../assets/images/books/game_of_thrones.jpg" class="img-fluid rounded w-50" alt="Book Cover">
            </div>
            <div class="col-lg-10 py-1">
                <div class="mb-2 d-flex justify-content-between">
                    <h4 class="card-title">A Game of Thrones</h4>
                    <button class="btn btn-success">Borrow</button>
                </div>
                <div class="mb-4 italics">
                    <h6><i>Summary</i></h6>
                    <p>
                        <i>
                        A Game of Thrones is the first novel in A Song of Ice and Fire, a series of fantasy novels 
                        by American author George R. R. Martin. It was published in 1996. The novel follows the ...
                        </i>
                    </p>
                </div>
            </div>
        </div>

    </div>
        
<?php include_once('../includes/dashboard_footer.php'); ?>
