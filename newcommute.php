<?php
include "session_check.php";
include "connect.php";
include "varosok.php";

$minkm = 5;
$maxkm = 1000;
$minprice = 125;
$maxprice = 50000;
$currentDate = date("Y-m-d");
$mindate = date("Y-m-d", strtotime("2020-01-01"));

if (isset($_POST['upload_receipt'])) {
    $date = $_POST['date'];
    /*$honnan = $_POST['honnan'];
    $hova = $_POST['hova'];*/
    $how = $_POST['how'];

    $work_id = $_SESSION['work_id'];
    $price = null;
    $km = null;

    try {
        $stmt = $conn->prepare("SELECT * FROM calendar WHERE date = :date AND work_id = :work_id AND day_status = 'work_day'");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':work_id', $work_id);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) < 1) {
            echo "Csak olyan napra vehet fel munkábajárást, amelyiken dolgozott is!";
            exit;
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }

    // Check if the date already exists in the commute table for the same work_id
    try {
        $stmt = $conn->prepare("SELECT * FROM commute WHERE date = :date AND work_id = :work_id");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':work_id', $work_id);
        $stmt->execute();
        $existingCommutes = $stmt->fetchAll();

        if (!empty($existingCommutes)) {
            echo "Ez a dátum már szerepel a rögzített munkábajárási adatok között!";
            exit;
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        exit;
    }



    if ($how === "PublicTransport") {
        $price = $_POST['price'];
        if ($price < $minprice || $price > $maxprice) {
            echo "Valós ár értéket adjon meg!";
            exit;
        }
    } /*elseif ($how === "Oda_Vissza") {
        $km = $_POST['km'];
        if ($km < $minkm || $km > $maxkm) {
            echo "Valós kilóméter értéket adjon meg!";
            exit;
        }
    }*/

    if ($date > $currentDate || $date < $mindate) {
        echo "Csak ".$mindate. " és ".$currentDate. " között adhat meg dátumokat!";
        exit;
    }

    /*if (!in_array(ucfirst(strtolower($honnan)), $varosok) || !in_array(ucfirst(strtolower($hova)), $varosok)) {
        echo "Érvénytelen városnév! A városnak a következők közül kell lennie: " . implode(", ", $varosok);
        exit;
    }*/

    $fileName = null;

    // File upload handling for public transport receipts
    if ($how === "PublicTransport" && isset($_FILES['receipt'])) {
        $fileTmpName = $_FILES['receipt']['tmp_name'];
        $fileType = mime_content_type($fileTmpName);

        if ($fileType === "application/pdf") {
            $uploadPath = "tickets/";
            $fileName = $work_id . "_" . basename($_FILES['receipt']['name']);
            $destination = $uploadPath . $fileName;

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if (!move_uploaded_file($fileTmpName, $destination)) {
                echo "Error moving the uploaded file. Check folder permissions.";
                exit;
            }
        } else {
            echo "Invalid file type. Please upload a PDF.";
            exit;
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO commute (work_id, how, date, filename, price, km) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $work_id);
        $stmt->bindParam(2, $how);
        $stmt->bindParam(3, $date);
        $stmt->bindParam(4, $fileName);
        $stmt->bindParam(5, $price);
        $stmt->bindParam(6, $km);

        $stmt->execute();
        echo '<script>alert("Data recorded successfully!");</script>';
        echo '<script>window.history.back();</script>'; // Go back to the previous page
    } catch (PDOException $e) {
        echo "Error recording data: " . $e->getMessage();
    }

} else {
    header("Location: comingtowork.php");
    exit;
}
?>
