    <?php
    include "session_check.php";
    include "connect.php";
    include "check_login.php";
    include "function_get_name.php";

    if (isset($_GET['view'])) {
        $currentView = $_GET['view'];
    }

    if (!isset($_SESSION['logged'])) {
        header("Location: login_form.php");
        exit;
    }

    if (isset($_GET['date'])) {
        $clickedDate = $_GET['date'];

        if (isset($_SESSION['work_id'])) {
            $userWorkID = $_SESSION['work_id'];

            // Fetch calendar details
            $sql = "SELECT * FROM calendar WHERE date = :clickedDate AND work_id = :userWorkID";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':clickedDate', $clickedDate);
            $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
            $stmt->execute();
            $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$calendarResult) {
                echo "Nincsenek rekordok erre a napra.";
                include "footer.php";
                exit;
            }

            // Fetch requests for the date
            $requestSql = "SELECT * FROM requests WHERE work_id = :userWorkID AND calendar_id = :calendarId and request_status='pending'";
            $requestStmt = $conn->prepare($requestSql);
            $requestStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
            $requestStmt->bindParam(':calendarId', $calendarResult['calendar_id'], PDO::PARAM_INT);
            $requestStmt->execute();
            $requests = $requestStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            echo "User session not found.";
            exit;
        }
    } else {
        echo "Date not specified.";
        exit;
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Dátum adatok</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="body-container">
            <div class="navbar">
            <?php include "nav-bar.php";?>
            </div>
            <div class="main-content">
                <div class="date-details-page">
                    <div class=date-details-container>
                        <h1 class=date-details-title>Date: <?php echo $calendarResult['date']; ?></h1>
                    </div>
                        <p>Nap: <?php echo getName(date('l', strtotime($calendarResult['date']))); ?></p>
                        <p>Státusz: <?php echo getName($calendarResult['day_status'])?></p>
                        <p>Megjegyzés: <?php echo $calendarResult['comment']; ?></p>


                        <?php
                        if($calendarResult['day_status']=="holiday" or $calendarResult['day_status']=="weekend"){
                            Echo "Hétvégén vagy ünnepnapon nem lehet módosításokat eszközölni.";
                        }else{
                            include "active_requests.php";

                            if ($_SESSION['is_user']==false){
                                include "list_day_users.php";
                            }

                            include "day_selector.php";
                        }

                        ?>
                    <div class="footer-div">
                        <?php
                        include "footer.php";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>

