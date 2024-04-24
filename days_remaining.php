<?php
include "connect.php";// Az adatbázis kapcsolatfájl beillesztése
// Ellenőrizzük, hogy a munka azonosító (work_id) be van-e állítva a munkamenetben
if (isset($_SESSION['work_id'])) {
    // A munka azonosító (work_id) lekérése a munkamenetből
    $workId = $_SESSION['work_id'];
    try {
        // Készítsünk egy SQL utasítást a fizetett szabadságok lekérdezésére az adatbázisból
        $sql = "SELECT paid_free FROM users WHERE work_id = :work_id";
        $stmt = $conn->prepare($sql);
        // A munka azonosító (work_id) paraméter összekapcsolása
        $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);
        $stmt->execute();// A lekérdezés végrehajtása
        $row = $stmt->fetch(PDO::FETCH_ASSOC);// Az adatok lekérése
        $total = $row['paid_free'];// A teljes összeg kiszámítása
        echo $total;// Az összeg kiíratása
    } catch (PDOException $e) {
        // Kezeljük a hibákat
        echo "Hiba: " . $e->getMessage();
    }
} else {
    // A munka azonosító (work_id) nincs beállítva a munkamenetben
    echo "Nincs munka azonosító a munkamenetben.";
}
?>

