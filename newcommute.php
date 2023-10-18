<?php
session_start();
include "connect.php"; // Include your database connection

if (isset($_POST['upload_receipt'])) {
    $date = $_POST['date'];
    $honnan = $_POST['honnan'];
    $hova = $_POST['hova'];
    $how = $_POST['how'];
    if ($how==="Car"){
        $price=0;
        $km=0;
    }
    else  {
        $price=$_POST['price'];
    }

    if($how==="Oda_Vissza"){
        $km = $_POST['km'];
        $price=0;
    }
    else{
        $km=0;
    }



    $email = $_SESSION['email']; // Assuming you have a user session
    $WORKID = $_SESSION['WORKID']; // Assuming you have a user session

    // Handle PDF file upload
    $uploadPath = "tickets/";
    $fileName = null;

    if (isset($_FILES['receipt']) and $_POST['how']==="PublicTransport") {
        $fileTmpName = $_FILES['receipt']['tmp_name'];
        $fileType = $_FILES['receipt']['type'];

        if ($fileType === "application/pdf") {
            $fileName = $_FILES['receipt']['name'];
            $destination = $uploadPath . $fileName;

            if (move_uploaded_file($fileTmpName, $destination)) {
                echo "File uploaded successfully!";
            } else {
                echo "Error moving the uploaded file. Check folder permissions.";
                exit;
            }
        } else {
            echo "Invalid file type. Please upload a PDF.";
            exit;
        }
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO commute (WORKID,  honnan, hova, how,date, filename,price, km) VALUES (?,?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssii", $WORKID,  $honnan, $hova,  $how,$date, $fileName,$price, $km);

    if ($stmt->execute()) {
        echo "Data recorded successfully!";
    } else {
        echo "Error recording data: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: comingtowork.php");
    exit;
}
?>
