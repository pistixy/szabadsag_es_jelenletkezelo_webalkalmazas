<?php
include "session_check.php";
include "connect.php";

if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $requestId = $_POST['request_id'];
    $userWorkID = $_SESSION['work_id'];

    // Start transaction
    $conn->beginTransaction();

    try {
        // Check if the request to be deleted belongs to the logged-in user
        $checkSql = "SELECT calendar_id FROM requests WHERE request_id = :requestId AND work_id = :userWorkID";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
        $checkStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
        $checkStmt->execute();
        $request = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            $calendarId = $request['calendar_id'];

            // Update the user's free and requested counts
            $updateUserSql = "UPDATE users SET free = free + 1, requested = requested - 1 WHERE work_id = :userWorkID";
            $updateUserStmt = $conn->prepare($updateUserSql);
            $updateUserStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
            $updateUserStmt->execute();

            // Update the calendar entry's day status back to '1'
            $updateCalendarSql = "UPDATE calendar SET day_status = 1 WHERE calendar_id = :calendarId";
            $updateCalendarStmt = $conn->prepare($updateCalendarSql);
            $updateCalendarStmt->bindParam(':calendarId', $calendarId, PDO::PARAM_INT);
            $updateCalendarStmt->execute();

            // Mark the request as 'deleted' in the requests table
            $deleteSql = "UPDATE requests SET request_status = 'deleted' WHERE request_id = :requestId";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
            $deleteStmt->execute();

            // Commit the transaction
            $conn->commit();

            // Redirect back to the requests page or show a success message
            header("Location: my_requests.php");
            exit;
        } else {
            echo "Unauthorized request or request not found.";
        }
    } catch (Exception $e) {
        // An error occurred; roll back the transaction
        $conn->rollBack();
        echo "An error occurred: " . $e->getMessage();
    }
} else {
    echo "Invalid request method or missing request ID.";
}
?>
