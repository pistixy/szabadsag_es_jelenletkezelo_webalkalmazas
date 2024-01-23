<?php
session_start();
include "connect.php";

if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $requestId = $_POST['request_id'];
    $userWorkID = $_SESSION['work_id'];

    // Optional: Check if the request to be deleted belongs to the logged-in user
    $checkSql = "SELECT * FROM requests WHERE request_id = :requestId AND work_id = :userWorkID";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
    $checkStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
    $checkStmt->execute();
    $request = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        // The request belongs to the user, proceed with deletion
        $deleteSql = "DELETE FROM requests WHERE request_id = :requestId";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
        $deleteStmt->execute();

        // Redirect back to the requests page or show a success message
        header("Location: my_requests.php"); // Adjust the redirection as needed
        exit;
    } else {
        echo "Unauthorized request or request not found.";
    }
} else {
    echo "Invalid request method or missing request ID.";
}
?>
