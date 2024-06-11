<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include "app/config/connect.php";

if (isset($_SESSION['work_id'])) {
    $userWorkId = $_SESSION['work_id'];
    $comment = 'Ünnep';  // Komment definiáása

    // ünnepek tömb "MM-DD"
    include "holiday_array.php";

    // Prepare SQL to update comment for matching month and day, regardless of year
    $sql = "UPDATE calendar SET comment = ?
            WHERE work_id = ?
            AND TO_CHAR(date, 'MM-DD') IN (" . implode(',', array_fill(0, count($publicHolidays), '?')) . ")";
    $stmt = $conn->prepare($sql);

    // Execute with all holiday month-days and the user work ID
    $params = array_merge([$comment, $userWorkId], $publicHolidays);
    if ($stmt->execute($params)) {
        echo "Comments added to the calendar for the current user successfully.<br>";
    } else {
        echo "Error adding comments for the current user: " . $stmt->errorInfo()[2] . "<br>";
    }

    // Update day_status based on comment
    $commentToUpdate = 'Ünnep';
    $day_status = "holiday";

    $sql = "UPDATE calendar SET day_status = ? WHERE comment = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $day_status, PDO::PARAM_STR);
    $stmt->bindParam(2, $commentToUpdate);

    if ($stmt->execute()) {
        echo "Entries with comment 'Ünnep' updated successfully. day_status set to 'holiday'.<br>";
    } else {
        echo "Error updating entries with comment 'Ünnep': " . $stmt->errorInfo()[2] . "<br>";
    }

} else {
    echo "User session not found.";
}
?>
