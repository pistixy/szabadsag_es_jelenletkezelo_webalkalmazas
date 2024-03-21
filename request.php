<?php
// Munkamenet ellenőrző fájl beillesztése
include "session_check.php";
// Adatbázis kapcsolatfájl beillesztése
include "connect.php";
// Navigációs sáv beillesztése
include "nav-bar.php";

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve, van-e munkaazonosítója, és van-e request_id a GET paraméterek között
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id']) || !isset($_GET['request_id'])) {
    // Ha bármelyik feltétel nem teljesül, átirányítjuk a bejelentkezési oldalra
    header("Location: login_form.php");
    exit;
}

// A request_id lekérése a GET paraméterek közül
$requestId = $_GET['request_id'];

// Kérések részleteinek lekérése az adatbázisból
$requestDetailsSql = "SELECT * FROM requests WHERE request_id = :requestId";
$requestDetailsStmt = $conn->prepare($requestDetailsSql);
$requestDetailsStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
$requestDetailsStmt->execute();
$requestDetails = $requestDetailsStmt->fetch(PDO::FETCH_ASSOC);

// SQL utasítás előkészítése az összes üzenet lekéréséhez a megadott request_id-re,
// és csatlakoztatjuk a naptár táblával a dátum lekéréséhez
$sql = "SELECT m.*, c.date 
        FROM messages m
        LEFT JOIN requests r ON m.request_id = r.request_id
        LEFT JOIN calendar c ON r.calendar_id = c.calendar_id
        WHERE m.request_id = :requestId 
        ORDER BY m.timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kérelmek üzenetei</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php if ($requestDetails): ?>
    <!-- Kérelem részleteinek megjelenítése -->
    <h1>Részletek a kérelem azonosítója: <?php echo htmlspecialchars($requestId); ?></h1>
    <p>Kért státusz: <?php echo htmlspecialchars($requestDetails['requested_status']); ?></p>
    <p>Üzenet: <?php echo htmlspecialchars($requestDetails['message']); ?></p>
    <p>Állapot: <?php echo htmlspecialchars($requestDetails['request_status']); ?></p>
    <!-- További részletek hozzáadása szükség esetén -->
<?php else: ?>
    <p>A kérelem részletei nem találhatók.</p>
<?php endif; ?>

<h2>Üzenetek ehhez a kérelemhez</h2>

<?php if (!empty($messages)): ?>
    <!-- Üzenetek táblázata -->
    <table>
        <tr>
            <th>Üzenet azonosítója</th>
            <th>Küldő munkaazonosítója</th>
            <th>Üzenet</th>
            <th>Dátum</th>
            <th>Időbélyegző</th>
        </tr>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td><?php echo htmlspecialchars($message['message_id']); ?></td>
                <td><?php echo htmlspecialchars($message['from_work_id']); ?></td>
                <td><?php echo htmlspecialchars($message['message']); ?></td>
                <td><?php echo htmlspecialchars($message['date']); ?></td>
                <td><?php echo htmlspecialchars($message['timestamp']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Nincsenek üzenetek ehhez a kérelemhez.</p>
<?php endif; ?>

<?php include "footer.php"; ?>

</body>
</html>
