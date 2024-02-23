<?php
include "session_check.php";
include "connect.php";

// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

// Check if the form is submitted and the required data is provided
if (isset($_POST['calendar_id'])) {
    // Retrieve calendar_id from the form submission
    $calendar_id = $_POST['calendar_id'];

    // Update the day_status in the calendar table to "unpayed_uncertified_taken" for the given calendar_id
    $stmt = $conn->prepare("UPDATE calendar SET day_status = 'unpayed_uncertified_taken' WHERE calendar_id = :calendar_id");
    $stmt->bindParam(':calendar_id', $calendar_id);
    $stmt->execute();

    // Redirect back to the previous page
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
} else {
    // If calendar_id is not provided, redirect to an error page or previous page
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
}
?>
