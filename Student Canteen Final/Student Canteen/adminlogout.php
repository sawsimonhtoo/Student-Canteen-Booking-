<?php
session_start();

// Logout if ?action=logout is set
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Unset all admin session variables
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_username']);
    
    // Destroy the session completely
    session_destroy();
    
    // Redirect to login page
    header("Location: adminlogin.php");
    exit();
}
?>
