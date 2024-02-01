<?php
include "session_check.php";
include "connect.php";
include "function_get_status_name.php";

// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

// Check if request IDs have been passed to initiate responses
if (isset($_POST['request_ids']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['request_ids'] as $requestId) {
        // Ensure requestId is not empty or invalid
        if (!empty($requestId) && is_numeric($requestId)) {
            $fromWorkID = $_SESSION['work_id']; // The responder's work_id

            // Check current status of the request
            $statusSql = "SELECT request_status FROM requests WHERE request_id = :requestId";
            $statusStmt = $conn->prepare($statusSql);
            $statusStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
            $statusStmt->execute();
            $statusResult = $statusStmt->fetch(PDO::FETCH_ASSOC);

            if ($statusResult && $statusResult['request_status'] === 'pending') {

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
                            case 'payed_requested':
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
                                $updateUserSql = "UPDATE users SET payed_free = payed_free + 1, payed_requested = payed_requested - 1 WHERE work_id = :requestingUserID";
                                $updateUserStmt = $conn->prepare($updateUserSql);
                                $updateUserStmt->bindParam(':requestingUserID', $requestingUserID, PDO::PARAM_INT);
                                $updateUserStmt->execute();
                                break;
                            case 'payed_past_requested':
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
                                $updateUserSql = "UPDATE users SET payed_past_free = payed_past_free + 1, payed_past_requested = payed_past_requested - 1 WHERE work_id = :requestingUserID";
                                $updateUserStmt = $conn->prepare($updateUserSql);
                                $updateUserStmt->bindParam(':requestingUserID', $requestingUserID, PDO::PARAM_INT);
                                $updateUserStmt->execute();
                                break;
                            case 'payed_edu_requested':
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
                                $updateUserSql = "UPDATE users SET payed_edu_free = payed_edu_free + 1, payed_edu_requested = payed_edu_requested - 1 WHERE work_id = :requestingUserID";
                                $updateUserStmt = $conn->prepare($updateUserSql);
                                $updateUserStmt->bindParam(':requestingUserID', $requestingUserID, PDO::PARAM_INT);
                                $updateUserStmt->execute();
                                break;
                            case 'payed_award_requested':
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
                                $updateUserSql = "UPDATE users SET payed_award_free = payed_award_free + 1, payed_award_requested = payed_award_requested - 1 WHERE work_id = :requestingUserID";
                                $updateUserStmt = $conn->prepare($updateUserSql);
                                $updateUserStmt->bindParam(':requestingUserID', $requestingUserID, PDO::PARAM_INT);
                                $updateUserStmt->execute();
                                break;
                            case 'unpayed_dad_requested':
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
                                $updateUserSql = "UPDATE users SET unpayed_dad_free = unpayed_dad_free + 1, unpayed_dad_requested = unpayed_dad_requested - 1 WHERE work_id = :requestingUserID";
                                $updateUserStmt = $conn->prepare($updateUserSql);
                                $updateUserStmt->bindParam(':requestingUserID', $requestingUserID, PDO::PARAM_INT);
                                $updateUserStmt->execute();
                                break;
                            case 'unpayed_home_requested':
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
                                $updateUserSql = "UPDATE users SET unpayed_home_free = unpayed_home_free + 1, unpayed_home_requested = unpayed_home_requested - 1 WHERE work_id = :requestingUserID";
                                $updateUserStmt = $conn->prepare($updateUserSql);
                                $updateUserStmt->bindParam(':requestingUserID', $requestingUserID, PDO::PARAM_INT);
                                $updateUserStmt->execute();
                                break;
                            case 'unpayed_requested':
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
                                $updateUserSql = "UPDATE users SET unpayed_free = unpayed_free + 1, unpayed_requested = unpayed_requested - 1 WHERE work_id = :requestingUserID";
                                $updateUserStmt = $conn->prepare($updateUserSql);
                                $updateUserStmt->bindParam(':requestingUserID', $requestingUserID, PDO::PARAM_INT);
                                $updateUserStmt->execute();
                                break;
                            default:
                                // Optional: Handle unknown requested status
                                break;
                        }

                        // Insert the rejection message into the messages table
                        $message = getStatusName($requestedStatus). "Elutasítva";
                        $type = 'response to request';
                        $currentTimestamp = date('Y-m-d H:i:s');

                        $insertSql = "INSERT INTO messages (from_work_id, to_work_id, type, request_id, message, timestamp) VALUES (:fromWorkID, :toWorkID, :type, :requestId, :message, :timestamp)";
                        $insertStmt = $conn->prepare($insertSql);
                        $insertStmt->bindParam(':fromWorkID', $fromWorkID, PDO::PARAM_INT);
                        $insertStmt->bindParam(':toWorkID', $requestingUserID, PDO::PARAM_INT);
                        $insertStmt->bindParam(':type', $type, PDO::PARAM_STR);
                        $insertStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                        $insertStmt->bindParam(':message', $message, PDO::PARAM_STR);
                        $insertStmt->bindParam(':timestamp', $currentTimestamp, PDO::PARAM_STR);
                        $insertStmt->execute();

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
            }} else {
                echo "Invalid or empty Request ID encountered.<br>";
            }
        }
    } else {
        echo "No requests specified to respond to.";
    }
    ?>
