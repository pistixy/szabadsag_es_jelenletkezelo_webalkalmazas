<?php
// Munkamenet ellenőrzése
include "session_check.php";

// Ha nincs bejelentkezve, átirányítás a bejelentkezési oldalra
if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Adatbázis kapcsolat létrehozása
include "connect.php";

// Ha a feltöltési űrlap elküldésre került és van feltöltött fájl
if (isset($_POST['upload_profile_picture']) && isset($_FILES['profile_picture'])) {
    $email = $_SESSION['email'];

    // Felhasználó munkaidjének lekérdezése az adatbázisból az e-mail cím alapján
    $stmt = $conn->prepare("SELECT work_id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetchAll();

    // Ha találat van az adatbázisban az e-mail cím alapján
    if (count($result) == 1) {
        $row = $result[0];
        $workId = $row['work_id'];

        // Feltöltési mappa létrehozása, ha nem létezik
        $uploadDir = "profile_pictures/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Fájlkiterjesztés és új fájlnév létrehozása
        $fileExtension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $newFileName = $workId . "." . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;

        // Engedélyezett képfájltípusok
        $validImageTypes = ["jpg", "jpeg", "png", "gif"];

        // Ha a fájlkiterjesztés érvényes
        if (in_array($fileExtension, $validImageTypes)) {
            // Fájl feltöltése a célhelyre
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                // Adatbázis frissítése a profilkép elérési útvonalával
                $stmt = $conn->prepare("UPDATE users SET profile_picture = :profile_picture WHERE email = :email");
                $stmt->bindParam(':profile_picture', $newFileName);
                $stmt->bindParam(':email', $email);

                // Adatbázis frissítése és átirányítás a profil oldalra
                if ($stmt->execute()) {
                    header("Location: profile.php");
                    exit;
                } else {
                    echo "Hiba az adatbázis frissítésekor.";
                }
            } else {
                echo "Hiba a kép feltöltésekor.";
            }
        } else {
            echo "Érvénytelen fájltípus. Kérlek tölts fel egy érvényes képfájlt.";
        }
    } else {
        echo "Felhasználó nem található az adatbázisban.";
    }
}
?>
