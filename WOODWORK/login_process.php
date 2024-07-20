<?php
// Start a session to track the user's login status
session_start();

// Admin credentials
$admin_username = 'Admin';
$admin_password = 'Nopassword';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the entered username and password
    $entered_username = $_POST['username'];
    $entered_password = $_POST['password'];

    // Check if the entered username and password match the admin credentials
    if ($entered_username === $admin_username && $entered_password === $admin_password) {
        // Set session variables to indicate that the user is logged in
        $_SESSION['admin_logged_in'] = true;
        
        // Redirect to the dashboard 
        header("Location: index.html");
        exit;
    } else {
        // If the username or password is incorrect, redirect back to the login page with an error message
        header("Location: login.html?error=1");
        exit;
    }
} else {
    // If the form is not submitted, redirect back to the login page with an error message
    header("Location: login.html?error=2");
    exit;
}
?>
