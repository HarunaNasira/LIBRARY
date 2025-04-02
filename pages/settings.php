<?php 
	
	include('../includes/dashboard_header.php'); 
	
?>

	<div class="content-wrapper">

        <!-- Page Title -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>
                Profile Settings
            </h5>
        </div>

        <!-- Settings/ Profile -->
        <div class="row user-profile">
            <div class="col-lg-4 stretch-card side-left">
                <div class="col-12 stretch-card">
                  <div class="card ">
                    <div class="card-body avatar">
                      <h4 class="card-title">Info</h4>
                      <img src="../assets/images/rgu_logo.jpg" alt="">
                      <p class="name">Nasira Haruna</p>
                      <p class="designation">-  Admin  -</p>
                      <a class="d-block text-center text-dark" href="#">n.haruna@rgu.ac.uk</a>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-lg-8 side-right stretch-card">
              <div class="card">
                <div class="card-body">
                  <div class="wrapper d-block d-sm-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Details</h4>
                    <ul class="nav nav-tabs tab-solid tab-solid-primary mb-0" id="myTab" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-expanded="true">Info</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="avatar-tab" data-toggle="tab" href="#avatar" role="tab" aria-controls="avatar">Profile Pic</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="security">Password</a>
                      </li>
                    </ul>
                  </div>
                  <div class="wrapper">
                    <hr>
                    <div class="tab-content" id="myTabContent">
                      <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info">
                        <form action="#">
                          <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Change user name">
                          </div>
                          <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Change email address">
                          </div>
                          <div class="form-group mt-5">
                            <button type="submit" class="btn btn-success mr-2">Update</button>
                            <button class="btn btn-outline-danger">Cancel</button>
                          </div>
                        </form>
                      </div>
                    
                      <!-- Profile Pic -->
                      <div class="tab-pane fade" id="avatar" role="tabpanel" aria-labelledby="avatar-tab">
                        <div class="wrapper mb-5 mt-4">
                          <span class="badge badge-warning text-white">Note : </span>
                          <p class="d-inline ml-3 text-muted">Image size is limited to not greater than 1MB .</p>
                        </div>
                        <form action="#">
                          <input type="file" class="dropify" data-max-file-size="1mb" data-default-file="../assets/images/rgu_logo.jpg"/>
                          <div class="form-group mt-5">
                            <button type="submit" class="btn btn-success mr-2">Update</button>
                            <button class="btn btn-outline-danger">Cancel</button>
                          </div>
                        </form>
                      </div>

                      <!-- Password -->
                      <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                        <form action="#">
                          <div class="form-group">
                            <label for="change-password">Change password</label>
                            <input type="password" class="form-control" id="change-password" placeholder="Enter you current password">
                          </div>
                          <div class="form-group">
                            <input type="password" class="form-control" id="new-password" placeholder="Enter you new password">
                          </div>
                          <div class="form-group mt-5">
                            <button type="submit" class="btn btn-success mr-2">Update</button>
                            <button class="btn btn-outline-danger">Cancel</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

    </div>
        
<?php include_once('../includes/dashboard_footer.php'); ?>
