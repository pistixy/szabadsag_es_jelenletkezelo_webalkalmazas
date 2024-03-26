<?php
include "session_check.php";
include "connect.php";
include "function_get_name.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestedStatus = $_POST['nap'];
    $date = $_POST['date']; // Ensure this date is in 'YYYY-MM-DD' format
    $userWorkID = $_SESSION['work_id'];
    $toWhom = "";
    $day_status = $_POST['day_status'];



// Prepare SQL to fetch kar and szervezetszam based on work_id
    $sql = "SELECT kar, szervezetszam FROM users WHERE work_id = :workId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':workId', $userWorkID, PDO::PARAM_INT);
    $stmt->execute();

// Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $kar = $result['kar'];
        $szervezetszam = $result['szervezetszam'];

        // Set to_whom based on kar and szervezetszam
        $toWhom = "admin, " . $kar . ", " . $szervezetszam;
    } else {
        // Handle case where no user data is found
        echo "No user data found for the given work_id.";
    }
    $currentTimestamp = date('Y-m-d H:i:s');
    $currentView=$_POST['view'];


    // Explode the date into an array [year, month, day]
    list($year, $month, $day) = explode('-', $date);

    // Convert them to integers if necessary
    $year = intval($year);
    $month = intval($month);
    $day = intval($day);
    $currentDate = date('Y-m-d');

    // Explode the current date into an array [year, month, day]
    list($currentYear, $currentMonth, $currentDay) = explode('-', $currentDate);

    // Convert them to integers if necessary
    $currentYear = intval($currentYear);
    $currentMonth = intval($currentMonth);

    // Explode the requested date into an array [year, month, day]
    list($requestedYear, $requestedMonth, $requestedDay) = explode('-', $date);

    // Convert them to integers if necessary
    $requestedYear = intval($requestedYear);
    $requestedMonth = intval($requestedMonth);

    // Check if the requested date is in the previous month
    if ($requestedYear < $currentYear || ($requestedYear == $currentYear && $requestedMonth < $currentMonth)) {
    echo "Nem kérvényezhetsz szabadságot az korábbi hónapról.";
    exit;
    }
    if ($day_status =="holiday" || $day_status =="weekend") {
        echo "Nem kérhetsz szabadságot hétvégére, vagy ünnepnapra.";
        exit;
    }


    // Begin transaction
    $conn->beginTransaction();

    function fetchCalendar($date, $userWorkID)
    {
        include "connect.php";
        // Fetch the calendar_id for the given date and work_id
        $sql = "SELECT calendar_id, day_status FROM calendar WHERE date = :date AND work_id = :userWorkID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
        $stmt->execute();
        $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);
        return $calendarResult;
    }
    }function updateStatus($calendarID,$requestedStatus){
        include "connect.php";
        //update status

        $updateCalendarSql = "UPDATE calendar SET day_status = :requestedStatus WHERE calendar_id = :calendar_id";
        $updateCalendarStmt = $conn->prepare($updateCalendarSql);
        $updateCalendarStmt->bindParam(':calendar_id', $calendarID, PDO::PARAM_INT);
        $updateCalendarStmt->bindParam(':requestedStatus', $requestedStatus, PDO::PARAM_STR);
        $updateCalendarStmt->execute();
        return 0;
    }
    function updateStatusAndInsertRequest($userWorkID,$calendarID,$requestedStatus,$toWhom,$currentTimestamp){
        include "connect.php";
        //update status
        $updateCalendarSql = "UPDATE calendar SET day_status = :requestedStatus WHERE calendar_id = :calendar_id";
        $updateCalendarStmt = $conn->prepare($updateCalendarSql);
        $updateCalendarStmt->bindParam(':calendar_id', $calendarID, PDO::PARAM_INT);
        $updateCalendarStmt->bindParam(':requestedStatus', $requestedStatus, PDO::PARAM_STR);
        $updateCalendarStmt->execute();

        // Insert the new request
        $insertSql = "INSERT INTO requests (work_id, calendar_id, requested_status, to_whom, request_status, timestamp, modified_date) VALUES (:work_id, :calendar_id, :requested_status, :to_whom, 'pending', :timestamp, NULL)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bindParam(':work_id', $userWorkID);
        $insertStmt->bindParam(':calendar_id', $calendarID);
        $insertStmt->bindParam(':requested_status', $requestedStatus);
        $insertStmt->bindParam(':to_whom', $toWhom);
        $insertStmt->bindParam(':timestamp', $currentTimestamp);
        $insertStmt->execute();
        return 0;
    }
        $calendarResult = fetchCalendar($date, $userWorkID);
        $calendarID = $calendarResult['calendar_id'];
        $dayStatus = $calendarResult['day_status'];

        if ($dayStatus=="paid_requested"){
            echo "Mielőtt ezt módosítanád, töröld a kérvényeid erre a napra. (".$date.")";
            exit;
        }
        if ($dayStatus=="paid_taken"){
            echo "Erre a napra már felhasznált egy szabadságot. Ez nem módosítható! (".$date.")";
            exit;
        }
        if ($dayStatus=="paid_planned"){
            switch ($requestedStatus) {
                case 'paid_leave':
                    echo "Már amúgyis szabadságon lenne, ne kérvényezzen még egyet!";
                    exit;
                    break;
                case 'work_day':
                    $requestedStatus='work_day';
                    try {
                        $calendarResult=fetchCalendar($date, $userWorkID);

                        if ($calendarResult) {
                            $calendarID = $calendarResult['calendar_id'];

                            updateStatus($calendarID,$requestedStatus);

                            $updateUserSql = "UPDATE users SET paid_free = paid_free +1, paid_planned = paid_planned - 1 WHERE work_id = :work_id";
                            $updateUserStmt = $conn->prepare($updateUserSql);
                            $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                            $updateUserStmt->execute();
                            // Commit the transaction
                            $conn->commit();

                            header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                            echo "Sikeres Home Office a $date napra.";
                            exit;
                        } else {
                            // Rollback if no calendar entry found
                            $conn->rollBack();
                            echo "No calendar entry found for the specified date and user.";
                            exit;
                        }
                    } catch (Exception $e) {
                        // Rollback on any other exception
                        $conn->rollBack();
                        echo "An error occurred: " . $e->getMessage();
                    }

                    break;
                case 'home_office':

                    $requestedStatus='home_office';
                    try {
                        $calendarResult=fetchCalendar($date, $userWorkID);

                        if ($calendarResult) {
                            $calendarID = $calendarResult['calendar_id'];

                            updateStatus($calendarID,$requestedStatus);

                            $updateUserSql = "UPDATE users SET paid_free = paid_free +1, paid_planned = paid_planned - 1 WHERE work_id = :work_id";
                            $updateUserStmt = $conn->prepare($updateUserSql);
                            $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                            $updateUserStmt->execute();
                            // Commit the transaction
                            $conn->commit();

                            header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                            echo "Sikeres Home Office a $date napra.";
                            exit;
                        } else {
                            // Rollback if no calendar entry found
                            $conn->rollBack();
                            echo "No calendar entry found for the specified date and user.";
                            exit;
                        }
                    } catch (Exception $e) {
                        // Rollback on any other exception
                        $conn->rollBack();
                        echo "An error occurred: " . $e->getMessage();
                    }
                    break;
                case 'unpaid_sickness_taken':
                    $requestedStatus='unpaid_sickness_taken';
                    try {
                        $calendarResult=fetchCalendar($date, $userWorkID);

                        if ($calendarResult) {
                            $calendarID = $calendarResult['calendar_id'];

                            updateStatus($calendarID,$requestedStatus);

                            $updateUserSql = "UPDATE users SET paid_free = paid_free +1, paid_planned = paid_planned - 1 WHERE work_id = :work_id";
                            $updateUserStmt = $conn->prepare($updateUserSql);
                            $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                            $updateUserStmt->execute();
                            // Commit the transaction
                            $conn->commit();

                            header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                            echo "Sikeres betegszabadság a $date napra.";
                            exit;
                        } else {
                            // Rollback if no calendar entry found
                            $conn->rollBack();
                            echo "No calendar entry found for the specified date and user.";
                            exit;
                        }
                    } catch (Exception $e) {
                        // Rollback on any other exception
                        $conn->rollBack();
                        echo "An error occurred: " . $e->getMessage();
                    }
                    break;
                default:
                    echo "Hiba történt!";
                    break;
            }
        }else{
            switch ($requestedStatus) {
                case 'paid_leave':
                    $sql = "SELECT work_id, paid_free, paid_requested FROM users WHERE work_id = :userWorkID";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($result)) {
                        $userData = $result[0];

                        if ($userData['paid_free'] > 0) {
                            $requestedStatus = 'paid_requested';
                        } else {
                            echo "Nincs elég szabadnapod!";
                            exit; // Use exit instead of break outside a loop/switch
                        }
                    } else {
                        echo "User not found.";
                        exit;
                    }
                    try {
                        $calendarResult=fetchCalendar($date, $userWorkID);

                        if ($calendarResult) {
                            $calendarID = $calendarResult['calendar_id'];

                            UpdateStatusAndInsertRequest($userWorkID,$calendarID,$requestedStatus,$toWhom,$currentTimestamp);
                            $updateUserSql = "UPDATE users SET paid_free = paid_free - 1, paid_requested = paid_requested + 1 WHERE work_id = :work_id";
                            $updateUserStmt = $conn->prepare($updateUserSql);
                            $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                            $updateUserStmt->execute();
                            // Commit the transaction
                            $conn->commit();

                            // Pop-up window or message for successful request
                            header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                            echo "Sikeres ", getName($requestedStatus), " kérelmezés a $date napra.";
                        } else {
                            // Rollback if no calendar entry found
                            $conn->rollBack();
                            echo "No calendar entry found for the specified date and user.";
                        }
                    } catch (Exception $e) {
                        // Rollback on any other exception
                        $conn->rollBack();
                        echo "An error occurred: " . $e->getMessage();
                    }
                    break;
                case 'work_day':
                    $requestedStatus='work_day';
                    switch ($dayStatus) {
                        case 'work_day':
                            echo "Már jelenleg is dolgozna ezen a napon!";
                            exit;
                            break;
                        case 'holiday':
                        case 'weekend':
                            echo "Nem kérhetsz munkanapot hétvégére vagy ünnepnapra.";
                            exit;
                            break;
                        case "home_office":
                        case "unpaid_sickness_taken":
                            updateStatus($calendarID,$requestedStatus);
                            header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                            echo "Státusza munkanapra visszamódosítva";
                            break;
                    }
                    break;
                case 'home_office':
                    $requestedStatus='home_office';
                    try {
                        $calendarResult=fetchCalendar($date, $userWorkID);

                        if ($calendarResult) {
                            $calendarID = $calendarResult['calendar_id'];

                            updateStatus($calendarID,$requestedStatus);

                            header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                            echo "Sikeres Home Office a $date napra.";
                        } else {
                            // Rollback if no calendar entry found
                            $conn->rollBack();
                            echo "No calendar entry found for the specified date and user.";
                        }
                    } catch (Exception $e) {
                        // Rollback on any other exception
                        $conn->rollBack();
                        echo "An error occurred: " . $e->getMessage();
                    }
                    break;
                case 'unpaid_sickness_taken':
                    $requestedStatus='unpaid_sickness_taken';
                    try {
                        $calendarResult=fetchCalendar($date, $userWorkID);

                        if ($calendarResult) {
                            $calendarID = $calendarResult['calendar_id'];

                            updateStatus($calendarID,$requestedStatus);

                            header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                            echo "Sikeres betegszabadság a $date napra.";
                        } else {
                            // Rollback if no calendar entry found
                            $conn->rollBack();
                            echo "No calendar entry found for the specified date and user.";
                        }
                    } catch (Exception $e) {
                        // Rollback on any other exception
                        $conn->rollBack();
                        echo "An error occurred: " . $e->getMessage();
                    }
                    break;
                default:
                    echo "Hiba történt!";
                    break;
            }
        }

?>