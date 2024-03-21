<?php
include "session_check.php";
include "connect.php";

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

// Ellenőrizzük, hogy egy kérekem azonosítója át lett-e adva a válaszhoz
if (isset($_POST['request_id']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $fromWorkID = $_SESSION['work_id']; // Válaszadó work_id-je
    $requestId = $_POST['request_id'];

    // Kérésben szereplő felhasználó munkaazonosítójának lekérése
    $requestSql = "SELECT work_id FROM requests WHERE request_id = :requestId";
    $requestStmt = $conn->prepare($requestSql);
    $requestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
    $requestStmt->execute();
    $requestDetails = $requestStmt->fetch(PDO::FETCH_ASSOC);

    if ($requestDetails) {
        $toWorkID = $requestDetails['work_id']; // Kérelmezö work_id-je
        $message = $_POST['message']; // A Kérelmezö által megadott üzenet
        $type = 'response to request'; // Fix típus
        $currentTimestamp = date('Y-m-d H:i:s'); // Aktuális időbélyeg

        // Válasz beszúrása az üzenetek táblába
        $insertSql = "INSERT INTO messages (from_work_id, to_work_id, to_position, type, request_id, message, timestamp) VALUES (:fromWorkID, :toWorkID, '', :type, :requestId, :message, :timestamp)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bindParam(':fromWorkID', $fromWorkID, PDO::PARAM_INT);
        $insertStmt->bindParam(':toWorkID', $toWorkID, PDO::PARAM_INT);
        $insertStmt->bindParam(':type', $type, PDO::PARAM_STR);
        $insertStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
        $insertStmt->bindParam(':message', $message, PDO::PARAM_STR);
        $insertStmt->bindParam(':timestamp', $currentTimestamp, PDO::PARAM_STR);
        $insertStmt->execute();

        // Átirányítás vagy sikerüzenet megjelenítése
        echo "A válaszod sikeresen elküldve.";
        // Átirányítás egy visszaigazoló oldalra vagy vissza a kérések listájára
    } else {
        echo "A kérés nem található.";
    }
} else {
    // Ha a form még nem lett elküldve, megjelenítjük
    if (isset($_GET['request_id'])) {
        $requestId = $_GET['request_id'];
        // Az űrlap megjelenítése
        ?>
        <!DOCTYPE html>
        <html lang="hu">
        <head>
            <meta charset="UTF-8">
            <title>Válasz küldése a kérésre</title>
            <link rel="stylesheet" href="styles.css">
        </head>
        <body>
        <?php include "nav-bar.php"; ?>
        <h1>Válasz küldése a kérésre</h1>
        <form action="respond_request.php" method="post">
            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($requestId); ?>">
            <label for="message">Üzenet:</label><br>
            <textarea id="message" name="message" required></textarea><br>
            <input type="submit" value="Válasz küldése">
        </form>
        <?php include "footer.php"; ?>
        </body>
        </html>
        <?php
    } else {
        echo "Nincs megadva kérelem_id, amire válaszolni lehetne.";
    }
}
?>
