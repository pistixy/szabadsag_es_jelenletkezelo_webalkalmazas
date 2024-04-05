<?php
include "session_check.php"; // Session ellenőrzése
include "connect.php"; // Adatbázis csatlakozás

//Változók inicializálása
$name = $email = $password = $passwordRepeat = $cim = '';
$adoazonosito = $szervezetszam = $alkalmazottikartyaszama = $letterCode = '';

// A filter_input használata a bemeneti adatok tisztítására és ellenőrzésére
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$passwordRepeat = filter_input(INPUT_POST, 'jelszoujra', FILTER_SANITIZE_STRING);
$cim = filter_input(INPUT_POST, 'cim', FILTER_SANITIZE_STRING);
$adoazonosito = filter_input(INPUT_POST, 'adoazonosito', FILTER_SANITIZE_STRING);
$szervezetszam = filter_input(INPUT_POST, 'szervezetszam', FILTER_SANITIZE_STRING);
$alkalmazottikartyaszama = filter_input(INPUT_POST, 'alkalmazottikartyaszama', FILTER_SANITIZE_STRING);
$letterCode = filter_input(INPUT_POST, 'letterCode', FILTER_SANITIZE_STRING);

$position = 'user'; // Pozíció beállítása alapértelmezetten 'user'
$paid_free = 20; // Alapértelmezett érték az új regisztrációkhoz
$paid_requested =0;
$paid_planned =0;
$paid_taken =0;


// További validálás (például ellenőrizni, hogy az email cím érvényes-e)
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Érvénytelen e-mail cím. <a href='registration_form.php'>Próbálkozás újra</a>";
    exit;
}

// Ellenőrizni, hogy a jelszavak megegyeznek-e
if ($password !== $passwordRepeat) {
    echo "A jelszavak nem egyeznek. <a href='registration_form.php'>Próbálkozás újra</a>";
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Jelszó hashelése

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?"); // SQL lekérdezés előkészítése
$stmt->bindParam(1, $email, PDO::PARAM_STR); // Paraméter hozzárendelése
$stmt->execute(); // Lekérdezés végrehajtása

// Ellenőrzés, hogy van-e már ilyen email címmel regisztrált felhasználó
if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "A megadott email címmel már regisztráltak! Kérjük, használjon másik email címet. <a href='registration_form.php'>Próbálkozás újra</a>";
    exit;
} else {
    // Új felhasználó adatainak beszúrása az adatbázisba
    $stmt = $conn->prepare("INSERT INTO users (
        name, email, password, cim, adoazonosito, szervezetszam, alkalmazottikartya, position,
        kar, paid_free, paid_requested, paid_planned, paid_taken
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $name, $email, $hashed_password, $cim, $adoazonosito, $szervezetszam,
        $alkalmazottikartyaszama, $position, $letterCode, $paid_free, $paid_requested,
        $paid_planned, $paid_taken
    ]);

    // Sikeres beszúrás ellenőrzése
    if ($stmt->rowCount() > 0) {
        // Session változók inicializálása
        $_SESSION['email'] = $email;
        $_SESSION['logged'] = false;
        $_SESSION['isAdmin'] = false;
        $_SESSION['is_user'] = true;

        // Felhasználó bejelentkezésének ellenőrzése és session beállítása
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Sikeres bejelentkezés esetén session beállítása
        if ($result && password_verify($password, $result['password'])) {
            $_SESSION['email'] = $result['email'];
            $_SESSION['logged'] = true;
            $_SESSION['work_id'] = $result['work_id'];
            $_SESSION['isAdmin'] = isset($result['admin']) && $result['admin'] == 1;

            // Regisztrációhoz kapcsolódó műveletek
            include "fill_up_calendar_when_register.php";
            include "edit_calendar_with_holidays.php";

            // Átirányítás abszolút URL-lal és HTTPS biztosításával
            header("Location: https://szabadsagkezelo.space/index.php");
            exit;
        } else {
            echo "Hiba történt a regisztráció során.";
        }
    } else {
        echo "Hiba történt a regisztráció során.";
    }
}
?>
