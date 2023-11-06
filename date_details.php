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

    if (isset($_SESSION['WORKID'])) {
        $userWorkID = $_SESSION['WORKID'];

        $sql = "SELECT * FROM calendar WHERE date = ? AND WORKID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $clickedDate, $userWorkID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $date = $row['date'];
            $dayOfWeek = date('l', strtotime($date));
            $isWorkingDay = $row['is_working_day'] == 1 ? "Munkanap" : "Szabadnap";
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
<p>Státusz: <?php echo $isWorkingDay; ?></p>
<p>Megjegyzés: <?php echo $comment; ?></p>

<div>
    <fieldset>
    <h2>Válassza ki, mire módosítaná az adott napot!</h2>
    <form action="request.php" method="post">

        <label for="fizetett_szabadnap">
            <input type="radio" name="nap" id="fizetett_szabadnap" value="Fizetett Szabadnap" <?php if($row['is_working_day']=="0"){
                echo "checked";
            }?>>
            Fizetett Szabadnap
        </label><br>

        <label for="munkanap">
            <input type="radio" name="nap" id="munkanap" value="Munkanap" <?php if($row['is_working_day']=="1"){
                echo "checked";
            }?>>
            Munkanap
        </label><br>

        <label for="online_munka">
            <input type="radio" name="nap" id="online_munka" value="Online Munka"<?php if($row['is_working_day']=="2"){
                echo "checked";
            }?>>
            Online Munka
        </label><br>

        <label for="betegszabadsag">
            <input type="radio" name="nap" id="betegszabadsag" value="Betegszabadság"<?php if($row['is_working_day']=="3"){
                echo "checked";
            }?>>
            Betegszabadság
        </label><br>

        <label for="fizetetlen_szabadsag">
            <input type="radio" name="nap" id="fizetetlen_szabadsag" value="Fizetetlen szabadság"<?php if($row['is_working_day']=="4"){
                echo "checked";
            }?>>
            Fizetetlen szabadság
        </label><br>

        <label for="tervezett_szabadsag">
            <input type="radio" name="nap" id="tervezett_szabadsag" value="Tervezett szabadság"<?php if($row['is_working_day']=="5"){
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
if (isset($_SESSION['WORKID'])) {
    $userWorkID = $_SESSION['WORKID'];

    $sql = "SELECT * FROM users WHERE WORKID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userWorkID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
    }
    echo $name;
}
?></textarea>

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
