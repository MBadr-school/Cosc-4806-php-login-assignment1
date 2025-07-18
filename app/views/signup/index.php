<?php require_once 'app/views/templates/headerPublic.php'?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="row w-100">
        <div class="col-md-6 col-lg-5 col-xl-4 mx-auto">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-success text-white text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-person-plus-fill display-4"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Create Account</h3>
                    <p class="mb-0 opacity-75">Join our movie community</p>
                </div>

                <div class="card-body px-5 py-4">
                    <?php if (isset($_SESSION['signup_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Error!</strong> <?php echo $_SESSION['signup_error']; unset($_SESSION['signup_error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="/signup/register" method="post" id="signupForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="bi bi-person me-2"></i>Username
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="username" 
                                   name="username" 
                                   placeholder="Choose a username" 
                                   required
                                   minlength="3"
                                   maxlength="20">
                            <div class="form-text">3-20 characters, letters and numbers only</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Enter your email" 
                                   required>
                            <div class="form-text">We'll never share your email with anyone</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock me-2"></i>Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Create a password" 
                                       required
                                       minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <div class="form-text">At least 6 characters</div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">
                                <i class="bi bi-lock-fill me-2"></i>Confirm Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       placeholder="Confirm your password" 
                                       required
                                       minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="bi bi-eye" id="toggleConfirmIcon"></i>
                                </button>
                            </div>
                            <div class="form-text" id="passwordMatch"></div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="bi bi-person-check me-2"></i>Create Account
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-center py-3 bg-light">
                    <small class="text-muted">Already have an account?</small>
                    <a href="/login" class="text-decoration-none fw-bold">Sign In</a>
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
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-bottom: none;
    }

    .form-control-lg {
        border-radius: 10px;
        padding: 12px 16px;
        border: 2px solid #e9ecef;
        transition: border-color 0.3s ease;
    }

    .form-control-lg:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .btn-lg {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .alert {
        border-radius: 10px;
        border: none;
    }

    .card-footer {
        background-color: #f8f9fa !important;
        border-top: 1px solid #e9ecef;
    }

    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .password-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 5px;
        transition: all 0.3s ease;
    }

    .strength-weak { background-color: #dc3545; }
    .strength-medium { background-color: #ffc107; }
    .strength-strong { background-color: #28a745; }

    @media (max-width: 768px) {
        .card {
            margin: 20px;
        }

        .container-fluid {
            padding: 20px;
        }
    }
</style>

<!-- JavaScript for form validation and password toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signupForm');
    const username = document.getElementById('username');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submitBtn');
    const passwordMatch = document.getElementById('passwordMatch');

    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const toggleIcon = document.getElementById('toggleIcon');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');

    togglePassword.addEventListener('click', function() {
        const type = password.type === 'password' ? 'text' : 'password';
        password.type = type;

        if (type === 'text') {
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
    });

    toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPassword.type === 'password' ? 'text' : 'password';
        confirmPassword.type = type;

        if (type === 'text') {
            toggleConfirmIcon.classList.remove('bi-eye');
            toggleConfirmIcon.classList.add('bi-eye-slash');
        } else {
            toggleConfirmIcon.classList.remove('bi-eye-slash');
            toggleConfirmIcon.classList.add('bi-eye');
        }
    });

    // Username validation
    username.addEventListener('input', function() {
        const value = this.value;
        const isValid = /^[a-zA-Z0-9_]{3,20}$/.test(value);

        if (value.length > 0) {
            if (isValid) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });

    // Email validation
    email.addEventListener('input', function() {
        const value = this.value;
        const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

        if (value.length > 0) {
            if (isValid) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });

    // Password matching validation
    function checkPasswordMatch() {
        const pass = password.value;
        const confirmPass = confirmPassword.value;

        if (confirmPass.length > 0) {
            if (pass === confirmPass) {
                confirmPassword.classList.remove('is-invalid');
                confirmPassword.classList.add('is-valid');
                passwordMatch.textContent = '✓ Passwords match';
                passwordMatch.className = 'form-text text-success';
                return true;
            } else {
                confirmPassword.classList.remove('is-valid');
                confirmPassword.classList.add('is-invalid');
                passwordMatch.textContent = '✗ Passwords do not match';
                passwordMatch.className = 'form-text text-danger';
                return false;
            }
        } else {
            confirmPassword.classList.remove('is-valid', 'is-invalid');
            passwordMatch.textContent = '';
            return false;
        }
    }

    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);

    // Form submission validation
    form.addEventListener('submit', function(e) {
        const isUsernameValid = /^[a-zA-Z0-9_]{3,20}$/.test(username.value);
        const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value);
        const isPasswordValid = password.value.length >= 6;
        const isPasswordMatch = password.value === confirmPassword.value;

        if (!isUsernameValid || !isEmailValid || !isPasswordValid || !isPasswordMatch) {
            e.preventDefault();
            alert('Please fix the errors before submitting.');
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