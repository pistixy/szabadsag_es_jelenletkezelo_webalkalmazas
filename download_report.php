<?php
include "connect.php";

// Ellenőrizzük, hogy az űrlap elküldve lett-e
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiválasztott dátum és státusz lekérése az űrlapról
    $selectedDate = $_POST['selectedDate'];
    $status = $_POST['status'];
    $szervezetszam = $_POST['szervezetszam'];

    // SQL lekérdezés előkészítése és végrehajtása
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

    // CSV fájlnév
    $filename = "report_" . $selectedDate . ".csv";

    // Fejlécek beállítása a letöltés kiváltásához
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'"');

    // Kimeneti adatfolyam megnyitása
    $output = fopen('php://output', 'w');

    // Oszlopfejlécek hozzáadása
    fputcsv($output, array('Munkaazonosító', 'Név', 'E-mail', 'Szervezetszám'));

    // Adatsorok hozzáadása
    foreach ($result as $row) {
        fputcsv($output, $row);
    }

    // Kimeneti adatfolyam bezárása
    fclose($output);
    exit();
}
?>
