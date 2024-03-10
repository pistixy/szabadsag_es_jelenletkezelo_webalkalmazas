<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
    $email = $_SESSION['email'];


    include "connect.php";


    try {
        $stmt = $conn->prepare("SELECT position FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $_SESSION['position']=$result['position'];

            if ($result['position'] == 'user'){
                $_SESSION['is_user'] =true;
            }else{
                $_SESSION['is_user'] = false;
            }
        } else {
            echo "No result found for email: $email";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
/*echo $_SESSION['position'];
if($_SESSION['is_user'] == true) {
    echo $_SESSION['position'];
}
if($_SESSION['is_user'] == false) {
    echo $_SESSION['position'];
}*/

