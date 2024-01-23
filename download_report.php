<?php
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedDate = $_POST['selectedDate'];
    $status = $_POST['status'];
    $szervezetszam = $_POST['szervezetszam'];

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

    // Filename for the CSV
    $filename = "report_" . $selectedDate . ".csv";

    // Set headers to trigger download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'"');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Add column headers
    fputcsv($output, array('Work ID', 'Name', 'Email', 'Organization Number'));

    // Add data rows
    foreach ($result as $row) {
        fputcsv($output, $row);
    }

    // Close output stream
    fclose($output);
    exit();
}
?>
