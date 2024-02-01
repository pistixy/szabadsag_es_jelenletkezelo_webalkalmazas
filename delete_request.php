<?php
include "session_check.php";
include "connect.php";

if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

if (isset($_POST['request_id'])) {
    $requestId = $_POST['request_id'];

    // Fetch the requested_status from the request
    $statusSql = "SELECT requested_status, calendar_id, work_id FROM requests WHERE request_id = :requestId";
    $statusStmt = $conn->prepare($statusSql);
    $statusStmt->bindParam(':requestId', $requestId);
    $statusStmt->execute();
    $request = $statusStmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        $requested_status = $request['requested_status'];

        try {
            // Start transaction
            $conn->beginTransaction();

            switch ($requested_status) {
                case 'payed_requested':
                    // Update the calendar day_status back to 'work_day'
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'work_day' WHERE calendar_id = :calendarId";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendarId', $request['calendar_id']);
                    $updateCalendarStmt->execute();

                    // Mark the request as deleted
                    $updateRequestSql = "UPDATE requests SET request_status = 'deleted' WHERE request_id = :requestId";
                    $updateRequestStmt = $conn->prepare($updateRequestSql);
                    $updateRequestStmt->bindParam(':requestId', $requestId);
                    $updateRequestStmt->execute();

                    // Update the user's payed_requested and payed_free counts
                    $updateUserSql = "UPDATE users SET payed_requested = payed_requested - 1, payed_free = payed_free + 1 WHERE work_id = :workId";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':workId', $request['work_id']);
                    $updateUserStmt->execute();

                    echo "payed_requested request successfully deleted.";
                    break;
                case 'payed_past_requested':
                    // Update the calendar day_status back to 'work_day'
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'work_day' WHERE calendar_id = :calendarId";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendarId', $request['calendar_id']);
                    $updateCalendarStmt->execute();

                    // Mark the request as deleted
                    $updateRequestSql = "UPDATE requests SET request_status = 'deleted' WHERE request_id = :requestId";
                    $updateRequestStmt = $conn->prepare($updateRequestSql);
                    $updateRequestStmt->bindParam(':requestId', $requestId);
                    $updateRequestStmt->execute();

                    // Update the user's payed_requested and payed_free counts
                    $updateUserSql = "UPDATE users SET payed_past_requested = payed_past_requested - 1, payed_past_free = payed_past_free + 1 WHERE work_id = :workId";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':workId', $request['work_id']);
                    $updateUserStmt->execute();

                    echo "payed_past_requested request successfully deleted.";
                    break;
                case 'payed_award_requested':
                    // Update the calendar day_status back to 'work_day'
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'work_day' WHERE calendar_id = :calendarId";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendarId', $request['calendar_id']);
                    $updateCalendarStmt->execute();

                    // Mark the request as deleted
                    $updateRequestSql = "UPDATE requests SET request_status = 'deleted' WHERE request_id = :requestId";
                    $updateRequestStmt = $conn->prepare($updateRequestSql);
                    $updateRequestStmt->bindParam(':requestId', $requestId);
                    $updateRequestStmt->execute();

                    // Update the user's payed_requested and payed_free counts
                    $updateUserSql = "UPDATE users SET payed_award_requested = payed_award_requested - 1, payed_award_free = payed_award_free + 1 WHERE work_id = :workId";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':workId', $request['work_id']);
                    $updateUserStmt->execute();

                    echo "payed_award_requested request successfully deleted.";
                    break;
                case 'payed_edu_requested':
                    // Update the calendar day_status back to 'work_day'
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'work_day' WHERE calendar_id = :calendarId";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendarId', $request['calendar_id']);
                    $updateCalendarStmt->execute();

                    // Mark the request as deleted
                    $updateRequestSql = "UPDATE requests SET request_status = 'deleted' WHERE request_id = :requestId";
                    $updateRequestStmt = $conn->prepare($updateRequestSql);
                    $updateRequestStmt->bindParam(':requestId', $requestId);
                    $updateRequestStmt->execute();

                    // Update the user's payed_requested and payed_free counts
                    $updateUserSql = "UPDATE users SET payed_edu_requested = payed_edu_requested - 1, payed_edu_free = payed_edu_free + 1 WHERE work_id = :workId";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':workId', $request['work_id']);
                    $updateUserStmt->execute();

                    echo "payed_edu_requested request successfully deleted.";
                    break;
                case 'unpayed_home_requested':
                    // Update the calendar day_status back to 'work_day'
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'work_day' WHERE calendar_id = :calendarId";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendarId', $request['calendar_id']);
                    $updateCalendarStmt->execute();

                    // Mark the request as deleted
                    $updateRequestSql = "UPDATE requests SET request_status = 'deleted' WHERE request_id = :requestId";
                    $updateRequestStmt = $conn->prepare($updateRequestSql);
                    $updateRequestStmt->bindParam(':requestId', $requestId);
                    $updateRequestStmt->execute();

                    // Update the user's payed_requested and payed_free counts
                    $updateUserSql = "UPDATE users SET unpayed_home_requested = unpayed_home_requested - 1, unpayed_home_free = unpayed_home_free + 1 WHERE work_id = :workId";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':workId', $request['work_id']);
                    $updateUserStmt->execute();

                    echo "unpayed_home_requested request successfully deleted.";

                    break;
                case 'unpayed_dad_requested':
                    // Update the calendar day_status back to 'work_day'
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'work_day' WHERE calendar_id = :calendarId";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendarId', $request['calendar_id']);
                    $updateCalendarStmt->execute();

                    // Mark the request as deleted
                    $updateRequestSql = "UPDATE requests SET request_status = 'deleted' WHERE request_id = :requestId";
                    $updateRequestStmt = $conn->prepare($updateRequestSql);
                    $updateRequestStmt->bindParam(':requestId', $requestId);
                    $updateRequestStmt->execute();

                    // Update the user's payed_requested and payed_free counts
                    $updateUserSql = "UPDATE users SET unpayed_dad_requested = unpayed_dad_requested - 1, unpayed_dad_free = unpayed_dad_free + 1 WHERE work_id = :workId";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':workId', $request['work_id']);
                    $updateUserStmt->execute();

                    echo "unpayed_dad_requested request successfully deleted.";

                    break;
                case 'unpayed_requested':
                    // Update the calendar day_status back to 'work_day'
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'work_day' WHERE calendar_id = :calendarId";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendarId', $request['calendar_id']);
                    $updateCalendarStmt->execute();

                    // Mark the request as deleted
                    $updateRequestSql = "UPDATE requests SET request_status = 'deleted' WHERE request_id = :requestId";
                    $updateRequestStmt = $conn->prepare($updateRequestSql);
                    $updateRequestStmt->bindParam(':requestId', $requestId);
                    $updateRequestStmt->execute();

                    // Update the user's payed_requested and payed_free counts
                    $updateUserSql = "UPDATE users SET unpayed_requested = unpayed_requested - 1, unpayed_free = unpayed_free + 1 WHERE work_id = :workId";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':workId', $request['work_id']);
                    $updateUserStmt->execute();

                    echo "unpayed_dad_requested request successfully deleted.";

                    break;
                default:
                    echo "Unrecognized request status.";
                    break;
            }

            // Commit transaction
            $conn->commit();
        } catch (PDOException $e) {
            // Rollback on any error
            $conn->rollBack();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Request not found.";
    }
} else {
    echo "No request specified.";
}
