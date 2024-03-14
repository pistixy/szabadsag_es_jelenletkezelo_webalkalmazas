<?php
if (session_status() === PHP_SESSION_NONE) {
    include "session_check.php";
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

        // Fill calendar data for the past 1 year and the next 2 years
        $currentDate = new DateTime();
        $pastLimit = 365; // Fill data for the past 1 year
        $futureLimit = 365 * 2; // Fill data for the next 2 years

        // Fill past calendar data
        for ($i = 1; $i <= $pastLimit; $i++) {
            $date = date("Y-m-d", strtotime($currentDate->format("Y-m-d") . " - " . $i . " days"));
            $day_status = date('N', strtotime($date)) <= 5 ? "work_day" : "weekend";

            $stmt = $conn->prepare("INSERT INTO calendar (work_id, date, day_status ) VALUES (:work_id, :date, :day_status )");
            $stmt->bindParam(':work_id', $userWorkId);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':day_status', $day_status);

            if ($stmt->execute()) {
                // Successful insert
            } else {
                echo "Error inserting data for date: " . $date . "<br>";
                echo "Error: " . $stmt->errorInfo()[2] . "<br>"; // PDO error info
            }
        }

        // Fill future calendar data
        for ($i = 0; $i < $futureLimit; $i++) {
            $date = date("Y-m-d", strtotime($currentDate->format("Y-m-d") . " + " . $i . " days"));
            $day_status = date('N', strtotime($date)) <= 5 ? "work_day" : "weekend";

            $stmt = $conn->prepare("INSERT INTO calendar (work_id, date, day_status ) VALUES (:work_id, :date, :day_status )");
            $stmt->bindParam(':work_id', $userWorkId);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':day_status', $day_status);

            if ($stmt->execute()) {
                // Successful insert
            } else {
                echo "Error inserting data for date: " . $date . "<br>";
                echo "Error: " . $stmt->errorInfo()[2] . "<br>"; // PDO error info
            }
        }

        echo "Calendar data for the past 1 year and the next 2 years has been filled.";
    } else {
        echo "User not found in the database.";
    }
} else {
    echo "User session not found.";
}
?>
