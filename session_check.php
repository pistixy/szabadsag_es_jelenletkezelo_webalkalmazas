<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeLimit = 60 * 60; // 60 minutes in seconds

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeLimit)) {
session_unset();
session_destroy();

header("Location: session_expired.php");
exit;
}

$_SESSION['last_activity'] = time();

?>
