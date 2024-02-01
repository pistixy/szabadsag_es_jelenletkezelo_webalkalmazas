<?php
// Make sure the required variables are set
if (!isset($clickedDate)) {
    echo "Nincs dátum meghatározva.";
    return;
}

include "connect.php"; // Ensure you have the database connection
$userWorkID=$_SESSION['work_id'];

$positionSql = "SELECT position, kar, szervezetszam FROM users WHERE work_id = :userWorkID";
$positionStmt = $conn->prepare($positionSql);
$positionStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$positionStmt->execute();
$userDetails = $positionStmt->fetch(PDO::FETCH_ASSOC);

$pozicio = $userDetails['position'];
$kar = $userDetails['kar'];
$szervezetszam = $userDetails['szervezetszam'];

switch ($pozicio) {
    case 'admin':
        // For admin, fetch all users
        $sql = "SELECT u.name, u.work_id, u.szervezetszam, u.kar, u.email, c.day_status 
                FROM calendar c
                JOIN users u ON c.work_id = u.work_id
                WHERE c.date = :clickedDate";
        break;
    case 'dekan':
        // For 'dekan', fetch users from a specific 'kar'
        $sql = "SELECT u.name, u.work_id, u.szervezetszam, u.kar, u.email, c.day_status 
                FROM calendar c
                JOIN users u ON c.work_id = u.work_id
                WHERE c.date = :clickedDate AND u.kar = :kar";
        break;
    case 'tanszekvezeto':
        // For 'tanszekvezeto', fetch users from a specific 'kar' and 'szervezetszam'
        $sql = "SELECT u.name, u.work_id, u.szervezetszam, u.kar, u.email, c.day_status 
                FROM calendar c
                JOIN users u ON c.work_id = u.work_id
                WHERE c.date = :clickedDate AND u.kar = :kar AND u.szervezetszam = :szervezetszam";
        break;
    default:
        // For a regular user, do not display anything
        echo "You do not have permission to view this information.";
        exit;
}


$stmt = $conn->prepare($sql);
$stmt->bindParam(':clickedDate', $clickedDate);
if ($pozicio === 'dekan') {
    $stmt->bindParam(':kar', $kar);
} elseif ($pozicio === 'tanszekvezeto') {
    $stmt->bindParam(':kar', $kar);
    $stmt->bindParam(':szervezetszam', $szervezetszam);
}
$stmt->execute();
$dayUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($dayUsers)) {
    echo "<h3>Felhasználók a $clickedDate napon:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Név</th><th>work_id</th><th>Email</th><th>Kar</th><th>Szervezetszám</th><th>Státusz</th></tr>";
    foreach ($dayUsers as $user) {
        $profileUrl = "profile.php?work_id=" . urlencode($user['work_id']);
        echo "<tr>";
        echo "<td><a href='$profileUrl'>" . htmlspecialchars($user['name']) . "</a></td>";
        echo "<td><a href='$profileUrl'>" . htmlspecialchars($user['work_id']) . "</a></td>";
        echo "<td><a href='$profileUrl'>" . htmlspecialchars($user['email']) . "</a></td>";
        echo "<td>".htmlspecialchars($user['kar'])."</td>";
        echo "<td>".htmlspecialchars($user['szervezetszam'])."</td>";
        echo "<td>" . getStatusName($user['day_status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Erre a napra nem jelentkezett be senki.</p>";
}

?>
