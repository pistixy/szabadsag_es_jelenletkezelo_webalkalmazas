<?php
session_start();
include "connect.php";

// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

// Check if a request ID has been passed to initiate a response
if (isset($_POST['request_id']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $fromWorkID = $_SESSION['work_id']; // The responder's work_id
    $requestId = $_POST['request_id'];

    // Fetch the work_id of the user who made the request
    $requestSql = "SELECT work_id FROM requests WHERE request_id = :requestId";
    $requestStmt = $conn->prepare($requestSql);
    $requestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
    $requestStmt->execute();
    $requestDetails = $requestStmt->fetch(PDO::FETCH_ASSOC);

    if ($requestDetails) {
        $toWorkID = $requestDetails['work_id']; // The requester's work_id
        $message = "Elutasítva"; // The rejection message
        $type = 'response to request'; // Fixed type
        $currentTimestamp = date('Y-m-d H:i:s'); // Current timestamp

        // Start a transaction
        $conn->beginTransaction();

        try {
            // Insert the response into the messages table
            $insertSql = "INSERT INTO messages (from_work_id, to_work_id, to_position, type, request_id, message, timestamp) VALUES (:fromWorkID, :toWorkID, '', :type, :requestId, :message, :timestamp)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bindParam(':fromWorkID', $fromWorkID, PDO::PARAM_INT);
            $insertStmt->bindParam(':toWorkID', $toWorkID, PDO::PARAM_INT);
            $insertStmt->bindParam(':type', $type, PDO::PARAM_STR);
            $insertStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
            $insertStmt->bindParam(':message', $message, PDO::PARAM_STR);
            $insertStmt->bindParam(':timestamp', $currentTimestamp, PDO::PARAM_STR);
            $insertStmt->execute();

            // Update the request status to "rejected" in the requests table
            $updateSql = "UPDATE requests SET request_status = 'rejected', modified_date = :modifiedDate WHERE request_id = :requestId";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
            $updateStmt->bindParam(':modifiedDate', $currentTimestamp, PDO::PARAM_STR);
            $updateStmt->execute();

            // Commit the transaction
            $conn->commit();

            // Redirect or display a success message
            echo "The request has been successfully rejected and the response sent.";
            // Redirect to a confirmation page or back to the requests list
        } catch (Exception $e) {
            // An error occurred, roll back the transaction
            $conn->rollBack();
            echo "An error occurred while processing your request: " . $e->getMessage();
        }
    } else {
        echo "Request not found.";
    }
} else {
    echo "No request specified to respond to.";
}
?>
