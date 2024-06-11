<?php
include "session_check.php";
include "app/config/connect.php";
include "app/helpers/function_get_name.php";

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
                        // Commit the transaction
                        $conn->commit();

                        echo "Kérelem ID: ",$requestId, ": A kérelem sikeresen elutasítva.";
                    } else {
                        $conn->rollBack();
                        echo "Request not found.";
                    }
                } catch (Exception $e) {
                    $conn->rollBack();
                    echo "Hiba történt a kérelem feldolgozása közben: " . $e->getMessage();
                }
            }} else {
                echo "Hibás vagy hiányzó kérelem ID.<br>";
            }
        }
    } else {
        echo "Nincs kiválasztott kérelem.";
    }
    ?>
