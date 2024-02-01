<?php
include "session_check.php";
include "connect.php";
include "function_get_status_name.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestedStatus = $_POST['nap'];
    $message = $_POST['message'];
    $date = $_POST['date']; // Ensure this date is in 'YYYY-MM-DD' format
    $userWorkID = $_SESSION['work_id'];
    $toWhom = "";

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



    // Begin transaction
    $conn->beginTransaction();

    switch ($requestedStatus) {
        case 'payed_leave':
            $sql = "SELECT work_id, payed_free, payed_requested, payed_past_free, payed_past_requested FROM users WHERE work_id = :userWorkID";
            $stmt = $conn->prepare($sql);

// Bind the parameter
            $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($result)) {
                $userData = $result[0]; // Assuming you want the first user's data

                if ($userData['payed_past_free'] > 0) {
                    $requestedStatus = 'payed_past_requested';
                } elseif ($userData['payed_free'] > 0) {
                    $requestedStatus = 'payed_requested';
                } else {
                    echo "Nincs elég szabadnapod!";
                    exit; // Use exit instead of break outside a loop/switch
                }
            } else {
                echo "User not found.";
                exit;
            }


            try {
                // Fetch the calendar_id for the given date and work_id
                $sql = "SELECT calendar_id FROM calendar WHERE date = :date AND work_id = :userWorkID";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
                $stmt->execute();
                $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($calendarResult) {
                    $calendarID = $calendarResult['calendar_id'];

                    // Update the day_status in the calendar table
                    $updateCalendarSql = "UPDATE calendar SET day_status = :requestedStatus WHERE calendar_id = :calendar_id";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendar_id', $calendarID, PDO::PARAM_INT);
                    $updateCalendarStmt->bindParam(':requestedStatus', $requestedStatus, PDO::PARAM_STR);
                    $updateCalendarStmt->execute();


                    // Insert the new request
                    $insertSql = "INSERT INTO requests (work_id, calendar_id, requested_status, message, to_whom, request_status, timestamp, modified_date) VALUES (:work_id, :calendar_id, :requested_status, :message, :to_whom, 'pending', :timestamp, NULL)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bindParam(':work_id', $userWorkID);
                    $insertStmt->bindParam(':calendar_id', $calendarID);
                    $insertStmt->bindParam(':requested_status', $requestedStatus);
                    $insertStmt->bindParam(':message', $message);
                    $insertStmt->bindParam(':to_whom', $toWhom);
                    $insertStmt->bindParam(':timestamp', $currentTimestamp);
                    $insertStmt->execute();

                    // Update the user's free and requested counts
                    if ($requestedStatus =='payed_past_requested'){
                        $requested='payed_past_requested';
                        $free='payed_past_free';
                    }
                    elseif ($requestedStatus =='payed_requested'){
                        $requested='payed_requested';
                        $free='payed_free';
                    }
                    // Ensure $free and $requested are valid column names to prevent SQL injection
                    $validColumns = ['payed_past_requested', 'payed_requested', 'payed_past_free', 'payed_free'];
                    if (in_array($free, $validColumns) && in_array($requested, $validColumns)) {
                        $updateUserSql = "UPDATE users SET $free = $free - 1, $requested = $requested + 1 WHERE work_id = :work_id";
                        $updateUserStmt = $conn->prepare($updateUserSql);
                        $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                        $updateUserStmt->execute();
                    } else {
                        // Handle invalid column names
                        echo "Invalid column name.";
                        exit;
                    }

                    // Commit the transaction
                    $conn->commit();

                    // Pop-up window or message for successful request
                    header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                    echo "Sikeres ", getStatusName($requestedStatus), " kérelmezés a $date napra.";
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
            // Code to handle work_day TODO
            break;
        case 'online_work':
            $requestedStatus='unpayed_home_requested';
            try {
                // Fetch the calendar_id for the given date and work_id
                $sql = "SELECT calendar_id FROM calendar WHERE date = :date AND work_id = :userWorkID";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
                $stmt->execute();
                $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($calendarResult) {
                    $calendarID = $calendarResult['calendar_id'];

                    // Update the day_status in the calendar table
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'unpayed_home_requested' WHERE calendar_id = :calendar_id";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendar_id', $calendarID, PDO::PARAM_INT);
                    $updateCalendarStmt->execute();

                    // Insert the new request
                    $insertSql = "INSERT INTO requests (work_id, calendar_id, requested_status, message, to_whom, request_status, timestamp, modified_date) VALUES (:work_id, :calendar_id, :requested_status, :message, :to_whom, 'pending', :timestamp, NULL)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bindParam(':work_id', $userWorkID);
                    $insertStmt->bindParam(':calendar_id', $calendarID);
                    $insertStmt->bindParam(':requested_status', $requestedStatus);
                    $insertStmt->bindParam(':message', $message);
                    $insertStmt->bindParam(':to_whom', $toWhom);
                    $insertStmt->bindParam(':timestamp', $currentTimestamp);
                    $insertStmt->execute();

                    // Update the user's free and requested counts
                    $updateUserSql = "UPDATE users SET unpayed_home_free = unpayed_home_free - 1, unpayed_home_requested = unpayed_home_requested + 1 WHERE work_id = :work_id";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                    $updateUserStmt->execute();

                    // Commit the transaction
                    $conn->commit();

                    // Pop-up window or message for successful request
                    header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                    echo "Sikeres kérelmezés a $date napra.";
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
        case 'award_leave':
            $requestedStatus='payed_award_requested';
            try {
                // Fetch the calendar_id for the given date and work_id
                $sql = "SELECT calendar_id FROM calendar WHERE date = :date AND work_id = :userWorkID";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
                $stmt->execute();
                $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($calendarResult) {
                    $calendarID = $calendarResult['calendar_id'];

                    // Update the day_status in the calendar table
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'payed_award_requested' WHERE calendar_id = :calendar_id";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendar_id', $calendarID, PDO::PARAM_INT);
                    $updateCalendarStmt->execute();

                    // Insert the new request
                    $insertSql = "INSERT INTO requests (work_id, calendar_id, requested_status, message, to_whom, request_status, timestamp, modified_date) VALUES (:work_id, :calendar_id, :requested_status, :message, :to_whom, 'pending', :timestamp, NULL)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bindParam(':work_id', $userWorkID);
                    $insertStmt->bindParam(':calendar_id', $calendarID);
                    $insertStmt->bindParam(':requested_status', $requestedStatus);
                    $insertStmt->bindParam(':message', $message);
                    $insertStmt->bindParam(':to_whom', $toWhom);
                    $insertStmt->bindParam(':timestamp', $currentTimestamp);
                    $insertStmt->execute();

                    // Update the user's free and requested counts
                    $updateUserSql = "UPDATE users SET payed_award_free = payed_award_free - 1, payed_award_requested = payed_award_requested + 1 WHERE work_id = :work_id";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                    $updateUserStmt->execute();

                    // Commit the transaction
                    $conn->commit();

                    // Pop-up window or message for successful request
                    header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));

                    echo "Sikeres kérelmezés a $date napra.";
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
        case 'edu_leave':
            $requestedStatus='payed_edu_requested';
            try {
                // Fetch the calendar_id for the given date and work_id
                $sql = "SELECT calendar_id FROM calendar WHERE date = :date AND work_id = :userWorkID";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
                $stmt->execute();
                $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($calendarResult) {
                    $calendarID = $calendarResult['calendar_id'];

                    // Update the day_status in the calendar table
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'payed_edu_requested' WHERE calendar_id = :calendar_id";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendar_id', $calendarID, PDO::PARAM_INT);
                    $updateCalendarStmt->execute();

                    // Insert the new request
                    $insertSql = "INSERT INTO requests (work_id, calendar_id, requested_status, message, to_whom, request_status, timestamp, modified_date) VALUES (:work_id, :calendar_id, :requested_status, :message, :to_whom, 'pending', :timestamp, NULL)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bindParam(':work_id', $userWorkID);
                    $insertStmt->bindParam(':calendar_id', $calendarID);
                    $insertStmt->bindParam(':requested_status', $requestedStatus);
                    $insertStmt->bindParam(':message', $message);
                    $insertStmt->bindParam(':to_whom', $toWhom);
                    $insertStmt->bindParam(':timestamp', $currentTimestamp);
                    $insertStmt->execute();

                    // Update the user's free and requested counts
                    $updateUserSql = "UPDATE users SET payed_edu_free = payed_edu_free - 1, payed_edu_requested = payed_edu_requested + 1 WHERE work_id = :work_id";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                    $updateUserStmt->execute();

                    // Commit the transaction
                    $conn->commit();

                    // Pop-up window or message for successful request
                    header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                    echo "Sikeres kérelmezés a $date napra.";
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
        case 'dad_leave':
            $requestedStatus='unpayed_dad_requested';
            try {
                // Fetch the calendar_id for the given date and work_id
                $sql = "SELECT calendar_id FROM calendar WHERE date = :date AND work_id = :userWorkID";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
                $stmt->execute();
                $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($calendarResult) {
                    $calendarID = $calendarResult['calendar_id'];

                    // Update the day_status in the calendar table
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'unpayed_dad_requested' WHERE calendar_id = :calendar_id";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendar_id', $calendarID, PDO::PARAM_INT);
                    $updateCalendarStmt->execute();

                    // Insert the new request
                    $insertSql = "INSERT INTO requests (work_id, calendar_id, requested_status, message, to_whom, request_status, timestamp, modified_date) VALUES (:work_id, :calendar_id, :requested_status, :message, :to_whom, 'pending', :timestamp, NULL)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bindParam(':work_id', $userWorkID);
                    $insertStmt->bindParam(':calendar_id', $calendarID);
                    $insertStmt->bindParam(':requested_status', $requestedStatus);
                    $insertStmt->bindParam(':message', $message);
                    $insertStmt->bindParam(':to_whom', $toWhom);
                    $insertStmt->bindParam(':timestamp', $currentTimestamp);
                    $insertStmt->execute();

                    // Update the user's free and requested counts
                    $updateUserSql = "UPDATE users SET unpayed_dad_free = unpayed_dad_free - 1, unpayed_dad_requested = unpayed_dad_requested + 1 WHERE work_id = :work_id";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                    $updateUserStmt->execute();

                    // Commit the transaction
                    $conn->commit();

                    // Pop-up window or message for successful request
                    header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                    echo "Sikeres kérelmezés a $date napra.";
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
        case 'unpayed_sickness_taken':
            $requestedStatus='unpayed_sickness_taken';
            try {
                // Fetch the calendar_id for the given date and work_id
                $sql = "SELECT calendar_id FROM calendar WHERE date = :date AND work_id = :userWorkID";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
                $stmt->execute();
                $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($calendarResult) {
                    $calendarID = $calendarResult['calendar_id'];

                    // Update the day_status in the calendar table
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'unpayed_sickness_taken' WHERE calendar_id = :calendar_id";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendar_id', $calendarID, PDO::PARAM_INT);
                    $updateCalendarStmt->execute();

                    // Update the user's free and requested counts
                    $updateUserSql = "UPDATE users SET unpayed_sickness_taken = unpayed_sickness_taken + 1 WHERE work_id = :work_id";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                    $updateUserStmt->execute();

                    // Commit the transaction
                    $conn->commit();

                    // TODO Pop-up window or message for successful request

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
        case 'unpayed_leave':
            $requestedStatus='unpayed_requested';
            try {
                // Fetch the calendar_id for the given date and work_id
                $sql = "SELECT calendar_id FROM calendar WHERE date = :date AND work_id = :userWorkID";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
                $stmt->execute();
                $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($calendarResult) {
                    $calendarID = $calendarResult['calendar_id'];

                    // Update the day_status in the calendar table
                    $updateCalendarSql = "UPDATE calendar SET day_status = 'unpayed_requested' WHERE calendar_id = :calendar_id";
                    $updateCalendarStmt = $conn->prepare($updateCalendarSql);
                    $updateCalendarStmt->bindParam(':calendar_id', $calendarID, PDO::PARAM_INT);
                    $updateCalendarStmt->execute();

                    // Insert the new request
                    $insertSql = "INSERT INTO requests (work_id, calendar_id, requested_status, message, to_whom, request_status, timestamp, modified_date) VALUES (:work_id, :calendar_id, :requested_status, :message, :to_whom, 'pending', :timestamp, NULL)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bindParam(':work_id', $userWorkID);
                    $insertStmt->bindParam(':calendar_id', $calendarID);
                    $insertStmt->bindParam(':requested_status', $requestedStatus);
                    $insertStmt->bindParam(':message', $message);
                    $insertStmt->bindParam(':to_whom', $toWhom);
                    $insertStmt->bindParam(':timestamp', $currentTimestamp);
                    $insertStmt->execute();

                    // Update the user's free and requested counts
                    $updateUserSql = "UPDATE users SET unpayed_free = unpayed_free - 1, unpayed_requested = unpayed_requested + 1 WHERE work_id = :work_id";
                    $updateUserStmt = $conn->prepare($updateUserSql);
                    $updateUserStmt->bindParam(':work_id', $userWorkID, PDO::PARAM_INT);
                    $updateUserStmt->execute();

                    // Commit the transaction
                    $conn->commit();

                    // Pop-up window or message for successful request
                    header("Location: calendar.php?view=" . urlencode($currentView)."&month=".urlencode($month)."&year=".urlencode($year));
                    echo "Sikeres kérelmezés a $date napra.";
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
            // Code to handle an unknown value
            break;
    }}
?>
