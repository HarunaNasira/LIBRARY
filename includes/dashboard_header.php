<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Nasira - Library Management System</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="../assets/css/flag-icon.min.css">
  <link rel="stylesheet" href="../assets/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
  <link rel="stylesheet" href="../assets/css/simple-line-icons.css">
  <link rel="stylesheet" href="../assets/css/feather.css">
  <link rel="stylesheet" href="../assets/css/vendor.bundle.base.css">
  
  <link rel="stylesheet" href="../assets/css/main.css">
  <link rel="stylesheet" href="../assets/css/chart/c3.min.css">
  <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="../assets/css/custom-datatable.css">

  <script src="https://unpkg.com/feather-icons"></script>
  <link rel="shortcut icon" href="../assets/images/favicon.svg" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css">
</head>

<body>
  <div class="container-scroller">
    
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
        <a class="navbar-brand brand-logo" href="./dashboard.php">
          <img src="../assets/images/logo_dark.svg" alt="logo"/>
		</a>
        <a class="navbar-brand brand-logo-mini mobile-nav-toggle" id="mobileNavToggle">
          <img src="../assets/images/favicon.svg" alt="logo"/>
		</a>
      </div>

      <div class="navbar-menu-wrapper d-flex align-items-center"> 
        <ul class="navbar-nav w-100 d-flex justify-content-end">
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
          <li class="nav-item">
            <div class="form-check form-switch d-flex align-items-center gap-5">
              <label class="form-check-label" for="emailReminderToggle">Email Reminders</label>
              <input class="form-check-input" type="checkbox" role="switch" id="emailReminderToggle" 
                <?php echo isset($_SESSION['email_reminder']) && $_SESSION['email_reminder'] ? 'checked' : ''; ?>>
            </div>
          </li>
          <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link" target="_blank" href="../test_reminders.php">Test Email Reminders</a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>

    <!-- Mobile Navigation -->
    <div class="mobile-nav" id="mobileNav">
      <div class="mobile-nav-header mt-5 pt-5">
        <button class="mobile-nav-close" id="mobileNavClose">
          <i data-feather="x"></i>
        </button>
      </div>
      <ul class="mobile-nav-links w-25 mx-auto">
        <li>
          <a href="dashboard.php">
            <i data-feather="grid" class="me-2"></i>
            Dashboard
          </a>
        </li>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <li>
            <a href="booklist.php">
              <i data-feather="archive" class="me-2"></i>
              Book List
            </a>
          </li>
          <li>
            <a href="addbook.php">
              <i data-feather="plus" class="me-2"></i>
              Add Book
            </a>
          </li>
          <li>
            <a href="issue_book.php">
              <i data-feather="book-open" class="me-2"></i>
              Issue Book
            </a>
          </li>
          <li>
            <a href="return_book.php">
              <i data-feather="rotate-ccw" class="me-2"></i>
              Return Book
            </a>
          </li>
          <li>
            <a href="add_user.php">
              <i data-feather="user-plus" class="me-2"></i>
              Add Users
            </a>
          </li>
          <li>
            <a href="view_users.php">
              <i data-feather="users" class="me-2"></i>
              View All Users
            </a>
          </li>
        <?php else: ?>
          <li>
            <a href="search_books.php">
              <i data-feather="search" class="me-2"></i>
              Search Books
            </a>
          </li>
          <li>
            <a href="borrowed_books.php">
              <i data-feather="layers" class="me-2"></i>
              My Borrowed Books
            </a>
          </li>
          <li>
            <a href="loan_history.php">
              <i data-feather="archive" class="me-2"></i>
              Loan History
            </a>
          </li>
        <?php endif; ?>
        <li>
          <a href="profile.php">
            <i data-feather="user" class="me-2"></i>
            Profile
          </a>
        </li>
        <li>
          <a href="../authentication/logout.php">
            <i data-feather="power" class="me-2"></i>
            Logout
          </a>
        </li>
      </ul>
    </div>

    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- SideNav -->
      <?php require('../includes/sidenav.php'); ?>
      
      <!-- main-panel start -->
      <div class="main-panel">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Navigation Toggle
    const mobileNavToggle = document.getElementById('mobileNavToggle');
    const mobileNavClose = document.getElementById('mobileNavClose');
    const mobileNav = document.getElementById('mobileNav');

    if (mobileNavToggle && mobileNavClose && mobileNav) {
        mobileNavToggle.addEventListener('click', () => {
            mobileNav.classList.toggle('active');
        });

        mobileNavClose.addEventListener('click', () => {
            mobileNav.classList.remove('active');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (mobileNav.classList.contains('active') && 
                !mobileNav.contains(e.target) && 
                !mobileNavToggle.contains(e.target)) {
                mobileNav.classList.remove('active');
            }
        });
    }

    // Email Reminder Toggle
    const emailToggle = document.getElementById('emailReminderToggle');
    if (emailToggle) {
        emailToggle.addEventListener('change', function() {
            const isEnabled = this.checked;
            fetch('../includes/update_email_reminder.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'email_reminder=' + (isEnabled ? '1' : '0')
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alert.style.zIndex = '9999';
                    alert.innerHTML = `
                        Email reminders ${isEnabled ? 'enabled' : 'disabled'} successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.body.appendChild(alert);
                    setTimeout(() => alert.remove(), 3000);
                } else {
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alert.style.zIndex = '9999';
                    alert.innerHTML = `
                        Error updating email reminder settings.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.body.appendChild(alert);
                    setTimeout(() => alert.remove(), 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
</script>