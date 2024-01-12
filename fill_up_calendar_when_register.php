<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Use the correct column name from your PostgreSQL database
    $stmt = $conn->prepare("SELECT work_id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if (count($result) == 1) {
        $row = $result[0];
        $userWorkId = $row['work_id']; // Adjust the column name to match your database

        // Fill calendar data...
        $currentDate = new DateTime();
        $lastDayOfYear = new DateTime($currentDate->format('Y-12-31'));
        $interval = $currentDate->diff($lastDayOfYear);
        $remainingDays = $interval->format('%a');
        $limit = $remainingDays + 7;

        for ($i = 0; $i < $limit; $i++) {
            $date = date("Y-m-d", strtotime($currentDate->format("Y-m-d") . " + " . $i . " days"));
            $isWorkingDay = date('N', strtotime($date)) <= 5 ? 1 : 0;
            $isVacationDay = $isWorkingDay ? 0 : 1;

            $stmt = $conn->prepare("INSERT INTO calendar (work_id, date, is_working_day, is_vacation_day) VALUES (:work_id, :date, :is_working_day, :is_vacation_day)");
            $stmt->bindParam(':work_id', $userWorkId);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':is_working_day', $isWorkingDay);
            $stmt->bindParam(':is_vacation_day', $isVacationDay);

            if ($stmt->execute()) {
                // Successful insert
            } else {
                echo "Error inserting data for date: " . $date . "<br>";
                echo "Error: " . $stmt->errorInfo()[2] . "<br>"; // PDO error info
            }
        }
        echo "Calendar data for the next $limit days has been filled.";
    } else {
        echo "User not found in the database.";
    }
} else {
    echo "User session not found.";
}

