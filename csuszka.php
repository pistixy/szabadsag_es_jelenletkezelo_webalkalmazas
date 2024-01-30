<?php
include "connect.php";
include "session_check.php";

// Determine whose details to show: either from URL (if admin and provided) or from session
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

    if ($result) {
        $payed_free = $result['payed_free'] + $result['payed_edu_free']+ $result['payed_award_free'] +$result['payed_past_free'];
        $payed_requested = $result['payed_requested'] + $result['payed_edu_requested']+ $result['payed_award_requested'] +$result['payed_past_requested'];
        $payed_planned = $result['payed_planned'] + $result['payed_edu_planned']+ $result['payed_award_planned'] +$result['payed_past_planned'];
        $payed_taken = $result['payed_taken'] + $result['payed_edu_taken']+ $result['payed_award_taken'] +$result['payed_past_taken'];

        $unpayed_free= $result['unpayed_dad_free']+$result['unpayed_home_free'];
        $unpayed_requested=$result['unpayed_dad_requested']+$result['unpayed_home_requested'];
        $unpayed_planned=$result['unpayed_dad_planned']+$result['unpayed_home_planned'];
        $unpayed_taken=$result['unpayed_sickness_taken']+$result['unpayed_dad_taken']+$result['unpayed_home_taken']+$result['unpayed_uncertified_taken'];

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

        .payed_free_csuszka {
            flex: <?php echo $payed_free; ?>;
            background-color: green;
        }
        .payed_requested_csuszka {
            flex: <?php echo $payed_requested; ?>;
            background-color: lightsalmon;
        }

        .payed_planned_csuszka {
            flex: <?php echo $payed_planned; ?>;
            background-color: lightgreen;
        }

        .payed_taken_csuszka {
            flex: <?php echo $payed_taken; ?>;
            background-color: red;
        }

        .unpayed_free_csuszka {
            flex: <?php echo $unpayed_free; ?>;
            background-color: green;
        }
        .unpayed_requested_csuszka {
            flex: <?php echo $unpayed_requested; ?>;
            background-color: lightsalmon;
        }

        .unpayed_planned_csuszka {
            flex: <?php echo $unpayed_planned; ?>;
            background-color: lightgreen;
        }

        .unpayed_taken_csuszka {
            flex: <?php echo $unpayed_taken; ?>;
            background-color: red;
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
            margin: auto;
            margin-bottom: 100px;
            background: #dddddd;
        }
    </style>
</head>
<body>
<fieldset class="fieldset">
    <legend>Fizetett szabadságok állása</legend>
    <div class="csuszka">
        <div class="section payed_free_csuszka">
            <span class="tooltiptext"><?php
        echo "Összesen fel nem használt: " . $payed_free . "<br>";
        echo "Fizetett szabadság: " . $result['payed_free'] . "<br>";
        echo "Tanulmányi szabadság: " . $result['payed_edu_free'] . "<br>";
        echo "Jutalom szabadság: " . $result['payed_award_free'] . "<br>";
        echo "Előző évi szabadság: " . $result['payed_past_free'] . "<br>";
        ?></span>
        </div>


        <div class="section payed_requested_csuszka">
            <span class="tooltiptext"><?php
                echo "Összesen kérelmezett: " . $payed_requested . "<br>";
                echo "Fizetett szabadság: " . $result['payed_requested'] . "<br>";
                echo "Tanulmányi szabadság: " . $result['payed_edu_requested'] . "<br>";
                echo "Jutalom szabadság: " . $result['payed_award_requested'] . "<br>";
                echo "Előző évi szabadság: " . $result['payed_past_requested'] . "<br>";
                ?></span>
        </div>
        <div class="section payed_planned_csuszka">
            <span class="tooltiptext"><?php
                echo "Összesen betervezett: " . $payed_planned . "<br>";
                echo "Fizetett szabadság: " . $result['payed_planned'] . "<br>";
                echo "Tanulmányi szabadság: " . $result['payed_edu_planned'] . "<br>";
                echo "Jutalom szabadság: " . $result['payed_award_planned'] . "<br>";
                echo "Előző évi szabadság: " . $result['payed_past_planned'] . "<br>";
                ?></span>
        </div>
        <div class="section payed_taken_csuszka">
            <span class="tooltiptext"><?php
                echo "Összesen felhasznált: " . $payed_taken . "<br>";
                echo "Fizetett szabadság: " . $result['payed_taken'] . "<br>";
                echo "Tanulmányi szabadság: " . $result['payed_edu_taken'] . "<br>";
                echo "Jutalom szabadság: " . $result['payed_award_taken'] . "<br>";
                echo "Előző évi szabadság: " . $result['payed_past_taken'] . "<br>";
                ?></span>
        </div>

    </div>
</fieldset>


<fieldset class="fieldset">
    <legend>Fizetetlen szabadságok állása</legend>
    <div class="csuszka">
        <div class="section unpayed_free_csuszka">
            <span class="tooltiptext"><?php
                echo "Összesen fel nem használt: " . $unpayed_free . "<br>";
                echo "Apanap: " . $result['unpayed_dad_free'] . "<br>";
                echo "Home office: " . $result['unpayed_home_free'] . "<br>";
                ?>
            </span>
        </div>


        <div class="section unpayed_requested_csuszka">
            <span class="tooltiptext"><?php
                echo "Összesen kérelmezett: " . $unpayed_requested . "<br>";
                echo "Apanap: " . $result['unpayed_dad_requested'] . "<br>";
                echo "Home office: " . $result['unpayed_home_requested'] . "<br>";
                ?>
            </span>
        </div>
        <div class="section unpayed_planned_csuszka">
            <span class="tooltiptext"><?php
                echo "Összesen betervezett: " . $unpayed_planned . "<br>";
                echo "Apanap: " . $result['unpayed_dad_planned'] . "<br>";
                echo "Home office: " . $result['unpayed_home_planned'] . "<br>";
                ?>
            </span>
        </div>
        <div class="section unpayed_taken_csuszka">
            <span class="tooltiptext"><?php
                echo "Összesen felhasznált: " . $unpayed_taken . "<br>";
                echo "Apanap: " . $result['unpayed_dad_taken'] . "<br>";
                echo "Home office: " . $result['unpayed_home_taken'] . "<br>";
                echo "Betegszabadság: " . $result['unpayed_sickness_taken'] . "<br>";
                echo "Igazolatlan: " . $result['unpayed_uncertified_taken'] . "<br>";
                ?>
            </span>
        </div>

    </div>
</fieldset>
</body>
</html>
