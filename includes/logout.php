<?php
session_start(); // Start the session

// Check if the user is logged in
if (isset($_SESSION['logged'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the homepage or any other suitable page
    header("Location: index.php"); // You can change "index.php" to the desired page
    exit;
} else {
    // If the user is not logged in, you can handle this case (e.g., redirect to login page)
    header("Location: includes/login_form.php"); // Redirect to the login page
    exit;
}
?>
