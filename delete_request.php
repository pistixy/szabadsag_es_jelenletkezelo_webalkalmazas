<?php
include "session_check.php";
include "connect.php";

if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

if (isset($_POST['request_id'])) {
    $requestId = $_POST['request_id'];

    // Fetch
    $statusSql = "SELECT requested_status, calendar_id, work_id FROM requests WHERE request_id = :requestId";
    $statusStmt = $conn->prepare($statusSql);
    $statusStmt->bindParam(':requestId', $requestId);
    $statusStmt->execute();
    $request = $statusStmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        $requested_status = $request['requested_status'];

        try {

            $conn->beginTransaction();

            switch ($requested_status) {
                case 'paid_requested':
                    // naptár nap státusz visszaállíatás
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'work_day' WHERE calendar_id = :calendarId";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendarId', $request['calendar_id']);
                    $updateCalendarStmt->execute();

                    // Töröltnek jelölés
                    $updateRequestSql = "UPDATE requests SET request_status = 'deleted' WHERE request_id = :requestId";
                    $updateRequestStmt = $conn->prepare($updateRequestSql);
                    $updateRequestStmt->bindParam(':requestId', $requestId);
                    $updateRequestStmt->execute();

                    // felhasználó adatainak módosítása
                    $updateUserSql = "UPDATE users SET paid_requested = paid_requested - 1, paid_free = paid_free + 1 WHERE work_id = :workId";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':workId', $request['work_id']);
                    $updateUserStmt->execute();

                    echo "A ", $requestId, " számú kérvény sikeresen törölve.";
                    break;
                
                default:
                    echo "Ismeretlen státusz.";
                    break;
            }

            // Commit
            $conn->commit();
        } catch (PDOException $e) {
            // hibák
            $conn->rollBack();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "kérelem ID nem találva.";
    }
} else {
    echo "Nincs kérelem ID.";
}
