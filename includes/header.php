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
  
  <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>
  <div class="container-scroller">
    <!-- partial:../../partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
        <a class="navbar-brand brand-logo text-black" href="#">LMS</a>
        <a class="navbar-brand brand-logo-mini" href="#"><img src="#" alt="logo"/></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button> 
        <ul class="navbar-nav w-100 d-flex justify-content-between">
          <li class="nav-item dropdown d-none d-lg-flex">
            <a class="nav-link dropdown-toggle nav-btn" id="actionDropdown" href="#">
              <span class="btn">+ Add New Book</span>
            </a>
          </li>
		  <li class="nav-item">
			<div class="form-check form-switch d-flex align-items-center gap-5">
				<label class="form-check-label" for="flexSwitchCheckDefault"> Set Email Reminder!</label>
				<input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" />
			</div>
          </li>
		  
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      
      
      <!-- SideNav -->
      <?php include_once('../includes/sidenav.php'); ?>
      
	  <!-- main-panel start -->
      <div class="main-panel">