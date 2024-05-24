<?php
// Ellenőrizzük, hogy a szükséges változók be vannak-e állítva
if (!isset($clickedDate)) {
    echo "Nincs dátum meghatározva.";
    return;
}

// Adatbáziskapcsolat fájl beillesztése
include "connect.php";
// Felhasználó munkaazonosítójának lekérése a munkamenetből
$userWorkID = $_SESSION['work_id'];

// Felhasználó részleteinek lekérése, mint például a pozíció, kar és szervezetszám
$positionSql = "SELECT position, kar, szervezetszam FROM users WHERE work_id = :userWorkID";
$positionStmt = $conn->prepare($positionSql);
$positionStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$positionStmt->execute();
$userDetails = $positionStmt->fetch(PDO::FETCH_ASSOC);

// Felhasználó pozíciójának, karjának és szervezetszámának tárolása változókban
$pozicio = $userDetails['position'];
$kar = $userDetails['kar'];
$szervezetszam = $userDetails['szervezetszam'];

// Pozíció alapján változó SQL lekérdezés kiválasztása
switch ($pozicio) {
    case 'admin':
        // Admin esetén összes felhasználót lekérünk
        $sql = "SELECT u.name, u.work_id, u.szervezetszam, u.kar, u.email, c.day_status 
                FROM calendar c
                JOIN users u ON c.work_id = u.work_id
                WHERE c.date = :clickedDate";
        break;
    case 'dekan':
        // Dekán esetén csak az adott karhoz tartozó felhasználókat kérjük le
        $sql = "SELECT u.name, u.work_id, u.szervezetszam, u.kar, u.email, c.day_status 
                FROM calendar c
                JOIN users u ON c.work_id = u.work_id
                WHERE c.date = :clickedDate AND u.kar = :kar";
        break;
    case 'tanszekvezeto':
        // Tanszékvezető esetén csak az adott karhoz és szervezetszámhoz tartozó felhasználókat kérjük le
        $sql = "SELECT u.name, u.work_id, u.szervezetszam, u.kar, u.email, c.day_status 
                FROM calendar c
                JOIN users u ON c.work_id = u.work_id
                WHERE c.date = :clickedDate AND u.kar = :kar AND u.szervezetszam = :szervezetszam";
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
    $stmt->bindParam(':kar', $kar);
} elseif ($pozicio === 'tanszekvezeto') {
    $stmt->bindParam(':kar', $kar);
    $stmt->bindParam(':szervezetszam', $szervezetszam);
}
$stmt->execute();
$dayUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ha vannak felhasználók az adott napon, listázzuk ki őket
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
        echo "<td>" . htmlspecialchars($user['kar']) . "</td>";
        echo "<td>" . htmlspecialchars($user['szervezetszam']) . "</td>";
        echo "<td>" . getName($user['day_status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    // Ha senki sem jelentkezett be az adott napon
    echo "<p>Erre a napra nem jelentkezett be senki.</p>";
}
?>
