<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['is_registered']) || $_SESSION['is_registered'] !== true) {

    // Store the requested URL to return after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

    // Redirect to login
    header('Location: login.php');
    exit;
}
