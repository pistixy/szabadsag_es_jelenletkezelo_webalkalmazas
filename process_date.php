<?php
include "connect.php";

if (isset($_POST['submit'])) {
    $selectedDate = $_POST['selectedDate'];
    $status = $_POST['status'];
    $szervezetszam = $_POST['szervezetszam'];

    $statusLabels = [
        0 => "Szabadnap",
        1 => "Munkanap",
        2 => "Online Munka",
        3 => "Betegszabadság",
        4 => "Fizetetlen szabadság",
        5 => "Tervezett szabadság",
    ];

    $sql = "SELECT c.work_id, u.name, u.email, u.szervezetszam
            FROM calendar AS c
            LEFT JOIN users AS u ON c.work_id = u.work_id
            WHERE c.date = :selectedDate AND c.is_working_day = :status AND u.szervezetszam = :szervezetszam";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':selectedDate', $selectedDate);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':szervezetszam', $szervezetszam, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        echo "<h2>Dolgozók a $szervezetszam számú szervezetben a $selectedDate napon $statusLabels[$status] státuszban voltak:</h2>";
        echo "<table>";
        echo "<tr><th>work_id</th><th>Név</th><th>Email cím</th><th>Szervezetszám</th></tr>";
        foreach ($result as $row) {
            echo "<tr><td>" . $row['work_id'] . "</td><td>" . $row['name'] . "</td><td>" . $row['email'] . "</td><td>" . $row['szervezetszam'] . "</td></tr>";
        }
        echo "</table>";
        echo "<br>";
        echo "Összesen a kijelölt napon ($selectedDate) a $szervezetszam számú szervezetben ennyien voltak $statusLabels[$status] státuszban: " . count($result);
    } else {
        echo "Nincs az adott napon adott státuszban lévő dolgozó az adott szervezetből.";
    }
} else {
    echo "Nincs submit.";
}
?>
