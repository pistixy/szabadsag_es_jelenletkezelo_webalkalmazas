<?php
//nincs használatban
// Munkamenet ellenőrző fájl beillesztése
include "session_check.php";
// Adatbázis kapcsolatfájl beillesztése
include "connect.php";
// Navigációs sáv beillesztése
include "nav-bar.php";

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve, és van-e munkaazonosítója
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    // Ha valamelyik feltétel nem teljesül, átirányítjuk a bejelentkezési oldalra
    header("Location: login_form.php");
    exit;
}

// Kérés részleteinek tárolására szolgáló tömb inicializálása
$requestDetails = [];

// Ellenőrizzük, hogy egy kérelem azonosítója lett-e átadva a válasz kezdeményezéséhez
if (isset($_GET['request_id'])) {
    // Kérelem azonosítójának lekérése a GET paraméterek közül
    $requestId = $_GET['request_id'];

    // Kérés részleteinek lekérése és tárolása megjelenítés céljából
    $requestSql = "SELECT * FROM requests WHERE request_id = :requestId";
    $requestStmt = $conn->prepare($requestSql);
    $requestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
    $requestStmt->execute();
    $requestDetails = $requestStmt->fetch(PDO::FETCH_ASSOC);

    // Ellenőrizzük, hogy létezik-e a kérelem azonosítójához tartozó kérés
    if (!$requestDetails) {
        echo "A kérelem nem található.";
        exit;
    }
} else {
    echo "Nincs megadva kérelem.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kérelemre válaszadás</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Kérelemre válaszadás</h1>

<?php if ($requestDetails): ?>
    <!-- Kérelem részleteinek megjelenítése, ha léteznek -->
    <div class="request-details">
        <h2>Kérelem részletei</h2>
        <p><strong>Kérelem azonosítója:</strong> <?php echo htmlspecialchars($requestDetails['request_id']); ?></p>
        <p><strong>Állapot:</strong> <?php echo htmlspecialchars($requestDetails['requested_status']); ?></p>
        <p><strong>Üzenet:</strong> <?php echo htmlspecialchars($requestDetails['message']); ?></p>
        <p><strong>Időbélyegző:</strong> <?php echo htmlspecialchars($requestDetails['timestamp']); ?></p>
        <!-- Egyéb részletek beillesztése szükség esetén -->
    </div>
<?php endif; ?>

<!-- Válaszadó űrlap -->
<form action="respond_request.php" method="post">
    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($requestId); ?>">
    <div class="form-group">
        <label for="message">Válaszod:</label>
        <textarea id="message" name="message" required></textarea>
    </div>
    <input type="submit" value="Válasz küldése">
</form>

<?php include "footer.php"; ?>

</body>
</html>
