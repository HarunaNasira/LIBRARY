<?php
    // Display options based on user role
    $user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
    $is_admin = ($user_role === 'admin');
?>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <div class="nav-link">
                <div class="profile-image">
                <img src="../assets/images/RGU_logo.jpg" class="" alt="profile"/>
                </div>
                <div class="profile-name">
                <p class="name">
                    <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User'; ?>
                </p>
                <p class="designation">
                    <?php echo ucfirst($user_role); ?>
                </p>
                </div>
            </div>
        </li>
        
        <!-- Depending on the role, link to the appropriate dashboard page -->
        <li class="nav-item"> 
            <a class="nav-link" href="dashboard.php">
                <i data-feather="grid" class="sidenav-icon"></i>
                <span class="menu-title text-black font-weight-semibold">Dashboard</span>
            </a>
        </li>

        <?php if ($is_admin): ?>
            <!-- Show to Only Admins -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#books" aria-expanded="false" aria-controls="page-layouts">
                    <i data-feather="archive" class="sidenav-icon"></i>
                    <span class="menu-title text-black font-weight-semibold">Manage Books</span>
                </a>
            <div class="collapse" id="books">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="./booklist.php">Book List</a></li>
                    <li class="nav-item"> <a class="nav-link" href="./addbook.php">Add Book</a></li>
                </ul>
            </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#users" aria-expanded="false" aria-controls="page-layouts">
                    <i data-feather="users" class="sidenav-icon"></i>
                    <span class="menu-title text-black font-weight-semibold">Users</span>
                </a>
                <div class="collapse" id="users">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"> <a class="nav-link" href="add_user.php">Add Users</a></li>
                        <li class="nav-item"> <a class="nav-link" href="view_users.php">View All</a></li>
                    </ul>
                </div>
            </li>
        <!-- End Only Admin Navs -->
        <?php endif; ?>

        <?php if (!$is_admin): ?>
            <!-- Only Show to Users -->
            <li class="nav-item"> 
                <a class="nav-link" href="./search_books.php">
                    <i data-feather="search" class="sidenav-icon"></i>
                    <span class="menu-title text-black font-weight-semibold">Search Books</span>
                </a>
            </li>
            <li class="nav-item"> 
                <a class="nav-link" href="#">
                    <i data-feather="layers" class="sidenav-icon"></i>
                    <span class="menu-title text-black font-weight-semibold">My Borrowed Books</span>
                </a>
            </li>
            <li class="nav-item"> 
                <a class="nav-link" href="#">
                    <i data-feather="archive" class="sidenav-icon"></i>
                    <span class="menu-title text-black font-weight-semibold">Loan History</span>
                </a>
            </li>
            <!-- End Users only nav -->
        <?php endif; ?>

        <li class="nav-item"> 
            <a class="nav-link" href="./profile.php">
                <i data-feather="user" class="sidenav-icon"></i>
                <span class="menu-title text-black font-weight-semibold">Profile</span>
            </a>
        </li>
        <li class="nav-item"> 
            <a class="nav-link" href="../authentication/logout.php">
                <i data-feather="power" class="sidenav-icon"></i>
                <span class="menu-title text-black font-weight-semibold">Logout</span>
            </a>
        </li>
    </ul>
</nav>