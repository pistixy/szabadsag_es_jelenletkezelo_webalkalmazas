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
        <?php
        include "nav-bar.php"
        ?>
    </div>
    <div class="main-content">
        <div class="my-requests">
            <h1>Összesítők exportálása</h1>
            <?php
            $stmt = $conn->prepare("SELECT work_id FROM users WHERE kar = :kar");
            $stmt->bindParam(':kar', $kar);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $workerIds = array_column($users, 'work_id');

            // Set necessary variables for export_workers_to_pdf.php
            $currentMonth = date('m');
            $currentYear = date('Y');
            $feltetel = $kar;

            echo '<form action="export_workers_to_pdf.php" method="post">';
            // Month selector
            echo '<label for="month">Select Month:</label>';
            echo '<select name="month" id="month">';
            for ($i = 1; $i <= 12; $i++) {
                $selected = ($i == $currentMonth) ? 'selected' : '';
                echo '<option value="' . $i . '" ' . $selected . '>' . date('F', mktime(0, 0, 0, $i, 1)) . '</option>';
            }
            echo '</select>';
            // Year selector
            echo '<label for="year">Select Year:</label>';
            echo '<select name="year" id="year">';
            for ($i = date('Y'); $i >= 2010; $i--) {
                $selected = ($i == $currentYear) ? 'selected' : '';
                echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
            }
            echo '</select>';

            // Hidden inputs
            echo '<input type="hidden" name="work_ids" value="' . implode(',', $workerIds) . '">';
            echo '<input type="hidden" name="feltetel" value="' . $feltetel . '">';

            // Submit button
            echo '<button type="submit" name="export_workers_pdf" value="1">';
            echo 'Beosztások exportálása';
            echo '</button>';
            echo '</form>';
            ?>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
</body>
</html>
