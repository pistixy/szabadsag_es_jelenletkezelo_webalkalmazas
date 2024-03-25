<?php
// Az adatbázis kapcsolati fájl beillesztése
include "connect.php";

// Ellenőrizzük, hogy a munka azonosító (work_id) be van-e állítva a munkamenetben
if (isset($_SESSION['work_id'])) {
    // A munka azonosító (work_id) lekérése a munkamenetből
    $workId = $_SESSION['work_id'];

    try {
        // SQL utasítás előkészítése a felhasználók táblában lévő adatok kiválasztására a munka azonosító (work_id) alapján
        $sql = "SELECT * FROM users WHERE work_id = :work_id";
        $stmt = $conn->prepare($sql);

        // A munka azonosító (work_id) paraméter hozzárendelése
        $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);

        // A lekérdezés végrehajtása
        $stmt->execute();

        // Az adatok lekérése
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Az összesített érték kiszámítása
        $total = $row['payed_free'] + $row['payed_past_free'] + $row['payed_edu_free'] + $row['payed_award_free'] + $row['unpayed_dad_free'] + $row['unpayed_home_free'] + $row['unpayed_free'] +
                 $row['payed_requested'] + $row['payed_past_requested'] + $row['payed_edu_requested'] + $row['payed_award_requested'] + $row['unpayed_dad_requested'] + $row['unpayed_home_requested'] + $row['unpayed_requested'] +
                 $row['payed_planned'] + $row['payed_past_planned'] + $row['payed_edu_planned'] + $row['payed_award_planned'] + $row['unpayed_dad_planned'] + $row['unpayed_home_planned'] + $row['unpayed_planned'];

        // Az összesített érték megjelenítése
        echo $total;

    } catch (PDOException $e) {
        // Hibakezelés
        echo "Hiba: " . $e->getMessage();
    }
} else {
    // Amennyiben nincs munka azonosító a munkamenetben
    echo "Nincs valid munkamenet.";
}
?>
