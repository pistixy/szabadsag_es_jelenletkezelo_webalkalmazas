<?php
include "session_check.php";
include "connect.php";
include "function_get_name.php";

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

                        
                       

                        switch ($requested_status){
                            case 'paid_requested':
                                $userUpdateSql = "UPDATE users SET paid_requested = paid_requested - 1, paid_planned = paid_planned + 1 WHERE work_id = :workId";
                                $userUpdateStmt = $conn->prepare($userUpdateSql);
                                $userUpdateStmt->bindParam(':workId', $requestData['work_id'], PDO::PARAM_INT);
                                $userUpdateStmt->execute();

                                $acceptRequestSql = "UPDATE requests SET request_status = 'accepted' WHERE request_id = :requestId";
                                $acceptRequestStmt = $conn->prepare($acceptRequestSql);
                                $acceptRequestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                                $acceptRequestStmt->execute();

                                $calendarUpdateSql = "UPDATE calendar SET day_status = 'paid_planned' WHERE calendar_id = :calendarId";
                                $calendarUpdateStmt = $conn->prepare($calendarUpdateSql);
                                $calendarUpdateStmt->bindParam(':calendarId', $requestData['calendar_id'], PDO::PARAM_INT);
                                $calendarUpdateStmt->execute();
                                break;
                            
                        }


                    }

                    // Commit the transaction
                    $conn->commit();
                    echo "Request ID $requestId has been successfully accepted.<br>";
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
