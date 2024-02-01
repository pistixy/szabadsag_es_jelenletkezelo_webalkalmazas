<?php
include "session_check.php";
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
        $requested_status=$requestData['requested_status'];

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
            case 'unpayed_requested':
                $userUpdateSql = "UPDATE users SET unpayed_requested = unpayed_requested - 1, unpayed_planned = unpayed_planned + 1 WHERE work_id = :workId";
                $userUpdateStmt = $conn->prepare($userUpdateSql);
                $userUpdateStmt->bindParam(':workId', $requestData['work_id'], PDO::PARAM_INT);
                $userUpdateStmt->execute();

                $acceptRequestSql = "UPDATE requests SET request_status = 'accepted' WHERE request_id = :requestId";
                $acceptRequestStmt = $conn->prepare($acceptRequestSql);
                $acceptRequestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
                $acceptRequestStmt->execute();

                $calendarUpdateSql = "UPDATE calendar SET day_status = 'unpayed_planned' WHERE calendar_id = :calendarId";
                $calendarUpdateStmt = $conn->prepare($calendarUpdateSql);
                $calendarUpdateStmt->bindParam(':calendarId', $requestData['calendar_id'], PDO::PARAM_INT);
                $calendarUpdateStmt->execute();
                break;
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

?>
