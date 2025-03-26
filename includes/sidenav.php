
<?php

    // Display options based on user role

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
                Nasira Haruna
            </p>
            <p class="designation">
                Admin
            </p>
            </div>
        </div>
        </li>
        <li class="nav-item"> 
            <a class="nav-link" href="#">
                <i data-feather="grid" class="sidenav-icon"></i>
                <span class="menu-title text-black font-weight-semibold">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#books" aria-expanded="false" aria-controls="page-layouts">
                <i data-feather="archive" class="sidenav-icon"></i>
                <span class="menu-title text-black font-weight-semibold">Manage Books</span>
            </a>
        <div class="collapse" id="books">
            <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="#">Add Books</a></li>
            <li class="nav-item"> <a class="nav-link" href="#">View All</a></li>
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
                    <li class="nav-item"> <a class="nav-link" href="#">Add Users</a></li>
                    <li class="nav-item"> <a class="nav-link" href="#">View All</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item"> 
            <a class="nav-link" href="#">
                <i data-feather="power" class="sidenav-icon"></i>
                <span class="menu-title text-black font-weight-semibold">Logout</span>
            </a>
        </li>
    </ul>
</nav>