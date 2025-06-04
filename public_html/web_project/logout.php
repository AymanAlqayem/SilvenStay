<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['is_registered']) && $_SESSION['is_registered'] === true) {
    // Set success message
    $_SESSION['message'] = "Logout successful";
    $_SESSION['message_type'] = "success";
    // Clear all session data
    $_SESSION = [];
    // Destroy the session
    session_destroy();
    // Redirect to main.php
    header("Location: main.php");
    exit;
} else {
    // If not logged in, set a message
    $_SESSION['message'] = "You are not logged in.";
    $_SESSION['message_type'] = "error";
    // Redirect to main.php
    header("Location: main.php");
    exit;
}
?>