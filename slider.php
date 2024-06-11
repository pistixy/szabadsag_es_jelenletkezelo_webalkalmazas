<?php
include "connect.php";
include "session_check.php";

// ellenörizzik le, hogy kinek a adatai mutassuk, ha van továbbitott work_id akkor az ahhoz tartozo felhasználót, különben sajátot
if (isset($_GET['work_id']) && $_SESSION['isAdmin']) {
    $workIdToCheck = $_GET['work_id'];
} else {
    $workIdToCheck = $_SESSION['work_id'];
}

$sql = "SELECT * FROM users WHERE work_id = :work_id";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bindParam(':work_id', $workIdToCheck, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo "User not found in the database.";
        exit;
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
            color: #333; 
            font-weight: bold;
            padding: 0 5px;
            display: flex;
            align-items: center;
            justify-content: center; 
            position: relative;
            border-radius: 1px; 
        }

        .section:hover .tooltiptext {
            display: block;
        }

        .paid_free_csuszka {
            flex: <?php echo $result['paid_free']; ?>;
            background: linear-gradient(to right, #4caf50, #81c784);
        }
        .paid_requested_csuszka {
            flex: <?php echo $result['paid_requested']; ?>;
            background: linear-gradient(to right, #ffccbc, #ffab91);
        }

        .paid_planned_csuszka {
            flex: <?php echo $result['paid_planned']; ?>;
            background: linear-gradient(to right, #aed581, #dcedc8);
        }

        .paid_taken_csuszka {
            flex: <?php echo  $result['paid_taken']; ?>;
            background: linear-gradient(to right, #e57373, #ef9a9a);
        }



        .tooltiptext {
            display: none;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #555; /* Slightly lighter tooltip background for subtlety */
            color: #fff;
            padding: 5px;
            z-index: 1000;
            border-radius: 4px;
            text-align: center;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Tooltip shadow for prominence */
        }

        .fieldset {
            width: 70%; /* Adjusted for better form alignment */
            margin: auto;
            margin-top: 50px;
            margin-bottom: 50px; /* Reduced bottom margin */
            background: #f7f7f7; /* Lighter background for the surrounding box */
            padding: 20px; /* Padding for inner spacing */
            border-radius: 8px; /* Rounded corners for the fieldset */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Consistent shadow with the slider */
        }
    </style>
</head>
<body>
<fieldset class="fieldset">
    <legend>Fizetett szabadságok állása</legend>
    <div class="csuszka">
        <div class="section paid_free_csuszka">
            <span class="tooltiptext"><?php
                echo "Fizetett felhasználható szabadság: " . $result['paid_free'] . "<br>";
                ?></span>
        </div>
        <div class="section paid_requested_csuszka">
            <span class="tooltiptext"><?php
                echo "Fizetett kérvényezett szabadság: " . $result['paid_requested'] . "<br>";
                ?></span>
        </div>
        <div class="section paid_planned_csuszka">
            <span class="tooltiptext"><?php
                echo "Fizetett betervezett szabadság: " . $result['paid_planned'] . "<br>";
                ?></span>
        </div>
        <div class="section paid_taken_csuszka">
            <span class="tooltiptext"><?php
                echo "Fizetett felhasznált szabadság: " . $result['paid_taken'] . "<br>";
                ?></span>
        </div>

    </div>
</fieldset>



</body>
</html>
