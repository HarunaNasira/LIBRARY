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
              <form class="pt-3">
                <div class="form-group">
                  <label for="exampleInputEmail">Username</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <img src="./assets/images/icons/user.svg" width="15px">
                      </span>
                    </div>
                    <input type="text" class="form-control form-control-lg border-left-0" id="exampleInputEmail" placeholder="Username">
                  </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                  <label for="exampleInputPassword">Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <img src="./assets/images/icons/lock.svg" width="15px">
                      </span>
                    </div>
                    <input type="password" class="form-control form-control-lg border-left-0" id="exampleInputPassword" placeholder="Password">                        
                  </div>
                </div>

                <!-- Role -->
                <div class="form-group">
                  <label for="exampleInputPassword">Role</label>
                  <div class="input-group">
                    <div class="col-sm-4">
                      <div class="form-radio">
                        <label class="form-check-label d-flex gap-3">
                          <input type="radio" class="form-check-input" name="userRole" id="userRole" value="" checked>
                          <h6>User</h6>
                        </label>
                      </div>
                    </div>   
                    <div class="col-sm-4">
                      <div class="form-radio">
                        <label class="form-check-label d-flex gap-3">
                          <input type="radio" class="form-check-input" name="userRole" id="adminRole" value="">
                          <h6 class="card-title">Admin</h6>
                        </label>
                      </div>
                    </div>            
                  </div>
                </div>

                <div class="my-3">
                  <a class="btn w-100 btn-block btn-primary btn-lg font-weight-medium auth-form-btn" href="./pages/dashboard.php">LOGIN</a>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <a href="#" class="auth-link text-black">Forgot password?</a>
                  <a href="#" class="auth-link text-black">Don't have an Account? Contact Admin</a>
                </div>
                
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
