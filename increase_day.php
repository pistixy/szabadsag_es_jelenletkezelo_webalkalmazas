<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all necessary data is provided
    if (isset($_POST['work_id'], $_POST['status'])) {
        // Extract data from the form
        $work_id = $_POST['work_id'];
        $status = $_POST['status'];

        include "connect.php";
        $stmt = $conn->prepare("UPDATE users SET $status = $status + 1 WHERE work_id = :work_id");
        $stmt->bindParam(':work_id', $work_id);
        $stmt->execute();

        // Redirect back to the page where the form was submitted from
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    } else {
        // Redirect back to the page where the form was submitted from with an error message
        header("Location: {$_SERVER['HTTP_REFERER']}?error=1");
        exit;
    }
} else {
    // If someone tries to access this page directly, redirect them to the homepage
    header("Location: index.php");
    exit;
}
?>
