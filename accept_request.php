<?php
include "session_check.php";
include "app/config/connect.php";

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
        // Commit the transaction
        $conn->commit();
        echo "A kérelem sikeresen elfogadva.";
    } catch (Exception $e) {
        // An error occurred; roll back the transaction
        $conn->rollBack();
        echo "Hiba történt a kérelem elfogadása során: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}

?>
