<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Összesítő</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="body-container">
    <div class="navbar">
        <?php include "nav-bar.php"; ?>
    </div>
    <div class="main-content">
        <div class="my-requests">
            <h1>Összesítők exportálása</h1>
            <?php
            include "connect.php";
            include "session_check.php";

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
                // Year selector
                echo '<label for="year">Válasszon évet:</label>';
                echo '<select name="year" id="year">';
                for ($i = date('Y') + 2; $i >= 2022; $i--) {
                    $selected = ($i == $selectedYear) ? 'selected' : '';
                    echo "<option value=\"$i\" $selected>$i</option>";
                }
                echo '</select> ';

                // Month selector
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
                    // Redefine the $stmt for pending requests as the previous $stmt is now used for fetching $users
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

                    // Check if there are no pending requests and show the export button if none are found
                    if ($result['pending_count'] == 0) {

                        echo "Előnézet: ";
                        echo '<form action="download_preview.php" method="post">';
                        echo '<input type="hidden" name="work_ids" value="' . implode(',', $workerIds) . '">';
                        echo '<input type="hidden" name="feltetel" value="' . $kar . '">';
                        echo '<input type="hidden" name="month" value="' . $selectedMonth . '">';
                        echo '<input type="hidden" name="year" value="' . $selectedYear . '">';
                        echo '<input type="hidden" name="position" value="dekan">';
                        echo '<button type="submit">Az elönezetért kattintson ide</button>';
                        echo '</form>';

                        // Show the export button form
                        echo "<br>";
                        echo '<form action="export_workers_to_pdf.php" method="post">';
                        echo '<input type="hidden" name="work_ids" value="' . implode(',', $workerIds) . '">';
                        echo '<input type="hidden" name="feltetel" value="' . $kar . '">';
                        echo '<input type="hidden" name="month" value="' . $selectedMonth . '">';
                        echo '<input type="hidden" name="year" value="' . $selectedYear . '">';
                        echo '<input type="hidden" name="position" value="dekan">';
                        echo '<button type="submit" name="export_workers_pdf" value="1">Beosztások validálása és exportálása</button>';
                        echo '</form>';


                    } else {
                        echo "Még vannak függőben lévő kérelmek a választott hónapra és évre!";
                    }

                }
            } else {
                echo "Nincs jogosultságod ezt megtekinteni!";
            }
            ?>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
</body>
</html>
