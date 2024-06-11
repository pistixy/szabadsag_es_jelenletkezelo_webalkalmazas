<?php
// Munkamenet ellenőrző fájl beillesztése
include "session_check.php";
// Adatbázis kapcsolatfájl beillesztése
include "app/config/connect.php";
// getName fuggveny beillsztese
include "function_get_name.php";

// A keresési lekérdezés lekérdezése az URL-ből, alapértelmezetten üres stringgel
$searchQuery = $_GET['search_query'] ?? '';

// A keresési kifejezés tisztítása
$searchTerm = "%" . $searchQuery . "%";

// Felhasználó adatainak lekérése a munkamenetben tárolt work_id alapján
$workId = $_SESSION['work_id'] ?? '';
$positionSql = "SELECT position, kar, szervezetszam FROM users WHERE work_id = :workId";
$positionStmt = $conn->prepare($positionSql);
$positionStmt->bindParam(':workId', $workId, PDO::PARAM_INT);
$positionStmt->execute();
$userData = $positionStmt->fetch(PDO::FETCH_ASSOC);

if ($userData) {
    $userPosition = $userData['position'];
    $kar = $userData['kar'];
    $szervezetszam = $userData['szervezetszam'];
} else {
    // Kezeljük az esetet, ha nem található felhasználói adat
    echo "Nincs felhasználói adat.";
    exit;
}

// Az SQL lekérdezés módosítása a felhasználó pozíciójának megfelelően
switch ($userPosition) {
    case 'admin':
        $sql = "SELECT * FROM users WHERE work_id::varchar LIKE :searchTerm OR name LIKE :searchTerm OR email LIKE :searchTerm";
        break;
    case 'dekan':
        $sql = "SELECT * FROM users WHERE (work_id::varchar LIKE :searchTerm OR name LIKE :searchTerm OR email LIKE :searchTerm) AND kar = :kar";
        break;
    case 'tanszekvezeto':
        $sql = "SELECT * FROM users WHERE (work_id::varchar LIKE :searchTerm OR name LIKE :searchTerm OR email LIKE :searchTerm) AND kar = :kar AND szervezetszam = :szervezetszam";
        break;
    default:
        echo "Nincs jogosultsága ehhez a kereséshez.";
        exit;
}

// Az utasítás előkészítése és végrehajtása
$stmt = $conn->prepare($sql);
$stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
if (in_array($userPosition, ['dekan', 'tanszekvezeto'])) {
    $stmt->bindParam(':kar', $kar, PDO::PARAM_STR);
}
if ($userPosition == 'tanszekvezeto') {
    $stmt->bindParam(':szervezetszam', $szervezetszam, PDO::PARAM_STR);
}
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Keresési eredmények</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
<?php include "navigation_bar-top.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "navigation_bar-side.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <div class="search-results">
                <h1>Keresési eredmények: "<?php echo htmlspecialchars($searchQuery); ?>"</h1>

                <?php if (is_array($results) && count($results) > 0): ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Munka ID</th>
                                <th>Név</th>
                                <th>Email</th>
                                <th>Kar</th>
                                <th>Szervezetszám</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <!-- Munka ID kattinthatóvá tétele -->
                                    <td><a href="profile.php?work_id=<?php echo $row['work_id']; ?>"><?php echo htmlspecialchars($row['work_id']); ?></a></td>
                                    <!-- Név kattinthatóvá tétele -->
                                    <td><a href="profile.php?work_id=<?php echo $row['work_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></a></td>
                                    <!-- Email kattinthatóvá tétele -->
                                    <td><a href="profile.php?work_id=<?php echo $row['work_id']; ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
                                    <td><?php echo htmlspecialchars($row['kar']); ?></td>
                                    <td><?php echo htmlspecialchars($row['szervezetszam']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Nincs találat erre a keresésre: "<?php echo htmlspecialchars($searchQuery); ?>".</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="public/js/collapse.js"></script>
</body>
</html>

