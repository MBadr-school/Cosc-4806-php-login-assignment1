<?php require_once 'app/views/templates/headerPublic.php'?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light">
		<div class="row w-100">
				<div class="col-md-6 col-lg-5 col-xl-4 mx-auto">
						<div class="card shadow-lg border-0 rounded-lg">
								<div class="card-header bg-primary text-white text-center py-4">
										<div class="mb-3">
												<i class="bi bi-person-circle display-4"></i>
										</div>
										<h3 class="fw-bold mb-0">Welcome Back!</h3>
										<p class="mb-0 opacity-75">Sign in to your account</p>
								</div>

								<div class="card-body px-5 py-4">
										<?php if (isset($_SESSION['signup_success'])): ?>
												<div class="alert alert-success alert-dismissible fade show" role="alert">
														<i class="bi bi-check-circle me-2"></i>
														<strong>Success!</strong> <?php echo $_SESSION['signup_success']; unset($_SESSION['signup_success']); ?>
														<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
												</div>
										<?php endif; ?>

										<?php if (isset($_SESSION['failedAuth']) && $_SESSION['failedAuth'] > 0): ?>
												<div class="alert alert-danger alert-dismissible fade show" role="alert">
														<i class="bi bi-exclamation-triangle me-2"></i>
														<strong>Login Failed!</strong> Invalid username or password.
														<?php if ($_SESSION['failedAuth'] >= 3): ?>
																<br><small>Too many failed attempts. Please try again later.</small>
														<?php endif; ?>
														<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
												</div>
										<?php endif; ?>

										<form action="/login/verify" method="post" id="loginForm">
												<div class="mb-3">
														<label for="username" class="form-label">
																<i class="bi bi-person me-2"></i>Username
														</label>
														<input type="text" 
																	 class="form-control form-control-lg" 
																	 id="username" 
																	 name="username" 
																	 placeholder="Enter your username" 
																	 required>
												</div>

												<div class="mb-4">
														<label for="password" class="form-label">
																<i class="bi bi-lock me-2"></i>Password
														</label>
														<div class="input-group">
																<input type="password" 
																			 class="form-control form-control-lg" 
																			 id="password" 
																			 name="password" 
																			 placeholder="Enter your password" 
																			 required>
																<button class="btn btn-outline-secondary" type="button" id="togglePassword">
																		<i class="bi bi-eye" id="toggleIcon"></i>
																</button>
														</div>
												</div>

												<div class="d-grid mb-3">
														<button type="submit" class="btn btn-primary btn-lg">
																<i class="bi bi-box-arrow-in-right me-2"></i>Sign In
														</button>
												</div>
										</form>
								</div>

								<div class="card-footer text-center py-3 bg-light">
										<small class="text-muted">Don't have an account?</small>
										<a href="/signup" class="text-decoration-none fw-bold">Create Account</a>
								</div>
						</div>

						<div class="text-center mt-3">
								<a href="/movie" class="text-decoration-none text-muted">
										<i class="bi bi-arrow-left me-2"></i>Back to Movie Search
								</a>
						</div>
				</div>
		</div>
</div>

<!-- Custom CSS -->
<style>
		.min-vh-100 {
				min-height: 100vh;
		}

		.card {
				border-radius: 15px;
				overflow: hidden;
		}

		.card-header {
				background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
				border-bottom: none;
		}

		.form-control-lg {
				border-radius: 10px;
				padding: 12px 16px;
				border: 2px solid #e9ecef;
				transition: border-color 0.3s ease;
		}

		.form-control-lg:focus {
				border-color: #4a90e2;
				box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
		}

		.btn-lg {
				padding: 12px 24px;
				border-radius: 10px;
				font-weight: 600;
				transition: all 0.3s ease;
		}

		.btn-primary:hover {
				background: linear-gradient(135deg, #357abd 0%, #2a5d8a 100%);
				transform: translateY(-2px);
				box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
		}

		.alert {
				border-radius: 10px;
				border: none;
		}

		.card-footer {
				background-color: #f8f9fa !important;
				border-top: 1px solid #e9ecef;
		}

		@media (max-width: 768px) {
				.card {
						margin: 20px;
				}

				.container-fluid {
						padding: 20px;
				}
		}
</style>

<!-- JavaScript for password toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
		const togglePassword = document.getElementById('togglePassword');
		const passwordInput = document.getElementById('password');
		const toggleIcon = document.getElementById('toggleIcon');

		togglePassword.addEventListener('click', function() {
				const type = passwordInput.type === 'password' ? 'text' : 'password';
				passwordInput.type = type;

				if (type === 'text') {
						toggleIcon.classList.remove('bi-eye');
						toggleIcon.classList.add('bi-eye-slash');
				} else {
						toggleIcon.classList.remove('bi-eye-slash');
						toggleIcon.classList.add('bi-eye');
				}
		});

		// Auto-dismiss alerts after 5 seconds
		setTimeout(function() {
				const alerts = document.querySelectorAll('.alert');
				alerts.forEach(function(alert) {
						const bsAlert = new bootstrap.Alert(alert);
						bsAlert.close();
				});
		}, 5000);
});
</script>

<?php require_once 'app/views/templates/footer.php' ?>
