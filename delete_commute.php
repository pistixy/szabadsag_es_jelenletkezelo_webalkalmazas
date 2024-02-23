<?php
include "session_check.php";
include "connect.php";

// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

// Check if commute_id is provided
if (!isset($_POST['commute_id'])) {
    // Redirect or handle error accordingly
    header("Location: commutes.php");
    exit;
}

// Retrieve commute_id from POST data
$commute_id = $_POST['commute_id'];

try {
    // Prepare SQL statement to delete the entry from the commute table
    $sql = "DELETE FROM commute WHERE commute_id = :commute_id AND work_id = :work_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':commute_id', $commute_id, PDO::PARAM_INT);
    $stmt->bindParam(':work_id', $_SESSION['work_id'], PDO::PARAM_INT);

    // Execute the statement
    $stmt->execute();

    // Redirect back to the page showing commutes after deletion
    header("Location: commutes.php");
    exit;
} catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
}
?>

