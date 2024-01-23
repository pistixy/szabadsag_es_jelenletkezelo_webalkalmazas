<?php
session_start();
include "connect.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestedStatus = $_POST['nap'];
    $message = $_POST['message'];
    $date = $_POST['date'];
    $userWorkID = $_SESSION['work_id'];
    $toWhom = "admin"; // This should be dynamically determined based on your application's logic
    $currentTimestamp = date('Y-m-d H:i:s'); // Get the current timestamp

    // Begin transaction
    $conn->beginTransaction();

    try {
        // Fetch the calendar_id for the given date and work_id
        $sql = "SELECT calendar_id FROM calendar WHERE date = :date AND work_id = :userWorkID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
        $stmt->execute();
        $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($calendarResult) {
            $calendarID = $calendarResult['calendar_id'];

            // Insert the new request into the database with timestamp and modified_date
            $insertSql = "INSERT INTO requests (work_id, calendar_id, requested_status, message, to_whom, request_status, timestamp, modified_date) VALUES (:work_id, :calendar_id, :requested_status, :message, :to_whom, 'pending', :timestamp, NULL)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bindParam(':work_id', $userWorkID);
            $insertStmt->bindParam(':calendar_id', $calendarID);
            $insertStmt->bindParam(':requested_status', $requestedStatus);
            $insertStmt->bindParam(':message', $message);
            $insertStmt->bindParam(':to_whom', $toWhom);
            $insertStmt->bindParam(':timestamp', $currentTimestamp);
            $insertStmt->execute();

            // Update the user's free and requested counts
            $updateUserSql = "UPDATE users SET free = free - 1, requested = requested + 1 WHERE work_id = :work_id";
            $updateUserStmt = $conn->prepare($updateUserSql);
            $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
            $updateUserStmt->execute();

            // Commit the transaction
            $conn->commit();

            echo "Sikeres kérelmezés.";
        } else {
            // Rollback if no calendar entry found
            $conn->rollBack();
            echo "No calendar entry found for the specified date and user.";
        }
    } catch (Exception $e) {
        // Rollback on any other exception
        $conn->rollBack();
        echo "An error occurred: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>
