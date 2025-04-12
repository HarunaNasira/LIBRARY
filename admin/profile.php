<?php 
	session_start();
	require_once '../config/db_connect.php';
	
	// Check if user is logged in
	if (!isset($_SESSION['user_id'])) {
		$_SESSION['error'] = "You must be logged in to access this page";
		redirect("../index.php");
	}
	
	// Get user data
	$user_id = $_SESSION['user_id'];
	$user_query = "SELECT * FROM users WHERE user_id = $user_id";
	$user_result = $conn->query($user_query);
	$user = $user_result->fetch_assoc();
	
	// Process form submissions
	$success_message = '';
	$error_message = '';
	
	// Process profile info update
	if (isset($_POST['update_info'])) {
		$name = htmlspecialchars($_POST['name']);
		$email = htmlspecialchars($_POST['email']);
		
		// Check if email is already taken by another user
		$email_check = "SELECT user_id FROM users WHERE email = '$email' AND user_id != $user_id";
		$email_result = $conn->query($email_check);
		
		if ($email_result->num_rows > 0) {
			$error_message = "Email address is already in use by another account.";
		} else {
			$update_query = "UPDATE users SET full_name = '$name', email = '$email' WHERE user_id = $user_id";
			if ($conn->query($update_query)) {
				$success_message = "Profile information updated successfully.";
				// Refresh user data
				$user_result = $conn->query($user_query);
				$user = $user_result->fetch_assoc();
			} else {
				$error_message = "Error updating profile: " . $conn->error;
			}
		}
	}
	
	// Process password update
	if (isset($_POST['update_password'])) {
		$current_password = $_POST['current_password'];
		$new_password = $_POST['new_password'];
		$confirm_password = $_POST['confirm_password'];
		
		// Verify current password
		if (password_verify($current_password, $user['password'])) {
			if ($new_password === $confirm_password) {
				if (strlen($new_password) >= 8) {
					$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
					$update_query = "UPDATE users SET password = '$hashed_password' WHERE user_id = $user_id";
					
					if ($conn->query($update_query)) {
						$success_message = "Password updated successfully.";
					} else {
						$error_message = "Error updating password: " . $conn->error;
					}
				} else {
					$error_message = "New password must be at least 8 characters long.";
				}
			} else {
				$error_message = "New passwords do not match.";
			}
		} else {
			$error_message = "Current password is incorrect.";
		}
	}
	
	// Process profile picture update
	if (isset($_POST['update_avatar']) && isset($_FILES['profile_pic'])) {
		$file = $_FILES['profile_pic'];
		$file_name = $file['name'];
		$file_tmp = $file['tmp_name'];
		$file_size = $file['size'];
		$file_error = $file['error'];
		
		$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		$allowed = array('jpg', 'jpeg', 'png');
		
		if (in_array($file_ext, $allowed)) {
			if ($file_error === 0) {
				if ($file_size < 1048576) { // 1MB max
					$file_name_new = "profile_" . $user_id . "." . $file_ext;
					$file_destination = "../assets/images/profiles/" . $file_name_new;
					
					// Create directory if it doesn't exist
					if (!file_exists("../assets/images/profiles/")) {
						mkdir("../assets/images/profiles/", 0777, true);
					}
					
					if (move_uploaded_file($file_tmp, $file_destination)) {
						$update_query = "UPDATE users SET profile_pic = '$file_name_new' WHERE user_id = $user_id";
						
						if ($conn->query($update_query)) {
							$success_message = "Profile picture updated successfully.";
							// Refresh user data
							$user_result = $conn->query($user_query);
							$user = $user_result->fetch_assoc();
						} else {
							$error_message = "Error updating profile picture: " . $conn->error;
						}
					} else {
						$error_message = "Error uploading file.";
					}
				} else {
					$error_message = "File size must be less than 1MB.";
				}
			} else {
				$error_message = "Error uploading file.";
			}
		} else {
			$error_message = "Only JPG, JPEG, and PNG files are allowed.";
		}
	}
	
	include('../includes/dashboard_header.php'); 
?>

	<div class="content-wrapper">

        <!-- Page Title -->
        <div class="my-3 d-flex justify-content-between align-items-center">
            <h5>
                Profile Settings
            </h5>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Settings/ Profile -->
        <div class="row user-profile">
            <div class="col-lg-4 stretch-card side-left">
                <div class="col-12 stretch-card">
                  <div class="card ">
                    <div class="card-body avatar">
                      <h4 class="card-title">Info</h4>
                      <?php if (!empty($user['profile_pic'])): ?>
                          <img src="../assets/images/profiles/<?php echo $user['profile_pic']; ?>" alt="Profile Picture">
                      <?php else: ?>
                          <img src="../assets/images/rgu_logo.jpg" alt="Default Profile">
                      <?php endif; ?>
                      <p class="name"><?php echo $user['full_name']; ?></p>
                      <p class="designation">- <?php echo ucfirst($user['role']); ?> -</p>
                      <a class="d-block text-center text-dark" href="#"><?php echo $user['email']; ?></a>
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
                        <a class="nav-link" id="avatar-tab" data-toggle="tab" href="#avatar" role="tab" aria-controls="avatar">Profile Picture</a>
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
                        <form action="" method="POST">
                          <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Change user name" value="<?php echo $user['full_name']; ?>" required>
                          </div>
                          <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Change email address" value="<?php echo $user['email']; ?>" required>
                          </div>
                          <div class="form-group mt-5">
                            <button type="submit" name="update_info" class="btn btn-success mr-2">Update</button>
                            <button type="reset" class="btn btn-outline-danger">Cancel</button>
                          </div>
                        </form>
                      </div>
                    
                      <!-- Profile Pic -->
                      <div class="tab-pane fade" id="avatar" role="tabpanel" aria-labelledby="avatar-tab">
                        <form action="" method="POST" enctype="multipart/form-data">
                          <div class="form-group">
                            <label for="profile_pic">Profile Picture</label>
                            <input type="file" class="form-control-file" id="profile_pic" name="profile_pic" accept="image/jpeg,image/png" required>
                          </div>
                          <div class="form-group mt-5">
                            <button type="submit" name="update_avatar" class="btn btn-success mr-2">Update</button>
                            <button type="reset" class="btn btn-outline-danger">Cancel</button>
                          </div>
                        </form>
                      </div>

                      <!-- Password -->
                      <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                        <form action="" method="POST">

							<div class="form-group">
								<label for="current_password">Current Password</label>
								<div class="input-group">
									<input type="password" class="form-control form-control-lg border-left-0" id="current_password" name="current_password" placeholder="Enter your current password" required>                        
									<div toggle="#current_password" class="input-group-prepend bg-transparent toggle-password">
										<span class="input-group-text bg-transparent border-right-0">
										<i class="mdi mdi-eye-off text-primary"></i>
										</span>
									</div>
								</div>
							</div>
							
							<div class="row mt-2">
								<div class="col">
									<div class="form-group">
										<label for="new_password">New Password</label>
										<div class="input-group">
											<input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter your new password" required>                        
											<div toggle="#new_password" class="input-group-prepend bg-transparent toggle-password">
												<span class="input-group-text bg-transparent border-right-0">
												<i class="mdi mdi-eye-off text-primary"></i>
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col">
									<div class="form-group">
										<label for="confirm_password">Confirm New  Password</label>
										<div class="input-group">
											<input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>                        
											<div toggle="#confirm_password" class="input-group-prepend bg-transparent toggle-password">
												<span class="input-group-text bg-transparent border-right-0">
												<i class="mdi mdi-eye-off text-primary"></i>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="form-group mt-2">
								<button type="submit" name="update_password" class="btn btn-success mr-2">Update</button>
								<button type="reset" class="btn btn-outline-danger">Reset</button>
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

<script>
	// View/Hide password
	document.addEventListener('DOMContentLoaded', function() {
		const toggleButtons = document.querySelectorAll('.toggle-password');
		
		toggleButtons.forEach(button => {
			button.addEventListener('click', function() {
				const targetId = this.getAttribute('toggle');
				const input = document.querySelector(targetId);
				
				const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
				input.setAttribute('type', type);
				
				const icon = this.querySelector('i');
				icon.classList.toggle('mdi-eye');
				icon.classList.toggle('mdi-eye-off');
			});
		});
	});
</script>
