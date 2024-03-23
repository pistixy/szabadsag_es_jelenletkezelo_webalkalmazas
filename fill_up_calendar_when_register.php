<?php
if (session_status() === PHP_SESSION_NONE) {
    include "session_check.php"; // Ellenőrizze, hogy a munkamenet aktív-e
}
include "connect.php"; // Adatbáziskapcsolat fájl beillesztése

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // A helyes oszlopnevet használja az Ön PostgreSQL adatbázisából
    $stmt = $conn->prepare("SELECT work_id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if (count($result) == 1) {
        $row = $result[0];
        $userWorkId = $row['work_id']; // Állítsa be az oszlopnevet, hogy megfeleljen az adatbázisának

        // Naptáradatok kitöltése az elmúlt 1 évben és a következő 10 évben
        $currentDate = new DateTime();
        $pastLimit = 365; // Adatok kitöltése az elmúlt 1 évben
        $futureLimit = 365 * 20; // Adatok kitöltése a következő 20 évre

        // Múltbeli naptár adatok kitöltése
        for ($i = 1; $i <= $pastLimit; $i++) {
            $date = date("Y-m-d", strtotime($currentDate->format("Y-m-d") . " - " . $i . " days"));
            $day_status = date('N', strtotime($date)) <= 5 ? "work_day" : "weekend";

            $stmt = $conn->prepare("INSERT INTO calendar (work_id, date, day_status ) VALUES (:work_id, :date, :day_status )");
            $stmt->bindParam(':work_id', $userWorkId);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':day_status', $day_status);

            if ($stmt->execute()) {
                // Sikeres beillesztés
            } else {
                echo "Hiba az adatok beillesztésénél a dátumhoz: " . $date . "<br>";
                echo "Hiba: " . $stmt->errorInfo()[2] . "<br>"; // PDO hibainformáció
            }
        }

        // Jövőbeli naptár adatok kitöltése
        for ($i = 0; $i < $futureLimit; $i++) {
            $date = date("Y-m-d", strtotime($currentDate->format("Y-m-d") . " + " . $i . " days"));
            $day_status = date('N', strtotime($date)) <= 5 ? "work_day" : "weekend";

            $stmt = $conn->prepare("INSERT INTO calendar (work_id, date, day_status ) VALUES (:work_id, :date, :day_status )");
            $stmt->bindParam(':work_id', $userWorkId);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':day_status', $day_status);

            if ($stmt->execute()) {
                // Sikeres beillesztés
            } else {
                echo "Hiba az adatok beillesztésénél a dátumhoz: " . $date . "<br>";
                echo "Hiba: " . $stmt->errorInfo()[2] . "<br>"; // PDO hibainformáció
            }
        }

        echo "Naptár visszamenőleg és a jövőre nézve feltöltve.";
    } else {
        echo "A felhasználó nem található az adatbázisban.";
    }
} else {
    echo "Nincs beállított felhasználói munkamenet.";
}
?>
