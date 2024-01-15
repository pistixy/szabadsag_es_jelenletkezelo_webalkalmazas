<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Date Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
session_start();
include "connect.php";
include "nav-bar.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

if (isset($_GET['date'])) {
    $clickedDate = $_GET['date'];

    if (isset($_SESSION['work_id'])) {
        $userWorkID = $_SESSION['work_id'];

        $sql = "SELECT * FROM calendar WHERE date = :clickedDate AND work_id = :userWorkID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':clickedDate', $clickedDate);
        $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 1) {
            $row = $result[0];
            $date = $row['date'];
            $dayOfWeek = date('l', strtotime($date));
            $day_status = $row['day_status'] == 1 ? "Munkanap" : "Szabadnap";
            $comment = $row['comment'];
        } else {
            echo "Date details not found for the current user.";
            exit;
        }
    } else {
        echo "User session not found.";
        exit;
    }
} else {
    echo "Date not specified.";
    exit;
}
?>


<h1>Date: <?php echo $date; ?></h1>
<p>Nap: <?php echo $dayOfWeek; ?></p>
<p>Státusz: <?php echo $day_status; ?></p>
<p>Megjegyzés: <?php echo $comment; ?></p>

<div>
    <fieldset>
    <h2>Válassza ki, mire módosítaná az adott napot!</h2>
    <form action="request.php" method="post">

        <label for="fizetett_szabadnap">
            <input type="radio" name="nap" id="fizetett_szabadnap" value="Fizetett Szabadnap" <?php if($row['day_status']=="0"){
                echo "checked";
            }?>>
            Fizetett Szabadnap
        </label><br>

        <label for="munkanap">
            <input type="radio" name="nap" id="munkanap" value="Munkanap" <?php if($row['day_status']=="1"){
                echo "checked";
            }?>>
            Munkanap
        </label><br>

        <label for="online_munka">
            <input type="radio" name="nap" id="online_munka" value="Online Munka"<?php if($row['day_status']=="2"){
                echo "checked";
            }?>>
            Online Munka
        </label><br>

        <label for="betegszabadsag">
            <input type="radio" name="nap" id="betegszabadsag" value="Betegszabadság"<?php if($row['day_status']=="3"){
                echo "checked";
            }?>>
            Betegszabadság
        </label><br>

        <label for="fizetetlen_szabadsag">
            <input type="radio" name="nap" id="fizetetlen_szabadsag" value="Fizetetlen szabadság"<?php if($row['day_status']=="4"){
                echo "checked";
            }?>>
            Fizetetlen szabadság
        </label><br>

        <label for="tervezett_szabadsag">
            <input type="radio" name="nap" id="tervezett_szabadsag" value="Tervezett szabadság"<?php if($row['day_status']=="5"){
                echo "checked";
            }?>>
            Tervezett szabadság
        </label><br>

        <label>Ide írja le kérését és indokolja!
            <textarea style="width: 100%; height: 200px">Tisztelt ...!
A <?php echo $date ?> napot szeretném...

Oka:

Tisztelettel,
<?php
if (isset($_SESSION['work_id'])) {
    $userWorkID = $_SESSION['work_id'];

    $sql = "SELECT * FROM users WHERE work_id = :userWorkID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) == 1) {
        $row = $result[0];
        $name = $row['name'];
        echo $name;
    } else {
        echo "User not found.";
    }
}
?>
</textarea>

        </label>

        <input type="hidden" name="date" value="<?php echo $date; ?>">
        <input type="submit" name="submit" value="Submit">
    </fieldset>
    </form>


</div>
<?php
include "footer.php";
?>
</body>
<script>

</script>
</html>
