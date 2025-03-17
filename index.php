<?php require_once('./includes/header.php'); ?>
    

    <section class="w-full vh-100">
          <div class="row d-flex justify-content-center align-items-center vh-100">
            <div class="col-xl-12 p-0">
              <div class="card rounded-3 text-black h-100">
                <div class="row h-100">
                  <div class="col-lg-5">
                    <div class="card-body p-md-5 mx-md-4">
      
                      <div class="text-center">
                        <img src="./assets/images/rgu_logo.jpg"
                          style="width: 85px;" alt="logo">
                        <!-- <h4 class="mt-1 pb-1">Welocme</h4> -->
                        <p class="mt-1 mb-5 pb-1">Please login with your rgu email</p>
                      </div>
      
                      <form action="">
                        <div data-mdb-input-init class="form-outline mb-4">
                          <label class="form-label" for="form2Example11">Username</label>
						  <input type="email" id="form2Example11" class="form-control"
                            placeholder="Phone email address" />
                        </div>
      
						<div data-mdb-input-init class="form-outline mb-4">
							<label class="form-label" for="form2Example22">Password</label>
								<input type="password" id="form2Example22" class="form-control" />
						</div>

						<div data-mdb-input-init class="form-outline mb-4">
							<label class="form-label" for="role">Select role</label>
							<br>
							<div class="">
								<div class="form-check form-check-inline w-25">
									<input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
									<label class="form-check-label" for="inlineRadio1">User</label>
								</div>
								<div class="form-check form-check-inline w-25">
									<input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
									<label class="form-check-label" for="inlineRadio2">Admin</label>
								</div>
							</div>
                        </div>
      
                        <div class="text-center pt-1 mb-5 pb-1">
                          <button data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="button">Log
                            in</button>
                          <a class="text-muted" href="#!">Forgot password?</a>
                        </div>
      
                        <div class="d-flex align-items-center justify-content-center pb-4">
                          <p class="mb-0 mr-1">Don't have an account? </p>
                          <a class="text-primary !underline" href="#!"> Contact Admin</a>
                        </div>
      
                      </form>
      
                    </div>
                  </div>
                  <div class="col-lg-7" 
				  		style="background-image: url('./assets/images/login_bg.svg'); background-position: center; background-repeat: no-repeat; background-size: cover;"></div>
                </div>
              </div>
            </div>
          </div>
    </section>


<?php require_once('./includes/footer.php'); ?>