<?php
// Ellenőrizzük, hogy a szükséges változók be vannak-e állítva
if (!isset($clickedDate)) {
    echo "Nincs dátum meghatározva.";
    return;
}

// Adatbáziskapcsolat fájl beillesztése
include "app/config/connect.php";
// Felhasználó munkaazonosítójának lekérése a munkamenetből
$userWorkID = $_SESSION['work_id'];

// Felhasználó részleteinek lekérése, mint például a pozíció, faculty és szervezetszám
$positionSql = "SELECT position, faculty, entity_id FROM users WHERE work_id = :userWorkID";
$positionStmt = $conn->prepare($positionSql);
$positionStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$positionStmt->execute();
$userDetails = $positionStmt->fetch(PDO::FETCH_ASSOC);

// Felhasználó pozíciójának, karjának és szervezetszámának tárolása változókban
$pozicio = $userDetails['position'];
$faculty = $userDetails['faculty'];
$entity_id = $userDetails['entity_id'];

// Pozíció alapján változó SQL lekérdezés kiválasztása
switch ($pozicio) {
    case 'admin':
        // Admin esetén összes felhasználót lekérünk
        $sql = "SELECT u.name, u.work_id, u.entity_id, u.faculty, u.email, c.day_status 
                FROM calendar c
                JOIN users u ON c.work_id = u.work_id
                WHERE c.date = :clickedDate";
        break;
    case 'dekan':
        // Dekán esetén csak az adott karhoz tartozó felhasználókat kérjük le
        $sql = "SELECT u.name, u.work_id, u.entity_id, u.faculty, u.email, c.day_status 
                FROM calendar c
                JOIN users u ON c.work_id = u.work_id
                WHERE c.date = :clickedDate AND u.faculty = :faculty";
        break;
    case 'tanszekvezeto':
        // Tanszékvezető esetén csak az adott karhoz és szervezetszámhoz tartozó felhasználókat kérjük le
        $sql = "SELECT u.name, u.work_id, u.entity_id, u.faculty, u.email, c.day_status 
                FROM calendar c
                JOIN users u ON c.work_id = u.work_id
                WHERE c.date = :clickedDate AND u.faculty = :faculty AND u.entity_id = :entity_id";
        break;
    default:
        // Szokásos felhasználó esetén ne jelenítsünk meg semmit
        echo "Nincs jogosultságod megtekinteni ezt az információt.";
        exit;
}

// SQL lekérdezés előkészítése és végrehajtása
$stmt = $conn->prepare($sql);
$stmt->bindParam(':clickedDate', $clickedDate);
if ($pozicio === 'dekan') {
    $stmt->bindParam(':faculty', $faculty);
} elseif ($pozicio === 'tanszekvezeto') {
    $stmt->bindParam(':faculty', $faculty);
    $stmt->bindParam(':entity_id', $entity_id);
}
$stmt->execute();
$dayUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ha vannak felhasználók az adott napon, listázzuk ki őket
if (!empty($dayUsers)) {
    echo "<h3>Felhasználók a $clickedDate napon:</h3>";
    echo "<table class='table'>";
    echo "<tr><th>Név</th><th>work_id</th><th>Email</th><th>Kar</th><th>Szervezetszám</th><th>Státusz</th></tr>";
    foreach ($dayUsers as $user) {
        $profileUrl = "profile.php?work_id=" . urlencode($user['work_id']);
        echo "<tr>";
        echo "<td><a href='$profileUrl'>" . htmlspecialchars($user['name']) . "</a></td>";
        echo "<td><a href='$profileUrl'>" . htmlspecialchars($user['work_id']) . "</a></td>";
        echo "<td><a href='$profileUrl'>" . htmlspecialchars($user['email']) . "</a></td>";
        echo "<td>" . htmlspecialchars($user['faculty']) . "</td>";
        echo "<td>" . htmlspecialchars($user['entity_id']) . "</td>";
        echo "<td>" . getName($user['day_status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    // Ha senki sem jelentkezett be az adott napon
    echo "<p>Erre a napra nem jelentkezett be senki.</p>";
}
?>
