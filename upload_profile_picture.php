<?php
session_start();

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

include "connect.php";

if (isset($_POST['profile_picture'])) {
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT WORKID FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $workId = $row['WORKID'];

        $uploadDir = "profile_pictures/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $newFileName = $workId . "." . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;

        $validImageTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array(strtolower($fileExtension), $validImageTypes)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE email = ?");
                $stmt->bind_param("ss", $newFileName, $email);

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
