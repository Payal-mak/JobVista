<?php
require_once 'includes/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Store user data for logout message
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

// Prepare the logout message before destroying the session
$logout_message = "You have been successfully logged out. Goodbye, $user_name!";

// Destroy all session data
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Restart the session to store the logout message
session_start();

// Set logout message in the new session
$_SESSION['logout_message'] = $logout_message;

// Redirect to login page
header("Location: login.php");
exit();
?>