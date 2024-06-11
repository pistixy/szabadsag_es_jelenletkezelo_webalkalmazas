<?php
include "session_check.php";
include "app/config/connect.php";
include "function_get_name.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Összesítő</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
<?php include "navigation_bar-top.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "navigation_bar-side.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <div class="my-requests">
                <h1>Összesítők exportálása</h1>
                <?php
                include "app/config/connect.php";
                include "session_check.php";
                $karok_tomb = ['ESK', 'DFK', 'GIVK', 'KGYK', 'MK', 'ÉÉKK', 'MÉK', 'AK', 'AHJK'];
                if (isset($_SESSION['logged']) && ($_SESSION['position'] == "dekan" || $_SESSION['position'] == "admin")) {
                    $work_id = $_SESSION['work_id'];
                    $stmt = $conn->prepare("SELECT kar FROM users WHERE work_id = :work_id");
                    $stmt->bindParam(':work_id', $work_id);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $kar = $result['kar'];

                    $selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
                    $selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');

                    $stmt = $conn->prepare("
                    SELECT COUNT(*) AS pending_count 
                    FROM requests 
                    INNER JOIN users ON requests.work_id = users.work_id 
                    INNER JOIN calendar ON requests.calendar_id = calendar.calendar_id 
                    WHERE users.kar = :kar 
                    AND requests.request_status = 'pending' 
                    AND EXTRACT(YEAR FROM calendar.date) = :selectedYear 
                    AND EXTRACT(MONTH FROM calendar.date) = :selectedMonth
                ");
                    $stmt->bindParam(':kar', $kar);
                    $stmt->bindParam(':selectedYear', $selectedYear, PDO::PARAM_INT);
                    $stmt->bindParam(':selectedMonth', $selectedMonth, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    echo '<form action="" method="post" class="filter-form">';

                    if ($_SESSION['position'] == "admin") {
                        $kar = isset($_POST['kar']) ? $_POST['kar'] : $kar;
                        echo '<div class="form-group">';
                        echo '<label for="kar">Válasszon kart:</label>';
                        echo '<select name="kar" id="kar">';
                        foreach ($karok_tomb as $karok) {
                            $selected = ($karok == $kar) ? 'selected' : '';
                            echo "<option value=\"$karok\" $selected>$karok</option>";
                        }
                        echo '</select>';
                        echo '</div>';
                    } else {
                        echo '<input type="hidden" name="kar" value="' . htmlspecialchars($kar) . '">';
                    }

                    echo '<div class="form-group">';
                    echo '<label for="year">Válasszon évet:</label>';
                    echo '<select name="year" id="year">';
                    for ($i = date('Y') + 2; $i >= 2022; $i--) {
                        $selected = ($i == $selectedYear) ? 'selected' : '';
                        echo "<option value=\"$i\" $selected>$i</option>";
                    }
                    echo '</select>';
                    echo '</div>';

                    echo '<div class="form-group">';
                    echo '<label for="month">Válasszon hónapot:</label>';
                    echo '<select name="month" id="month">';
                    for ($i = 1; $i <= 12; $i++) {
                        $selected = ($i == $selectedMonth) ? 'selected' : '';
                        $monthName = date('F', mktime(0, 0, 0, $i, 10));
                        echo "<option value=\"$i\" $selected>$monthName</option>";
                    }
                    echo '</select>';
                    echo '</div>';

                    echo '<button type="submit" name="check_requests" class="action-button">';
                    echo '<img src="public/images/icons/done_all_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Check">Ellenőrizze a kérelmeket';
                    echo '</button>';
                    echo '</form>';

                    $stmt = $conn->prepare("SELECT work_id FROM users WHERE kar = :kar");
                    $stmt->bindParam(':kar', $kar);
                    $stmt->execute();
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $workerIds = array_column($users, 'work_id');

                    if (isset($_POST['check_requests'])) {
                        include "check_requests.php";
                    }
                } else {
                    echo "Nincs jogosultságod ezt megtekinteni!";
                }
                ?>
            </div>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="public/js/collapse.js"></script>
</body>
</html>


