<?php 
	
	include_once('../includes/header.php'); 
	
?>

	<div class="content-wrapper">

		<!-- Welcome -->
		<section class="my-6">
			<h6>Welcome, Nasira</h6>
		</section>
		
		<!-- Cards -->
		<section class="row">
            <div class="col-12">
              <div class="row bg-white rounded p-6 mt-6">
                <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card rounded-md">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Total books</h4>
                      <div class="d-flex justify-content-between">
                        <!-- <p class="text-muted">Avg. Session</p> -->
                        <p class="text-muted">0</p>
                      </div>
                      <div class="progress progress-md">
                        <div class="progress-bar bg-info w-25" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Lended Books</h4>                      
                      <div class="d-flex justify-content-between">
                        <p class="text-muted">0</p>
                      </div>
                      <div class="progress progress-md">
                        <div class="progress-bar bg-success w-25" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Available Books</h4>
                      <div class="d-flex justify-content-between">
                        <p class="text-muted">0</p>
                      </div>
                      <div class="progress progress-md">
                        <div class="progress-bar bg-danger w-25" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Total users</h4>
                      <div class="d-flex justify-content-between">
                        <p class="text-muted">Avg. Session</p>
                        <p class="text-muted">0</p>
                      </div>
                      <div class="progress progress-md">
                        <div class="progress-bar bg-warning w-25" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
		</section>

	</div>
        
<?php include_once('../includes/footer.php'); ?>
