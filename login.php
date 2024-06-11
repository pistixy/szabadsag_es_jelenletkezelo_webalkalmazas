<?php
include "session_check.php";
include "app/config/connect.php";

// Ellenörizzök, hogy az ürlap el lett-e küldve
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ellenörizzök, hogy az adatok az ürlapon el lettek-e küldve
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $result = $stmt->fetchAll();

        if ($result) {
            $row = $result[0];

            // Jelszó ellenörzése
            if (password_verify($password, $row['password'])) {

                //Session változók beállítása
                $_SESSION['email'] = $row['email'];
                $_SESSION['logged'] = true;
                $_SESSION['work_id'] = $row['work_id'];

                // adminisztrátori jog ellenörzése
                if((isset($row['position']) && $row['position'] == "admin")){
                    $_SESSION['isAdmin']=true;
                    $_SESSION['position']=$row['position'];
                    //echo "admin true";//debug
                }else{
                    $_SESSION['isAdmin']=false;
                    $_SESSION['position']=$row['position'];
                    //echo "admin false";//debug
                }
                            // Fömenüre irányítás
                header("Location: index.php");
                exit;
            }
        }
        // Ha helytelenül töltötte ki a falhasználó az adatait
        echo "Helytelen felhasználónév vagy jelszó! <a href='login_form.php'>Bejelentkezés újra</a>";
    } else {
        // Ha nem töltötte ki a felhasználó a mezök valamelyikét
        echo "Hiányzó adatok! Kérjük, töltse ki az összes mezőt. <a href='login_form.php'>Vissza a bejelentkezéshez</a>";
    }
} else {
    // Form nincs elküldve
    header("Location: login_form.php");
    exit;
}
?>
