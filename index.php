<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Nas Library - Login</title>
  <!-- Prebuilt css from Bootstrap-->
  <link rel="stylesheet" href="./assets/css/flag-icon.min.css">
  <link rel="stylesheet" href="./assets/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="./assets/css/font-awesome.min.css">
  <link rel="stylesheet" href="./assets/css/simple-line-icons.css">
  <link rel="stylesheet" href="./assets/css/feather.css">
  <link rel="stylesheet" href="./assets/css/vendor.bundle.base.css">
  
  <!-- Custom SStyles -->
  <link rel="stylesheet" href="./assets/css/main.css">
  <link rel="shortcut icon" href="./assets/images/favicon.svg" />

</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
        <div class="row flex-grow">
          <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="auth-form-transparent text-left p-3">
              <div class="brand-logo">
                <img src="./assets/images/logo_dark.svg" alt="logo">
              </div>
              <h4>Welcome back!</h4>
              <small class="text-muted">Happy to see you again!</small>
              
              <?php
                // Display error
                if (isset($_SESSION['error'])) {
                  echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                  unset($_SESSION['error']);
                }
                
                // Display role change message
                if (isset($_SESSION['role_change_message'])) {
                  echo '<div class="alert alert-info">' . $_SESSION['role_change_message'] . '</div>';
                  unset($_SESSION['role_change_message']);
                }
              ?>
              
              <form class="pt-3" action="./authentication/login.php" method="post">
                <div class="form-group">
                  <label for="username">Username</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <img src="./assets/images/icons/user.svg" width="15px">
                      </span>
                    </div>
                    <input type="text" class="form-control form-control-lg border-left-0" id="username" name="username" placeholder="Username" required>
                  </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                  <label for="password">Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <img src="./assets/images/icons/lock.svg" width="15px">
                      </span>
                    </div>
                    <input type="password" class="form-control form-control-lg border-left-0" id="password" name="password" placeholder="Password" required>                        
                  </div>
                </div>

                <!-- Role -->
                <div class="form-group">
                  <label for="role">Role</label>
                  <div class="input-group">
                    <div class="col-sm-4">
                      <div class="form-radio">
                        <label class="form-check-label d-flex gap-3">
                          <input type="radio" class="form-check-input" name="role" id="userRole" value="user" checked>
                          <h6>User</h6>
                        </label>
                      </div>
                    </div>   
                    <div class="col-sm-4">
                      <div class="form-radio">
                        <label class="form-check-label d-flex gap-3">
                          <input type="radio" class="form-check-input" name="role" id="adminRole" value="admin">
                          <h6 class="card-title">Admin</h6>
                        </label>
                      </div>
                    </div>            
                  </div>
                </div>

                <div class="my-3">
                  <button type="submit" class="btn w-100 btn-block btn-primary btn-lg font-weight-medium auth-form-btn">LOGIN</button>
                </div>
                <!-- <div class="my-2 d-flex justify-content-between align-items-center">
                  <a href="#" class="auth-link text-black">Forgot password?</a>
                  <a href="#" class="auth-link text-black">Don't have an Account? Contact Admin</a>
                </div> -->
                
              </form>
            </div>
          </div>
          <div class="col-lg-6 login-half-bg d-flex flex-row">
            <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2025  All rights reserved.</p>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <script src="./assets/js/template.js"></script>
  <script src="./assets/js/vendor.bundle.base.js"></script>

</body>
</html>
