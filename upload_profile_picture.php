<?php
session_start();

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

include "connect.php";

if (isset($_POST['upload_profile_picture']) && isset($_FILES['profile_picture'])) {
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT work_id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if (count($result) == 1) {
        $row = $result[0];
        $workId = $row['work_id'];

        $uploadDir = "profile_pictures/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $newFileName = $workId . "." . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;

        $validImageTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array($fileExtension, $validImageTypes)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                $stmt = $conn->prepare("UPDATE users SET profile_picture = :profile_picture WHERE email = :email");
                $stmt->bindParam(':profile_picture', $newFileName);
                $stmt->bindParam(':email', $email);

                if ($stmt->execute()) {
                    header("Location: profile.php");
                    exit;
                } else {
                    echo "Error updating the database.";
                }
            } else {
                echo "Error uploading the picture.";
            }
        } else {
            echo "Invalid file type. Please upload a valid image.";
        }
    } else {
        echo "User not found in the database.";
    }
}
?>

