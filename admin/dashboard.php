<?php 
	session_start(); 
	require_once '../config/db_connect.php';

	// Check if user is logged in as an admin
	if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
		$_SESSION['error'] = "You must be logged in as an admin to access this page";
		header("Location: ../index.php");
		exit;
	}

	// Get statistics
	$totalBooks = $conn->query("SELECT COUNT(*) as count FROM books")->fetch_assoc()['count'];
	$lendedBooks = $conn->query("SELECT COUNT(*) as count FROM book_loans WHERE status = 'borrowed'")->fetch_assoc()['count'];
	$availableBooks = $conn->query("SELECT SUM(available_quantity) as count FROM books")->fetch_assoc()['count'];
	$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];

	// Ensure we have valid numbers (not null)
	$lendedBooks = $lendedBooks ? $lendedBooks : 0;
	$availableBooks = $availableBooks ? $availableBooks : 0;

	// Get data for monthly lending chart
	$monthlyLendingData = array_fill(0, 12, 0); // Initialize with zeros for all months
	$monthlyQuery = "SELECT MONTH(borrow_date) as month, COUNT(*) as count 
					FROM book_loans 
					WHERE YEAR(borrow_date) = YEAR(CURRENT_DATE) 
					GROUP BY MONTH(borrow_date)";
	$monthlyResult = $conn->query($monthlyQuery);
	
	if ($monthlyResult) {
		while ($row = $monthlyResult->fetch_assoc()) {
			// Month index should be 0-11 for JavaScript arrays
			$monthIndex = intval($row['month']) - 1;
			$monthlyLendingData[$monthIndex] = intval($row['count']);
		}
	}

	// Get data for donut chart
	$pendingRequests = $conn->query("SELECT COUNT(*) as count FROM book_loans WHERE status = 'pending'")->fetch_assoc()['count'];
	$overdueBooks = $conn->query("SELECT COUNT(*) as count FROM book_loans WHERE status = 'borrowed' AND due_date < CURDATE()")->fetch_assoc()['count'];
	$returnedBooks = $conn->query("SELECT COUNT(*) as count FROM book_loans WHERE status = 'returned'")->fetch_assoc()['count'];
	
	// Ensure we have valid numbers (not null)
	$pendingRequests = $pendingRequests ? $pendingRequests : 0;
	$overdueBooks = $overdueBooks ? $overdueBooks : 0;
	$returnedBooks = $returnedBooks ? $returnedBooks : 0;

	// Get recent checkouts
	$recentLoans = $conn->query("
		SELECT l.loan_id, b.isbn, b.title, b.author, u.full_name, l.borrow_date, l.due_date, l.status 
		FROM book_loans l 
		JOIN books b ON l.book_id = b.book_id 
		JOIN users u ON l.user_id = u.user_id 
		ORDER BY l.borrow_date DESC LIMIT 5
	");
	
	// Debug data
	$debugData = [
		'totalBooks' => $totalBooks,
		'lendedBooks' => $lendedBooks,
		'availableBooks' => $availableBooks,
		'pendingRequests' => $pendingRequests,
		'overdueBooks' => $overdueBooks,
		'monthlyData' => $monthlyLendingData
	];
	
	include('../includes/dashboard_header.php');
?>

<!-- Add chart data as global JavaScript variables -->
<script>
// Pass data to chart.js file for the bar chart
window.monthlyBorrowData = <?php echo json_encode(array_values($monthlyLendingData)); ?>;

// Store donut chart data for direct use 
const donutChartData = [
	['Borrowed', <?php echo $lendedBooks; ?>],
	['Available', <?php echo $availableBooks; ?>],
	['Pending', <?php echo $pendingRequests; ?>],
	['Overdue', <?php echo $overdueBooks; ?>]
];
</script>

	<div class="content-wrapper">

		<!-- Welcome -->
		<section class="my-4 d-flex justify-content-between">
			<h2 class="font-weight-bold">Hello, 
        		<span class="welcome-text"><?php echo $_SESSION['full_name']; ?> |</span>
      		</h2>
      		<h6><?php echo date('l | F jS, Y'); ?></h6>
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
                  <h3 class="card-text">
				  <?php echo $totalBooks; ?>
				  </h3>
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
                  <h3 class="card-text">
				  <?php echo $lendedBooks; ?>
				  </h3>
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
                  <h3 class="card-text">
				  <?php echo $availableBooks; ?>
				  </h3>
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
                  <h3 class="card-text">
				  <?php echo $totalUsers; ?>
				  </h3>
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
					<h5 class="card-title">Book Status Overview</h5>
					<small class="text-muted">Distribution of book statuses</small>
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

		<!-- Quick Actions -->
		<section class="row mt-4">
			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Quick Actions</h4>
						<div class="row">
							<div class="col-md-3">
								<a href="add_book.php" class="btn btn-primary btn-block">Add New Book</a>
							</div>
							<div class="col-md-3">
								<a href="add_user.php" class="btn btn-success btn-block">Add New User</a>
							</div>
							<div class="col-md-3">
								<a href="issue_book.php" class="btn btn-info btn-block">Issue Book</a>
							</div>
							<div class="col-md-3">
								<a href="return_book.php" class="btn btn-warning btn-block">Return Book</a>
							</div>
						</div>
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
									<th>Borrowed By</th>
									<th>Issue Date</th>
									<th>Due Date</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if ($recentLoans->num_rows > 0) {
										while ($loan = $recentLoans->fetch_assoc()) {
											$statusClass = '';
											switch($loan['status']) {
												case 'borrowed':
													$statusClass = 'primary';
													break;
												case 'returned':
													$statusClass = 'success';
													break;
												case 'pending':
													$statusClass = 'warning';
													break;
												case 'overdue':
													$statusClass = 'danger';
													break;
												default:
													$statusClass = 'secondary';
											}
											echo '<tr>
												<td>#' . $loan['loan_id'] . '</td>
												<td>' . $loan['isbn'] . '</td>
												<td>' . $loan['title'] . '</td>
												<td>' . $loan['author'] . '</td>
												<td>' . $loan['full_name'] . '</td>
												<td>' . date('d M Y', strtotime($loan['borrow_date'])) . '</td>
												<td>' . date('d M Y', strtotime($loan['due_date'])) . '</td>
												<td><span class="badge badge-' . $statusClass . '">' . ucfirst($loan['status']) . '</span></td>
											</tr>';
										}
									} else {
										echo '<tr><td colspan="8" class="text-center">No recent checkouts</td></tr>';
									}
								?>
							</tbody>
						</table>
						</div>
					</div>
				</div>
			</div>
		</section>

	</div>
        
<!-- Custom script for C3 donut chart -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize C3 donut chart directly
    var c3DonutChart = c3.generate({
        bindto: '#c3-donut-chart',
        data: {
            columns: donutChartData,
            type: 'donut'
        },
        color: {
            pattern: ['rgba(54, 162, 235, 1)', 'rgba(75, 192, 192, 1)', 'rgba(255, 206, 86, 1)', 'rgba(255, 99, 132, 1)']
        },
        donut: {
            title: "Book Status"
        }
    });
});
</script>

<?php include_once('../includes/dashboard_footer.php'); ?>
