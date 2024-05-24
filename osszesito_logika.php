<?php
include "session_check.php";
include "connect.php";
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
    <link rel="stylesheet" href="styles4.css">
</head>
<body>
<?php include "test_top-bar.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "test_nav-bar.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <div class="my-requests">
                <h1>Összesítők exportálása</h1>
                <?php
                include "connect.php";
                include "session_check.php";
                $karok_tomb = ['ESK', 'DFK', 'GIVK', 'KGYK', 'MK', 'ÉÉKK', 'MÉK', 'AK', 'AHJK'];
                // Check if the user is logged in and has permission
                if (isset($_SESSION['logged']) && ($_SESSION['position'] == "dekan" || $_SESSION['position'] == "admin")) {
                    $work_id = $_SESSION['work_id'];
                    $stmt = $conn->prepare("SELECT kar FROM users WHERE work_id = :work_id");
                    $stmt->bindParam(':work_id', $work_id);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $kar = $result['kar'];

                    // Define selected year and month
                    $selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
                    $selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');

                    // Prepare to check for pending requests
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

                    // Start form
                    echo '<form action="" method="post">'; // Form submits to itself to select year and month

                    // Include the kar selector for admins
                    if ($_SESSION['position'] == "admin") {
                        $kar = isset($_POST['kar']) ? $_POST['kar'] : $kar; // Use posted value if available, otherwise use the user's kar
                        echo '<label for="kar">Válasszon kart:</label>';
                        echo '<select name="kar" id="kar">';
                        foreach ($karok_tomb as $karok) {
                            $selected = ($karok == $kar) ? 'selected' : '';
                            echo "<option value=\"$karok\" $selected>$karok</option>";
                        }
                        echo '</select> ';
                    } else {
                        // For non-admin users, set a hidden input to use their 'kar'
                        echo '<input type="hidden" name="kar" value="' . htmlspecialchars($kar) . '">';
                    }

                    // Year selector
                    $selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
                    echo '<label for="year">Válasszon évet:</label>';
                    echo '<select name="year" id="year">';
                    for ($i = date('Y') + 2; $i >= 2022; $i--) {
                        $selected = ($i == $selectedYear) ? 'selected' : '';
                        echo "<option value=\"$i\" $selected>$i</option>";
                    }
                    echo '</select> ';

                    // Month selector
                    $selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
                    echo '<label for="month">Válasszon hónapot:</label>';
                    echo '<select name="month" id="month">';
                    for ($i = 1; $i <= 12; $i++) {
                        $selected = ($i == $selectedMonth) ? 'selected' : '';
                        $monthName = date('F', mktime(0, 0, 0, $i, 10)); // Convert month number to name
                        echo "<option value=\"$i\" $selected>$monthName</option>";
                    }
                    echo '</select> ';

                    // Button to submit year and month selection
                    echo '<button type="submit" name="check_requests" value="1">Ellenőrizze a kérelmeket</button>';
                    echo '</form>';

                    // If the 'check_requests' button is pressed
                    $stmt = $conn->prepare("SELECT work_id FROM users WHERE kar = :kar");
                    $stmt->bindParam(':kar', $kar);
                    $stmt->execute();
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Define $users here so it's always available

                    $workerIds = array_column($users, 'work_id'); // Get the worker IDs

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
<script src="collapse.js"></script>
</body>
</html>

