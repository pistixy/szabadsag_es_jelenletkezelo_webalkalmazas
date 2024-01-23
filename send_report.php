<?php

session_start();
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['email'])) {
    $selectedDate = $_POST['selectedDate'];
    $status = $_POST['status'];
    $szervezetszam = $_POST['szervezetszam'];
    $userEmail = $_SESSION['email']; // Email from session

    // Prepare and execute the query
    $sql = "SELECT c.work_id, u.name, u.email, u.szervezetszam
            FROM calendar AS c
            LEFT JOIN users AS u ON c.work_id = u.work_id
            WHERE c.date = :selectedDate AND c.day_status = :status AND u.szervezetszam = :szervezetszam";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':selectedDate', $selectedDate);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':szervezetszam', $szervezetszam, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build the report content
    $reportContent = "Report for Date: $selectedDate\nStatus: $status\nOrganization Number: $szervezetszam\n\n";
    $reportContent .= "Work ID, Name, Email, Organization Number\n";
    foreach ($result as $row) {
        $reportContent .= implode(", ", $row) . "\n";
    }

    // Email subject and headers
    $subject = "Report for $selectedDate";
    $headers = "From: webmaster@example.com";

    // Send the email
    if (mail($userEmail, $subject, $reportContent, $headers)) {
        echo "Report sent successfully to $userEmail";
    } else {
        echo "Failed to send report";
    }
} else {
    echo "Invalid request or no user email in session.";
}

