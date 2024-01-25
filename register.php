<?php
include "session_check.php";
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
$free=20;
$taken=0;
$requested=0;
$planned=0;

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bindParam(1, $email);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($result) > 0) {
    echo "A megadott email címmel már regisztráltak! Kérjük, használjon másik email címet. <a href='registration_form.php'>Próbálkozás újra</a>";
    exit;
} else {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, cim, adoazonosito, szervezetszam, alkalmazottikartya, position, free, taken, requested, planned) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $email);
    $stmt->bindParam(3, $hashed_password);
    $stmt->bindParam(4, $cim);
    $stmt->bindParam(5, $adoazonosito);
    $stmt->bindParam(6, $szervezetszam);
    $stmt->bindParam(7, $alkalmazottikartyaszama);
    $stmt->bindParam(8, $position, PDO::PARAM_INT);
    $stmt->bindParam(9, $free, PDO::PARAM_INT);
    $stmt->bindParam(10, $taken, PDO::PARAM_INT);
    $stmt->bindParam(11, $requested, PDO::PARAM_INT);
    $stmt->bindParam(12, $planned, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['email'] = $email;

        // User login check and session setting
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            if (password_verify($password, $result['password'])) {
                $_SESSION['email'] = $result['email'];
                $_SESSION['logged'] = true;
                $_SESSION['work_id'] = $result['work_id'];

                $_SESSION['isAdmin'] = isset($result['admin']) && $result['admin'] == 1;
            }
        }

        include "fill_up_calendar_when_register.php";
        include "edit_calendar_with_holidays.php";

        header("Location: index.php");
        exit;
    } else {
        echo "Hiba történt a regisztráció során.";
    }
}
?>
