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

        $unpayed_free= $result['unpayed_dad_free']+$result['unpayed_home_free']+$result['unpayed_free'];
        $unpayed_requested=$result['unpayed_dad_requested']+$result['unpayed_home_requested']+$result['unpayed_requested'];
        $unpayed_planned=$result['unpayed_dad_planned']+$result['unpayed_home_planned']+$result['unpayed_planned'];
        $unpayed_taken=$result['unpayed_sickness_taken']+$result['unpayed_dad_taken']+$result['unpayed_home_taken']+$result['unpayed_uncertified_taken']+$result['unpayed_taken'];

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
            color: #333; /* Darker text color for better contrast */
            font-weight: bold;
            padding: 0 5px; /* Adjusted padding for text */
            display: flex;
            align-items: center;
            justify-content: center; /* Centered text horizontally */
            position: relative;
            border-radius: 1px; /* Rounded corners for each section */
        }

        .section:hover .tooltiptext {
            display: block;
        }

        .payed_free_csuszka {
            flex: <?php echo $payed_free; ?>;
            background: linear-gradient(to right, #4caf50, #81c784);
        }
        .payed_requested_csuszka {
            flex: <?php echo $payed_requested; ?>;
            background: linear-gradient(to right, #ffccbc, #ffab91);
        }

        .payed_planned_csuszka {
            flex: <?php echo $payed_planned; ?>;
            background: linear-gradient(to right, #aed581, #dcedc8);
        }

        .payed_taken_csuszka {
            flex: <?php echo $payed_taken; ?>;
            background: linear-gradient(to right, #e57373, #ef9a9a);
        }

        .unpayed_free_csuszka {
            flex: <?php echo $unpayed_free; ?>;
            background: linear-gradient(to right, #4caf50, #81c784);
        }
        .unpayed_requested_csuszka {
            flex: <?php echo $unpayed_requested; ?>;
            background: linear-gradient(to right, #ffccbc, #ffab91);
        }

        .unpayed_planned_csuszka {
            flex: <?php echo $unpayed_planned; ?>;
            background: linear-gradient(to right, #aed581, #dcedc8);
        }

        .unpayed_taken_csuszka {
            flex: <?php echo $unpayed_taken; ?>;
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
    <legend>Fizetettlen szabadságok állása</legend>
    <div class="csuszka">
        <div class="section unpayed_free_csuszka">
            <span class="tooltiptext"><?php
                echo "Összesen fel nem használt: " . $unpayed_free . "<br>";
                echo "Apanap: " . $result['unpayed_dad_free'] . "<br>";
                echo "Home office: " . $result['unpayed_home_free'] . "<br>";
                echo "Igazolt távollét: " . $result['unpayed_free'] . "<br>";
                ?>
            </span>
        </div>


        <div class="section unpayed_requested_csuszka">
            <span class="tooltiptext"><?php
                echo "Összesen kérelmezett: " . $unpayed_requested . "<br>";
                echo "Apanap: " . $result['unpayed_dad_requested'] . "<br>";
                echo "Home office: " . $result['unpayed_home_requested'] . "<br>";
                echo "Igazolt távollét: " . $result['unpayed_requested'] . "<br>";
                ?>
            </span>
        </div>
        <div class="section unpayed_planned_csuszka">
            <span class="tooltiptext"><?php
                echo "Összesen betervezett: " . $unpayed_planned . "<br>";
                echo "Apanap: " . $result['unpayed_dad_planned'] . "<br>";
                echo "Home office: " . $result['unpayed_home_planned'] . "<br>";
                echo "Igazolt távollét: " . $result['unpayed_planned'] . "<br>";
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
                echo "Igazolt távollét: " . $result['unpayed_taken'] . "<br>";
                ?>
            </span>
        </div>

    </div>
</fieldset>
</body>
</html>
