<?php
// Munkamenet ellenőrzése
include "session_check.php";
// Adatbáziskapcsolat
include "connect.php";

// Ha a kérés POST és van e-mail a munkamenetben
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['email'])) {
    // Kiválasztott dátum és státusz begyűjtése az űrlapról
    $selectedDate = $_POST['selectedDate'];
    $status = $_POST['status'];
    $szervezetszam = $_POST['szervezetszam'];
    $userEmail = $_SESSION['email']; // E-mail a munkamenetből

    // Lekérdezés összeállítása és végrehajtása
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

    // Jelentés tartalmának összeállítása
    $reportContent = "Jelentés a dátumról: $selectedDate\nStátusz: $status\nSzervezetszám: $szervezetszam\n\n";
    $reportContent .= "Munkaidő, Név, E-mail, Szervezetszám\n";
    foreach ($result as $row) {
        $reportContent .= implode(", ", $row) . "\n";
    }

    // E-mail tárgy és fejlécek
    $subject = "Jelentés a dátumról: $selectedDate";
    $headers = "From: webmaster@example.com";

    // E-mail küldése
    if (mail($userEmail, $subject, $reportContent, $headers)) {
        echo "Jelentés sikeresen elküldve ide: $userEmail";
    } else {
        echo "Jelentés küldése sikertelen";
    }
} else {
    echo "Érvénytelen kérés vagy nincs felhasználói e-mail a munkamenetben.";
}
?>
