<?php
include "session_check.php";
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

    // Start a transaction
    $conn->beginTransaction();

    try {
        // Fetch the work_id, calendar_id, and requested_status for this request_id
        $requestSql = "SELECT work_id, calendar_id, requested_status FROM requests WHERE request_id = :requestId";
        $requestStmt = $conn->prepare($requestSql);
        $requestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
        $requestStmt->execute();
        $requestDetails = $requestStmt->fetch(PDO::FETCH_ASSOC);

        if ($requestDetails) {
            $requestingUserID = $requestDetails['work_id']; // The requester's work_id
            $calendarId = $requestDetails['calendar_id']; // The calendar_id associated with the request
            $requestedStatus = $requestDetails['requested_status'];

            // Determine the type of leave request
            switch ($requestedStatus) {
                case 'paid_requested':
                    // Update the request status to "rejected" in the requests table
                    $updateRequestSql = "UPDATE requests SET request_status = 'rejected', modified_date = :modifiedDate WHERE request_id = :requestId";
                    $updateRequestStmt = $conn->prepare($updateRequestSql);
                    $updateRequestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                    $updateRequestStmt->bindParam(':modifiedDate', $currentTimestamp, PDO::PARAM_STR);
                    $updateRequestStmt->execute();

                    // Update the day status in the calendar table back to '1'
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'work_day' WHERE calendar_id = :calendarId";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendarId', $calendarId, PDO::PARAM_INT);
                    $updateCalendarStmt->execute();

                    // Update the user's free and requested counts
                    $updateUserSql = "UPDATE users SET paid_free = paid_free + 1, paid_requested = paid_requested - 1 WHERE work_id = :requestingUserID";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':requestingUserID', $requestingUserID, PDO::PARAM_INT);
                    $updateUserStmt->execute();
                    break;
                
                

                default:
                    // Optional: Handle unknown requested status
                    break;
            }

            // Insert the rejection message into the messages table
            

            // Commit the transaction
            $conn->commit();

            echo "The request has been successfully rejected and the response sent.";
        } else {
            $conn->rollBack();
            echo "Request not found.";
        }
    } catch (Exception $e) {
        $conn->rollBack();
        echo "An error occurred while processing your request: " . $e->getMessage();
    }
} else {
    echo "No request specified to respond to.";
}
?>
