
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
                Super Admin
            </p>
            </div>
        </div>
        </li>
        <li class="nav-item"> 
        <a class="nav-link" href="dashboard.html">
            <i class="icon-menu menu-icon"></i>
            <span class="menu-title">Dashboard</span>
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#books" aria-expanded="false" aria-controls="page-layouts">
            <i class="icon-check menu-icon"></i>
            <span class="menu-title">Manage Books</span>
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
            <i class="icon-check menu-icon"></i>
            <span class="menu-title">Users</span>
        </a>
        <div class="collapse" id="users">
            <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="#">View All</a></li>
            <li class="nav-item"> <a class="nav-link" href="#">Add Users</a></li>
            </ul>
        </div>
        </li>
    </ul>
</nav>