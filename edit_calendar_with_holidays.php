<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include "connect.php";

if (isset($_SESSION['work_id'])) {
    $userWorkId = $_SESSION['work_id'];
    $comment = 'Ünnep';  // Define the comment variable

    $publicHolidays = [
        '2024-01-01', '2024-03-15', '2024-05-01', '2024-08-20',
        '2023-10-23', '2023-11-01', '2023-12-24', '2023-12-25', '2023-12-26'
    ];

    $placeholders = str_repeat('?,', count($publicHolidays)-1) . '?';
    $sql = "UPDATE calendar SET comment = ? WHERE work_id = ? AND date IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    $params = array_merge([$comment, $userWorkId], $publicHolidays);
    $stmt->execute($params);  // Correctly pass the parameters array

    if ($stmt->execute()) {
        echo "Comments added to the calendar for the current user successfully.<br>";
    } else {
        echo "Error adding comments for the current user: " . $stmt->errorInfo()[2] . "<br>";
    }

    $commentToUpdate = 'Ünnep';
    $isWorkingDayValue = 0;
    $isVacationDayValue = 1;

    $sql = "UPDATE calendar SET is_working_day = ?, is_vacation_day = ? WHERE comment = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $isWorkingDayValue, PDO::PARAM_INT);
    $stmt->bindParam(2, $isVacationDayValue, PDO::PARAM_INT);
    $stmt->bindParam(3, $commentToUpdate);

    if ($stmt->execute()) {
        echo "Entries with comment 'Ünnep' updated successfully. is_working_day set to 1 and is_vacation_day set to 0.<br>";
    } else {
        echo "Error updating entries with comment 'Ünnep': " . $stmt->errorInfo()[2] . "<br>";
    }

} else {
    echo "User session not found.";
}
?>
