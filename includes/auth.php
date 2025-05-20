<?php
require_once 'db_connect.php';
require_once 'functions.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is a job seeker
function isJobSeeker() {
    return isLoggedIn() && $_SESSION['role'] === 'job_seeker';
}

// Check if user is an employer
function isEmployer() {
    return isLoggedIn() && $_SESSION['role'] === 'employer';
}

// Check if user is an admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// Protect job seeker routes
function protectJobSeekerRoute() {
    if (!isLoggedIn() || !isJobSeeker()) {
        header("Location: ../login.php");
        exit();
    }
}

// Protect employer routes
function protectEmployerRoute() {
    if (!isLoggedIn() || !isEmployer()) {
        header("Location: ../login.php");
        exit();
    }
}

// Protect admin routes
function protectAdminRoute() {
    if (!isLoggedIn() || !isAdmin()) {
        header("Location: ../login.php");
        exit();
    }
}

// Register a new user
function registerUser($name, $email, $password, $role, $phone = null, $address = null, $company = null) {
    global $conn;

    // Sanitize inputs
    $name = sanitize($name);
    $email = sanitize($email);
    $role = sanitize($role);
    $phone = $phone ? sanitize($phone) : null;
    $address = $address ? sanitize($address) : null;
    $company = $company ? sanitize($company) : null;

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        return "All required fields must be filled.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }

    if (strlen($password) < 6) {
        return "Password must be at least 6 characters long.";
    }

    if (!in_array($role, ['job_seeker', 'employer'])) {
        return "Invalid role.";
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        return "Email already exists.";
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database (only using existing columns: name, email, password, role)
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) 
                           VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$name, $email, $hashed_password, $role]);

    // Note: If you want to store phone, address, and company, you need to:
    // 1. Add these columns to the users table in your database:
    // ALTER TABLE users ADD COLUMN phone VARCHAR(20), ADD COLUMN address TEXT, ADD COLUMN company VARCHAR(255);
    // 2. Then uncomment the line below and adjust the query above to include these fields:
    // $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone, address, company) VALUES (?, ?, ?, ?, ?, ?, ?)");
    // $result = $stmt->execute([$name, $email, $hashed_password, $role, $phone, $address, $company]);

    if ($result) {
        return true;
    } else {
        return "Registration failed. Please try again.";
    }
}

// Login a user
function loginUser($email, $password) {
    global $conn;

    // Sanitize inputs
    $email = sanitize($email);

    // Validate inputs
    if (empty($email) || empty($password)) {
        return "Email and password are required.";
    }

    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] === 'banned') {
            return "Your account has been banned.";
        }

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'job_seeker') {
            header("Location: job_seeker/dashboard.php");
        } elseif ($user['role'] === 'employer') {
            header("Location: employer/dashboard.php");
        } elseif ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        }
        exit();

        return true;
    } else {
        return "Invalid email or password.";
    }
}

// Logout user
function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}
?>