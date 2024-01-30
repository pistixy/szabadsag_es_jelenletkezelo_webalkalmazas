<?php
include "session_check.php";
include "connect.php";
include "nav-bar.php";
include "function_get_status_name.php";


if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Check if work_id is passed in URL and user is admin, else use session work_id
if (isset($_GET['work_id']) && $_SESSION['isAdmin']) {
    $userWorkId = $_GET['work_id'];
    $isOwnCalendar = $userWorkId == $_SESSION['work_id'];
} else {
    $userWorkId = $_SESSION['work_id'];
    $isOwnCalendar = true;
}

// Fetch user's name for the calendar header
$stmt = $conn->prepare("SELECT name FROM users WHERE work_id = :work_id");
$stmt->bindParam(':work_id', $userWorkId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$calendarOwnerName = $user ? $user['name'] : "Unknown User";

if (isset($_GET['year']) && isset($_GET['month'])) {
    $year = intval($_GET['year']);
    $month = intval($_GET['month']);
} else {
    $year = date("Y");
    $month = date("n");
}

$prevMonth = ($month == 1) ? 12 : $month - 1;
$prevYear = ($month == 1) ? $year - 1 : $year;
$nextMonth = ($month == 12) ? 1 : $month + 1;
$nextYear = ($month == 12) ? $year + 1 : $year;

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$monthName = date("F", mktime(0, 0, 0, $month, 1, $year));

$firstDayOfWeek = date("N", mktime(0, 0, 0, $month, 1, $year));
// TODO $currentView = isset($_GET['view']) ? $_GET['view'] : 'yearly';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isOwnCalendar ? "Naptárad" : "{$calendarOwnerName} Naptára"; ?></title>
    <link rel="stylesheet" >
    <style>
        .payed_requested {
            background-color: #90EE90;
            color: #004225; /* Dark green for contrast */
        }

        .payed_planned {
            background-color: #32CD32;
            color: #ffffff;
        }

        .payed_taken {
            background-color: #006400;
            color: #ffffff;
        }

        .payed_past_requested {
            background-color: #FFFFE0;
            color: #333333;
        }

        .payed_past_planned {
            background-color: #FFFF00;
            color: #333333;
        }

        .payed_past_taken {
            background-color: #FFD700;
            color: #333333;
        }

        .payed_edu_requested {
            background-color: #ADD8E6;
            color: #002366; /* Dark blue for contrast */
        }

        .payed_edu_planned {
            background-color: #0000FF;
            color: #ffffff;
        }

        .payed_edu_taken {
            background-color: #00008B;
            color: #ffffff;
        }

        .payed_award_requested {
            background-color: #DDA0DD;
            color: #4B0082; /* Dark purple for contrast */
        }

        .payed_award_planned {
            background-color: #800080;
            color: #ffffff;
        }

        .payed_award_taken {
            background-color: #4B0082;
            color: #ffffff;
        }

        .unpayed_dad_requested {
            background-color: #A52A2A;
            color: #ffffff;
        }

        .unpayed_dad_planned {
            background-color: #8B4513;
            color: #ffffff;
        }

        .unpayed_dad_taken {
            background-color: #654321;
            color: #ffffff;
        }

        .unpayed_home_requested {
            background-color: #D3D3D3;
            color: #333333;
        }

        .unpayed_home_planned {
            background-color: #808080;
            color: #ffffff;
        }

        .unpayed_home_taken {
            background-color: #696969;
            color: #ffffff;
        }

        .work_day {
            background-color: #FFA500;
            color: #333333;
        }

        .weekend {
            background-color: #FF6347;
            color: #ffffff;
        }

        .holiday {
            background-color: #FF0000;
            color: #ffffff;
        }

        .unpayed_sickness_taken {
            background-color: #0000FF;
            color: #ffffff;
        }

        .unpayed_uncertified_taken {
            background-color: #000000;
            color: #ffffff;
        }
    </style>
</head>
<body>

<table style="width: 100%;">
    <tr>
        <td style="width: 33.33%; text-align: center;">
            <form action="" method="get">
                <input type="hidden" name="year" value="<?php echo $year; ?>">
                <input type="hidden" name="work_id" value="<?php echo $userWorkId; ?>">
                <select name="view" onchange="this.form.submit()">
                    <option value="yearly" <?php echo (!isset($_GET['view']) || $_GET['view'] == 'yearly') ? 'selected' : ''; ?>>Éves Nézet</option>
                    <option value="monthly" <?php echo (isset($_GET['view']) && $_GET['view'] == 'monthly') ? 'selected' : ''; ?>>Havi Nézet</option>
                </select>
            </form>
        </td>
        <td style="width: 33.33%; text-align: center;">
            <h1><?php echo $isOwnCalendar ? "Naptárad" : $calendarOwnerName . " Naptára"; ?></h1>
        </td>
        <td style="width: 33.33%; text-align: center;">
            <?php
            include "legend.php";
            ?>
        </td>

    </tr>

</table>

<?php
// Default to yearly view if no view is set or if yearly view is selected
$selectedView = $_GET['view'] ?? 'yearly';

if ($selectedView == 'yearly') {
    include "year_view.php"; // Assuming you have a separate file for the yearly view
} elseif ($selectedView == 'monthly') {
    include "month_view.php"; // Assuming you have a separate file for the monthly view
}

?>
<?php
include "csuszka.php";
include "footer.php";
?>

</body>
</html>
