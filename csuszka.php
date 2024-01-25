<?php
include "connect.php";
include "session_check.php";

// Determine whose details to show: either from URL (if admin and provided) or from session
if (isset($_GET['work_id']) && $_SESSION['isAdmin']) {
    $workIdToCheck = $_GET['work_id'];
} else {
    $workIdToCheck = $_SESSION['work_id'];
}

$sql = "SELECT free, planned, taken, requested FROM users WHERE work_id = :work_id";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bindParam(':work_id', $workIdToCheck, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $free = $result['free'];
        $planned = $result['planned'];
        $taken = $result['taken'];
        $requested = $result['requested'];
    } else {
        echo "User not found in the database.";
    }
} else {
    echo "Error with the database query: " . $conn->errorInfo()[2];
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        .csuszka {
            margin: 0 auto;
            width: 100%;
            background-color: #f0f0f0;
            height: 20px;
            border: 1px solid #ccc;
            display: flex;
        }

        .section {
            text-align: center;
            color: #fff;
            font-weight: bold;
            padding: 5px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .section:hover .tooltiptext {
            display: block;
        }

        .free {
            flex: <?php echo $free; ?>;
            background-color: green;
        }

        .taken {
            flex: <?php echo $taken; ?>;
            background-color: red;
        }

        .requested {
            flex: <?php echo $requested; ?>;
            background-color: lightsalmon;
        }

        .planned {
            flex: <?php echo $planned; ?>;
            background-color: lightgreen;
        }

        .tooltiptext {
            display: none;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #000;
            color: #fff;
            padding: 5px;
            border-radius: 4px;
            text-align: center;
        }

        .fieldset {
            width: 60%;
            margin: 0 auto;
            background: #dddddd;
        }
    </style>
</head>
<body>
<fieldset class="fieldset">
    <legend>Fizetett szabadságok állása</legend>
    <div class="csuszka">
        <div class="section free">
            <span class="tooltiptext">Fel nem használt szabadnapok száma: <?php echo $free; ?></span>
        </div>
        <div class="section planned">
            <span class="tooltiptext">Betervezett és már engedélyezett szabadnapok száma: <?php echo $planned; ?></span>
        </div>
        <div class="section requested">
            <span class="tooltiptext">Betervezett de még nem engedélyezett szabadnapok száma: <?php echo $requested; ?></span>
        </div>
        <div class="section taken">
            <span class="tooltiptext">Felhasznált szabadnapok száma (múltbeli): <?php echo $taken; ?></span>
        </div>

    </div>
</fieldset>
</body>
</html>
