<?php

include "connect.php";

$name = $_POST["name"];
$surname = $_POST["surname"];
$email = $_POST["email"];
$password = $_POST["password"];
$birthdate = $_POST["birthday"];
$phone = $_POST["phone"];


try {
    $birthdate = new DateTime($birthdate);
} catch (Exception $e) {
} // Convert to DateTime object
$birthdateString = $birthdate->format('Y-m-d'); // Format as string in 'Y-m-d' format
$today = new DateTime();
$age = $today->diff($birthdate)->y;


if ($age < 18) {
    echo "Az oldal használatához legalább 18 évesnek kell lenned! <a href='registration_form.php'>Próbálkozás újra</a>";
} else {
    $temp = $_POST["email"];
    $temparray = explode("@", $temp);
    $usern = $temparray[0];
    $joined = date("Y-m-d H:i:s");
    $admin = 0;
    $position =0;

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "A megadott email címmel már regisztráltak! Kérjük, használjon másik email címet. <a href='registration_form.php'>Próbálkozás újra</a>";
    } else {
        $jelszoujra = $_POST["jelszoujra"];
        if ($jelszoujra !== $password) {
            echo "A jelszavak nem egyeznek, <a href='registration_form.php'>Próbálja újra</a>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (surname, name, email, password, phone, birthdate, admin, joined, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssisi", $surname, $name, $email, $hashed_password, $phone, $birthdateString, $admin, $joined, $position);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "Sikeres regisztráció!";
                include_once "login.php";
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    }
}

