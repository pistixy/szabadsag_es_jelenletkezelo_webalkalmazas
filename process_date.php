<?php

include "connect.php";
include "session_check.php";
include "nav-bar.php";

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
            WHERE c.date = :selectedDate AND c.day_status = :status AND u.szervezetszam = :szervezetszam";
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
            echo "<tr>";
            echo "<td><a href='profile.php?work_id=" . $row['work_id'] . "'>" . $row['work_id'] . "</a></td>";
            echo "<td><a href='profile.php?work_id=" . $row['work_id'] . "'>" . $row['name'] . "</a></td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['szervezetszam'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<br>";
        echo "Összesen a kijelölt napon ($selectedDate) a $szervezetszam számú szervezetben ennyien voltak $statusLabels[$status] státuszban: " . count($result);

        // "Jelentés küldése" button
        echo '<form action="send_report.php" method="post">';
        echo '<input type="hidden" name="selectedDate" value="' . $selectedDate . '">';
        echo '<input type="hidden" name="status" value="' . $status . '">';
        echo '<input type="hidden" name="szervezetszam" value="' . $szervezetszam . '">';
        echo '<input type="submit" value="Jelentés küldése" name="sendReport">';
        echo '</form>';

        // "Jelentés letöltése" button
        echo '<form action="download_report.php" method="post">';
        echo '<input type="hidden" name="selectedDate" value="' . $selectedDate . '">';
        echo '<input type="hidden" name="status" value="' . $status . '">';
        echo '<input type="hidden" name="szervezetszam" value="' . $szervezetszam . '">';
        echo '<input type="submit" value="Jelentés letöltése" name="downloadReport">';
        echo '</form>';
    } else {
        echo "Nincs az adott napon adott státuszban lévő dolgozó az adott szervezetből.";
    }
} else {
    echo "Nincs submit.";
}

include "footer.php";
?>
