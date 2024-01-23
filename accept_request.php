<?php
session_start();
include "connect.php";

if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $requestId = $_POST['request_id'];

    // Start transaction
    $conn->beginTransaction();

    try {
        // Fetch the requested_status and calendar_id for this request_id from the requests table
        $requestSql = "SELECT work_id, requested_status, calendar_id FROM requests WHERE request_id = :requestId";
        $requestStmt = $conn->prepare($requestSql);
        $requestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
        $requestStmt->execute();
        $requestData = $requestStmt->fetch(PDO::FETCH_ASSOC);

        if ($requestData && $requestData['requested_status'] == 'Fizetett Szabadnap') {
            // Decrement the requested count and increment the planned count for the user
            $userUpdateSql = "UPDATE users SET requested = requested - 1, planned = planned + 1 WHERE work_id = :workId";
            $userUpdateStmt = $conn->prepare($userUpdateSql);
            $userUpdateStmt->bindParam(':workId', $requestData['work_id'], PDO::PARAM_INT);
            $userUpdateStmt->execute();

            // Update the request_status to "accepted" in the requests table
            $acceptRequestSql = "UPDATE requests SET request_status = 'accepted' WHERE request_id = :requestId";
            $acceptRequestStmt = $conn->prepare($acceptRequestSql);
            $acceptRequestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
            $acceptRequestStmt->execute();

            // Update the day_status in the calendar table
            $calendarUpdateSql = "UPDATE calendar SET day_status = 2 WHERE calendar_id = :calendarId";
            $calendarUpdateStmt = $conn->prepare($calendarUpdateSql);
            $calendarUpdateStmt->bindParam(':calendarId', $requestData['calendar_id'], PDO::PARAM_INT);
            $calendarUpdateStmt->execute();
        }

        // Commit the transaction
        $conn->commit();
        echo "The request has been successfully accepted.";
    } catch (Exception $e) {
        // An error occurred; roll back the transaction
        $conn->rollBack();
        echo "An error occurred while processing the acceptance: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}

// Redirect or inform the user
// header('Location: incoming_requests.php');
// exit;
?>
