<?php
include "connect.php";

if (isset($_POST['submit'])) {
    $selectedDate = $_POST['selectedDate'];
    $status = $_POST['status'];

    $statusLabels = [
        0 => "Szabadnap",
        1 => "Munkanap",
        2 => "Online Munka",
        3 => "Betegszabadság",
        4 => "Fizetetlen szabadság",
        5 => "Tervezett szabadság",
    ];

    $sql = "SELECT c.WORKID, u.NAME, u.EMAIL, u.szervezetszam
            FROM calendar AS c
            LEFT JOIN users AS u ON c.WORKID = u.WORKID
            WHERE c.date = ? AND c.is_working_day = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $selectedDate, $status);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Dolgozók akik a $selectedDate napon $statusLabels[$status] státuszban voltak:</h2>";
        echo "<table>";
        echo "<tr><th>WORKID</th><th>Név</th><th>Email cím</th><th>Szervezetszám</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['WORKID'] . "</td><td>" . $row['NAME'] . "</td><td>" . $row['EMAIL'] . "</td><td>" . $row['szervezetszam'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "Nincs az adott napon adott státuszban lévő dolgozó.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Nincs submit.";
}
?>
