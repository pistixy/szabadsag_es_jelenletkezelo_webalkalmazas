<?php
session_start();
include "connect.php";

$name = $_POST["name"];
$email = $_POST["email"];
$password = $_POST["password"];
$passwordRepeat = $_POST["jelszoujra"];
$cim = $_POST["cim"];
$adoazonosito = $_POST["adoazonosito"];
$szervezetszam = $_POST["szervezetszam"];
$alkalmazottikartyaszama = $_POST["alkalmazottikartyaszama"];

// Check if the passwords match
if ($password !== $passwordRepeat) {
    echo "A jelszavak nem egyeznek. <a href='registration_form.php'>Próbálkozás újra</a>";
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$joined = date("Y-m-d H:i:s");
$admin = 0;
$position = 0;

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "A megadott email címmel már regisztráltak! Kérjük, használjon másik email címet. <a href='registration_form.php'>Próbálkozás újra</a>";
} else {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, cim, adoazonosito, szervezetszam, alkalmazottikartya, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $name, $email, $hashed_password, $cim, $adoazonosito, $szervezetszam, $alkalmazottikartyaszama, $position);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['email'] = $email;

        include "fill_up_calendar_when_register.php";
        include "edit_calendar_with_holidays.php";
        include "login.php";

        header("Location: index.php");
        exit;
    } else {
        echo "Sikeres regisztráció!";
    }
}
?>
