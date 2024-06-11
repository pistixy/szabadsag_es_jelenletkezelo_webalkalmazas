<?php
// Adatbázis kapcsolatfájl beillesztése
include "connect.php";
include "check_login.php";
include "session_check.php";

// Ellenőrizzük, hogy az űrlap elküldve lett-e
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ellenőrizzük, hogy minden szükséges mezőt kitöltöttek-e
    if (isset($_POST['date']) && isset($_POST['price']) && isset($_FILES['receipt']) && isset($_SESSION['work_id'])) {
        // Munkaazonosító lekérése a munkamenetből
        $work_id = $_SESSION['work_id'];

        $date = $_POST['date'];
        $price = $_POST['price'];

        // Fájlfeltöltés kezelése
        $targetDir = "storage/passes/";
        $targetFile = $targetDir . basename($_FILES["receipt"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Ellenőrizzük, hogy a fájl PDF-e
        if ($fileType != "pdf") {
            echo "Sajnáljuk, csak PDF fájlok engedélyezettek.";
            $uploadOk = 0;
        }
        // Ellenőrizzük a fájlméretet (5 MB-ra korlátozva)
        if ($_FILES["receipt"]["size"] > 5000000) {
            echo "Sajnáljuk, a fájl túl nagy.";
            $uploadOk = 0;
        }
        // Ellenőrizzük, hogy volt-e hiba a feltöltésnél
        if ($uploadOk == 0) {
            echo "Sajnáljuk, a fájlt nem sikerült feltölteni.";
        } else {
            // Ha minden rendben van, próbáljuk meg feltölteni a fájlt
            if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $targetFile)) {
                // Fájl sikeresen feltöltve, most illesszük be az adatokat az adatbázisba
                $filename = basename($_FILES["receipt"]["name"]); // Fájlnév tárolása változóban
                $sql = "INSERT INTO public.commute (work_id, how, date, filename, price) 
                        VALUES (:work_id, 'Pass', :date, :filename, :price)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':work_id', $work_id);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':filename', $filename); // Itt használjuk a változót
                $stmt->bindParam(':price', $price);

                if ($stmt->execute()) {
                    echo '<script>alert("Az adatok sikeresen rögzítve!");</script>';
                    echo '<script>window.history.back();</script>'; // Visszatérés az előző oldalra
                } else {
                    echo "Hiba az adatok beszúrása közben az adatbázisba.";
                }
            } else {
                echo "Sajnáljuk, hiba történt a fájl feltöltése során.";
            }
        }
    } else {
        echo "Minden mezőt ki kell tölteni.".var_dump($_POST);
    }
} else {
    echo "Érvénytelen kérési módszer.";
}
?>
