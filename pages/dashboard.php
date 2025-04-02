<?php 
	
	include('../includes/dashboard_header.php'); 
	
?>

	<div class="content-wrapper">

		<!-- Welcome -->
		<section class="my-4 d-flex justify-content-between">
			<h2 class="font-weight-bold">Hello, 
        		<span class="welcome-text">Nasira |</span>
      		</h2>
      		<h6>Thursday | April 1st, 2025</h6>
		</section>
		
		<!-- Statistics -->
		<section class="row">
      <div class="col-12">
        <div class="row">

          <!-- Total Books -->
          <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card rounded-md">
            <div class="card rounded-3 border">
              <div class="card-body">
                <h6 class="card-title">Total Books</h6>
                <div class="d-flex justify-content-between">
                  <h3 class="card-text">2000</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Lended Books -->
          <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card rounded-md">
            <div class="card rounded-3 border">
              <div class="card-body">
                <h6 class="card-title">Lended Books</h6>
                <div class="d-flex justify-content-between">
                  <h3 class="card-text">0</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Available Books -->
          <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card rounded-md">
            <div class="card rounded-3 border">
              <div class="card-body">
                <h6 class="card-title">Available Books</h6>
                <div class="d-flex justify-content-between">
                  <h3 class="card-text">0</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Users -->
          <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card">
            <div class="card rounded-3 border">
              <div class="card-body">
                <h6 class="card-title">Total users</h6>
                <div class="d-flex justify-content-between">
                  <h3 class="card-text">0</h3>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
		</section>


		<!-- Lending Rate -->
		 <section class="row">
			<div class="col-lg-5 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Book Availabiltiy</h5>
						<small class="text-muted">Overview of Pie Chart</small>
						<div id="c3-donut-chart"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-7 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Book Lending Trend</h5>
						<canvas id="barChart"></canvas>
					</div>
				</div>
			</div>
		 </section>

		<!-- Recent Checkouts -->
		<section class="row">
			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<h5 class="card-text">Recent Checkouts</h5>
							<a href="#" class="btn btn-inverse-success btn-rounded btn-fw">View all</a>
						</div>
						<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th>ID</th>
									<th>ISBN</th>
									<th>Title</th>
									<th>Author</th>
									<th>Obtained By</th>
									<th>Issued Date</th>
									<th>Return Date</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>#4543</td>
									<td>5531</td>
									<td>In the Chest of a Woman</td>
									<td>Daniel Oklu</td>
									<td>Jace Brian</td>
									<td>12 March 2025</td>
									<td>12 April 2025</td>
								</tr>
								<tr>
									<td>#4544</td>
									<td>5532</td>
									<td>The Lost City</td>
									<td>Emily Wilson</td>
									<td>Olivia Brown</td>
									<td>15 April 2025</td>
									<td>15 May 2025</td>
								</tr>
								<tr>
									<td>#4545</td>
									<td>5533</td>
									<td>The Last Hope</td>
									<td>Michael Davis</td>
									<td>William Lee</td>
									<td>20 May 2025</td>
									<td>20 June 2025</td>
								</tr>
								<tr>
									<td>#4546</td>
									<td>5534</td>
									<td>The Forgotten Kingdom</td>
									<td>Sarah Taylor</td>
									<td>James Smith</td>
									<td>25 June 2025</td>
									<td>25 July 2025</td>
								</tr>
								<tr>
									<td>#4547</td>
									<td>5535</td>
									<td>The Hidden Treasure</td>
									<td>Kevin White</td>
									<td>Elizabeth Johnson</td>
									<td>30 July 2025</td>
									<td>30 August 2025</td>
								</tr>
							</tbody>
						</table>
						</div>
					</div>
				</div>
			</div>
		</section>

	</div>
        
<?php include_once('../includes/dashboard_footer.php'); ?>
