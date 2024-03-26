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
$position='user';
$letterCode = $_POST["letterCode"];
$paid_free=20; //20
$paid_requested=0;
$paid_planned=0;
$paid_taken=0;



// Check if the passwords match
if ($password !== $passwordRepeat) {
    echo "A jelszavak nem egyeznek. <a href='registration_form.php'>Próbálkozás újra</a>";
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bindParam(1, $email);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($result) > 0) {
    echo "A megadott email címmel már regisztráltak! Kérjük, használjon másik email címet. <a href='registration_form.php'>Próbálkozás újra</a>";
    exit;
} else {
    $stmt = $conn->prepare("INSERT INTO users (
        name, email, password, cim, adoazonosito, szervezetszam, alkalmazottikartya, position,
        kar, paid_free, paid_requested, paid_planned, paid_taken
        
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $name, $email, $hashed_password, $cim, $adoazonosito, $szervezetszam,
        $alkalmazottikartyaszama,$position, $letterCode, $paid_free, $paid_requested,
        $paid_planned, $paid_taken
    ]);

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
