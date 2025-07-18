<?php

class SignupController extends Controller {

    public function index() {
        $this->view('signup/index');
    }

    public function register() {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $_SESSION['signup_error'] = 'All fields are required.';
            header('Location: /signup');
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['signup_error'] = 'Passwords do not match.';
            header('Location: /signup');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['signup_error'] = 'Password must be at least 6 characters long.';
            header('Location: /signup');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['signup_error'] = 'Please enter a valid email address.';
            header('Location: /signup');
            exit;
        }

        // Check if user already exists
        $user = $this->model('User');
        if ($user->userExists($username, $email)) {
            $_SESSION['signup_error'] = 'Username or email already exists.';
            header('Location: /signup');
            exit;
        }

        // Create user
        if ($user->createUser($username, $email, $password)) {
            $_SESSION['signup_success'] = 'Account created successfully! Please login.';
            header('Location: /login');
            exit;
        } else {
            $_SESSION['signup_error'] = 'Failed to create account. Please try again.';
            header('Location: /signup');
            exit;
        }
    }
} 