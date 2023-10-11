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

<?php
include "footer.php";
?>
</body>
</html>
