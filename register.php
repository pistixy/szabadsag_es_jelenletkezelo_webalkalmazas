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
$letterCode = $_POST["letterCode"];
$payed_free=20; //20
$payed_requested=0;
$payed_planned=0;
$payed_taken=0;
$payed_past_free=0;
$payed_past_requested=0;
$payed_past_planned=0;
$payed_past_taken=0;
$payed_edu_free=5; //5
$payed_edu_requested=0;
$payed_edu_planned=0;
$payed_edu_taken=0;
$payed_award_free=0;
$payed_award_requested=0;
$payed_award_planned=0;
$payed_award_taken=0;
$unpayed_sickness_taken=0;
$unpayed_uncertified_taken=0;
$unpayed_dad_free=0;
$unpayed_dad_requested=0;
$unpayed_dad_planned=0;
$unpayed_dad_taken=0;
$unpayed_home_free=20; //20
$unpayed_home_requested=0;
$unpayed_home_planned=0;
$unpayed_home_taken=0;
$unpayed_free=20; //20
$unpayed_requested=0;
$unpayed_planned=0;
$unpayed_taken=0;


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
        name, email, password, cim, adoazonosito, szervezetszam, alkalmazottikartya, 
        kar, payed_free, payed_requested, payed_planned, payed_taken, 
        payed_past_free, payed_past_requested, payed_past_planned, payed_past_taken,
        payed_edu_free, payed_edu_requested, payed_edu_planned, payed_edu_taken, 
        payed_award_free, payed_award_requested, payed_award_planned, payed_award_taken, 
        unpayed_sickness_taken, unpayed_uncertified_taken, unpayed_dad_free, unpayed_dad_requested, 
        unpayed_dad_planned, unpayed_dad_taken, unpayed_home_free, unpayed_home_requested, 
        unpayed_home_planned, unpayed_home_taken, unpayed_free, unpayed_requested, unpayed_planned, unpayed_taken
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $name, $email, $hashed_password, $cim, $adoazonosito, $szervezetszam,
        $alkalmazottikartyaszama, $letterCode, $payed_free, $payed_requested,
        $payed_planned, $payed_taken, $payed_past_free, $payed_past_requested,
        $payed_past_planned, $payed_award_taken, $payed_edu_free, $payed_edu_requested,
        $payed_edu_planned, $payed_edu_taken, $payed_award_free, $payed_award_requested,
        $payed_award_planned, $payed_award_taken, $unpayed_sickness_taken, $unpayed_uncertified_taken,
        $unpayed_dad_free, $unpayed_dad_requested, $unpayed_dad_planned, $unpayed_dad_taken,
        $unpayed_home_free, $unpayed_home_requested, $unpayed_home_planned, $unpayed_home_taken,
        $unpayed_free, $unpayed_requested, $unpayed_planned, $unpayed_taken
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
