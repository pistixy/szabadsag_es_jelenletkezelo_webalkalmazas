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
                    // Fetch the requested_status and calendar_id for this request_id from the requests table
                    $requestSql = "SELECT work_id, requested_status, calendar_id FROM requests WHERE request_id = :requestId";
                    $requestStmt = $conn->prepare($requestSql);
                    $requestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                    $requestStmt->execute();
                    $requestData = $requestStmt->fetch(PDO::FETCH_ASSOC);

                    if ($requestData){
                        $requestingUserID = $requestData['work_id']; // The requester's work_id
                        $calendarId = $requestData['calendar_id']; // The calendar_id associated with the request
                        $requested_status=$requestData['requested_status'];

                        // Insert the rejection message into the messages table
                        $message = getStatusName($requested_status) . " Elfogadva";
                        $type = 'response to request';
                        $currentTimestamp = date('Y-m-d H:i:s');

                        $insertSql = "INSERT INTO messages (from_work_id, to_work_id, to_position, type, request_id, message, timestamp) VALUES (:fromWorkID, :toWorkID, '', :type, :requestId, :message, :timestamp)";
                        $insertStmt = $conn->prepare($insertSql);
                        $insertStmt->bindParam(':fromWorkID', $fromWorkID, PDO::PARAM_INT);
                        $insertStmt->bindParam(':toWorkID', $requestingUserID, PDO::PARAM_INT);
                        $insertStmt->bindParam(':type', $type, PDO::PARAM_STR);
                        $insertStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                        $insertStmt->bindParam(':message', $message, PDO::PARAM_STR);
                        $insertStmt->bindParam(':timestamp', $currentTimestamp, PDO::PARAM_STR);
                        $insertStmt->execute();

                        switch ($requested_status){
                            case 'payed_requested':
                                $userUpdateSql = "UPDATE users SET payed_requested = payed_requested - 1, payed_planned = payed_planned + 1 WHERE work_id = :workId";
                                $userUpdateStmt = $conn->prepare($userUpdateSql);
                                $userUpdateStmt->bindParam(':workId', $requestData['work_id'], PDO::PARAM_INT);
                                $userUpdateStmt->execute();

                                $acceptRequestSql = "UPDATE requests SET request_status = 'accepted' WHERE request_id = :requestId";
                                $acceptRequestStmt = $conn->prepare($acceptRequestSql);
                                $acceptRequestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                                $acceptRequestStmt->execute();

                                $calendarUpdateSql = "UPDATE calendar SET day_status = 'payed_planned' WHERE calendar_id = :calendarId";
                                $calendarUpdateStmt = $conn->prepare($calendarUpdateSql);
                                $calendarUpdateStmt->bindParam(':calendarId', $requestData['calendar_id'], PDO::PARAM_INT);
                                $calendarUpdateStmt->execute();
                                break;
                            case 'payed_past_requested':
                                $userUpdateSql = "UPDATE users SET payed_past_requested = payed_past_requested - 1, payed_past_planned = payed_past_planned + 1 WHERE work_id = :workId";
                                $userUpdateStmt = $conn->prepare($userUpdateSql);
                                $userUpdateStmt->bindParam(':workId', $requestData['work_id'], PDO::PARAM_INT);
                                $userUpdateStmt->execute();

                                $acceptRequestSql = "UPDATE requests SET request_status = 'accepted' WHERE request_id = :requestId";
                                $acceptRequestStmt = $conn->prepare($acceptRequestSql);
                                $acceptRequestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                                $acceptRequestStmt->execute();

                                $calendarUpdateSql = "UPDATE calendar SET day_status = 'payed_past_planned' WHERE calendar_id = :calendarId";
                                $calendarUpdateStmt = $conn->prepare($calendarUpdateSql);
                                $calendarUpdateStmt->bindParam(':calendarId', $requestData['calendar_id'], PDO::PARAM_INT);
                                $calendarUpdateStmt->execute();
                                break;
                            case 'payed_award_requested':
                                $userUpdateSql = "UPDATE users SET payed_award_requested = payed_award_requested - 1, payed_award_planned = payed_award_planned + 1 WHERE work_id = :workId";
                                $userUpdateStmt = $conn->prepare($userUpdateSql);
                                $userUpdateStmt->bindParam(':workId', $requestData['work_id'], PDO::PARAM_INT);
                                $userUpdateStmt->execute();

                                $acceptRequestSql = "UPDATE requests SET request_status = 'accepted' WHERE request_id = :requestId";
                                $acceptRequestStmt = $conn->prepare($acceptRequestSql);
                                $acceptRequestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                                $acceptRequestStmt->execute();

                                $calendarUpdateSql = "UPDATE calendar SET day_status = 'payed_award_planned' WHERE calendar_id = :calendarId";
                                $calendarUpdateStmt = $conn->prepare($calendarUpdateSql);
                                $calendarUpdateStmt->bindParam(':calendarId', $requestData['calendar_id'], PDO::PARAM_INT);
                                $calendarUpdateStmt->execute();
                                break;
                            case 'payed_edu_requested':
                                $userUpdateSql = "UPDATE users SET payed_edu_requested = payed_edu_requested - 1, payed_edu_planned = payed_edu_planned + 1 WHERE work_id = :workId";
                                $userUpdateStmt = $conn->prepare($userUpdateSql);
                                $userUpdateStmt->bindParam(':workId', $requestData['work_id'], PDO::PARAM_INT);
                                $userUpdateStmt->execute();

                                $acceptRequestSql = "UPDATE requests SET request_status = 'accepted' WHERE request_id = :requestId";
                                $acceptRequestStmt = $conn->prepare($acceptRequestSql);
                                $acceptRequestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                                $acceptRequestStmt->execute();

                                $calendarUpdateSql = "UPDATE calendar SET day_status = 'payed_edu_planned' WHERE calendar_id = :calendarId";
                                $calendarUpdateStmt = $conn->prepare($calendarUpdateSql);
                                $calendarUpdateStmt->bindParam(':calendarId', $requestData['calendar_id'], PDO::PARAM_INT);
                                $calendarUpdateStmt->execute();
                                break;
                            case 'unpayed_dad_requested':
                                $userUpdateSql = "UPDATE users SET unpayed_dad_requested = unpayed_dad_requested - 1, unpayed_dad_planned = unpayed_dad_planned + 1 WHERE work_id = :workId";
                                $userUpdateStmt = $conn->prepare($userUpdateSql);
                                $userUpdateStmt->bindParam(':workId', $requestData['work_id'], PDO::PARAM_INT);
                                $userUpdateStmt->execute();

                                $acceptRequestSql = "UPDATE requests SET request_status = 'accepted' WHERE request_id = :requestId";
                                $acceptRequestStmt = $conn->prepare($acceptRequestSql);
                                $acceptRequestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                                $acceptRequestStmt->execute();

                                $calendarUpdateSql = "UPDATE calendar SET day_status = 'unpayed_dad_planned' WHERE calendar_id = :calendarId";
                                $calendarUpdateStmt = $conn->prepare($calendarUpdateSql);
                                $calendarUpdateStmt->bindParam(':calendarId', $requestData['calendar_id'], PDO::PARAM_INT);
                                $calendarUpdateStmt->execute();
                                break;
                            case 'unpayed_home_requested':
                                $userUpdateSql = "UPDATE users SET unpayed_home_requested = unpayed_home_requested - 1, unpayed_home_planned = unpayed_home_planned + 1 WHERE work_id = :workId";
                                $userUpdateStmt = $conn->prepare($userUpdateSql);
                                $userUpdateStmt->bindParam(':workId', $requestData['work_id'], PDO::PARAM_INT);
                                $userUpdateStmt->execute();

                                $acceptRequestSql = "UPDATE requests SET request_status = 'accepted' WHERE request_id = :requestId";
                                $acceptRequestStmt = $conn->prepare($acceptRequestSql);
                                $acceptRequestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                                $acceptRequestStmt->execute();

                                $calendarUpdateSql = "UPDATE calendar SET day_status = 'unpayed_home_planned' WHERE calendar_id = :calendarId";
                                $calendarUpdateStmt = $conn->prepare($calendarUpdateSql);
                                $calendarUpdateStmt->bindParam(':calendarId', $requestData['calendar_id'], PDO::PARAM_INT);
                                $calendarUpdateStmt->execute();
                                break;
                        }


                    }

                    // Commit the transaction
                    $conn->commit();
                    echo "Request ID $requestId has been successfully accepted and message sent.<br>";
                } catch (Exception $e) {
                    $conn->rollBack();
                    echo "An error occurred while processing request ID $requestId: " . $e->getMessage() . "<br>";
                }
            }} else {
                echo "Invalid or empty Request ID encountered.<br>";
            }
        }
    } else {
        echo "No requests specified to respond to.";
    }
    ?>
