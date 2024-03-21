<?php
// Munkamenet ellenőrző fájl beillesztése
include "session_check.php";
// Adatbázis kapcsolatfájl beillesztése
include "connect.php";

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

// Változók inicializálása
$users = [];
$message = '';
$yesterday = date('Y-m-d', strtotime('-1 day'));
// Űrlap beküldésének kezelése
if (isset($_POST['submit'])) {
    // Szerezd be a kiválasztott dátumot az űrlapról
    $selectedDate = $_POST['selectedDate'];
} else {
        // Állítsa be az alapértelmezett dátumot
        $selectedDate = $yesterday;
}
// Lekérdezés a naptár táblából a kiválasztott dátumhoz tartozó work_id rekordok lekéréséhez a day_status = "work_day" esetén
$stmt = $conn->prepare("SELECT work_id, calendar_id, day_status FROM calendar WHERE date = :selectedDate AND day_status = 'work_day'");
$stmt->bindParam(':selectedDate', $selectedDate);
$stmt->execute();
$calendarData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// A lekért work_id rekordokhoz tartozó felhasználók lekérése
if (!empty($calendarData)) {
    $placeholders = rtrim(str_repeat('?,', count($calendarData)), ',');
    $workIDs = array_column($calendarData, 'work_id');
    $sql = "SELECT * FROM users WHERE work_id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($workIDs);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $message = "Nincsenek dolgozók a kiválasztott dátumon(".$selectedDate.").";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jelentés</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="body-container">
    <div class="navbar">
        <?php include "nav-bar.php"; ?>
    </div>
    <div class="main-content">
        <div class="jelenletiiv">
            <h1>Válasszon dátumot!</h1>
            <form action="" method="post">
                <label for="selectedDate">Dátum:</label>
                <input type="date" id="selectedDate" name="selectedDate" value="<?php echo htmlspecialchars($selectedDate); ?>" required>
                <input type="submit" value="Küldés" name="submit">
            </form>
        </div>
        <div class="report-missing-form">
            <?php if (!empty($users)): ?>
                <h2>Dolgozók a <?php echo $selectedDate; ?> napon</h2>
                <table border="1">
                    <thead>
                    <tr>
                        <th>Work ID</th>
                        <th>Név</th>
                        <th>Kar</th>
                        <th>Szervezetszám</th>
                        <th>Email</th>
                        <th>Státusz</th>
                        <th>Műveletek</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $key => $user): ?>
                        <tr>
                            <td><?php echo $user['work_id']; ?></td>
                            <td><?php echo $user['name']; ?></td>
                            <td><?php echo $user['kar']; ?></td>
                            <td><?php echo $user['szervezetszam']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $calendarData[$key]['day_status']; ?></td>
                            <td>
                                <form action="report_missing.php" method="post">
                                    <input type="hidden" name="calendar_id" value="<?php echo $calendarData[$key]['calendar_id']; ?>">
                                    <button type="submit" name="perform_action">Igazolatlan távollét jelentése <?php echo $calendarData[$key]['calendar_id']; ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif (!empty($message)): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
</body>
</html>
