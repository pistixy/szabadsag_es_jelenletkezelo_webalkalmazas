<?php
$sql = "SELECT free,planned, taken, requested FROM users WHERE WORKID = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $_SESSION['WORKID']);
    $stmt->execute();
    $stmt->bind_result($free, $planned, $taken, $requested);

    if ($stmt->fetch()) {
    } else {
        echo "User not found in the database.";
    }

    $stmt->close();
} else {
    echo "Error with the database query: " . $conn->error;
}

$conn->close();
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
        <div class="section taken">
            <span class="tooltiptext">Felhasznált szabadnapok száma (múltbeli): <?php echo $taken; ?></span>
        </div>
        <div class="section requested">
            <span class="tooltiptext">Betervezett de még nem engedélyezett szabadnapok száma: <?php echo $requested; ?></span>
        </div>

    </div>
</fieldset>
</body>
</html>
