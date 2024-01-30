<?php
// Make sure the required variables are set
if (!isset($clickedDate)) {
    echo "Nincs dátum meghatározva.";
    return;
}

include "connect.php"; // Ensure you have the database connection

// SQL to fetch user details and their status on a clicked date
$sql = "SELECT u.name, u.work_id,u.szervezetszam,u.email, c.day_status 
        FROM calendar c
        JOIN users u ON c.work_id = u.work_id
        WHERE c.date = :clickedDate";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':clickedDate', $clickedDate);
$stmt->execute();
$dayUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($dayUsers)) {
    echo "<h3>Felhasználók a $clickedDate napon:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Név</th><th>work_id</th><th>Email</th><th>Szervezetszám</th><th>Státusz</th></tr>";
    foreach ($dayUsers as $user) {
        $profileUrl = "profile.php?work_id=" . urlencode($user['work_id']);
        echo "<tr>";
        echo "<td><a href='$profileUrl'>" . htmlspecialchars($user['name']) . "</a></td>";
        echo "<td><a href='$profileUrl'>" . htmlspecialchars($user['work_id']) . "</a></td>";
        echo "<td><a href='$profileUrl'>" . htmlspecialchars($user['email']) . "</a></td>";
        echo "<td>".htmlspecialchars($user['szervezetszam'])."</td>";
        echo "<td>" . getStatusName($user['day_status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Erre a napra nem jelentkezett be senki.</p>";
}

?>
