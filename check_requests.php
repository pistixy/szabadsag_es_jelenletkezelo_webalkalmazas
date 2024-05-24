<?php
if (count($workerIds) != 0) {
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



// korlátok definiálása
    $firstDay = date("Y-m-d", mktime(0, 0, 0, $selectedMonth, 1, $selectedYear+2));
    $lengthOfMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear+2);
    $lastDay = date("Y-m-d", mktime(0, 0, 0, $selectedMonth, $lengthOfMonth, $selectedYear+2));

//echo "TODO: ";
//echo $firstDay," ", $lastDay;
//foreach ($workerIds as $workID){
//    echo $workID, " ";
//}
    include 'holidayarray.php'; // Include the array with public holidays

    $conn->beginTransaction();

    try {
        $insertQuery = "INSERT INTO calendar (work_id, date, day_status, comment) VALUES (:work_id, :date, :day_status, :comment)";
        $insertStmt = $conn->prepare($insertQuery);
        $i=0; //változtatott sorok száma
        foreach ($workerIds as $workID) {
            // Retrieve all existing records for the current work_id within the date range
            $existingRecordsQuery = $conn->prepare("SELECT date FROM calendar WHERE work_id = :work_id AND date BETWEEN :firstDay AND :lastDay");
            $existingRecordsQuery->execute([':work_id' => $workID, ':firstDay' => $firstDay, ':lastDay' => $lastDay]);
            $existingDates = $existingRecordsQuery->fetchAll(PDO::FETCH_COLUMN, 0);

            $period = new DatePeriod(
                new DateTime($firstDay),
                new DateInterval('P1D'),
                (new DateTime($lastDay))->modify('+1 day')
            );

            foreach ($period as $date) {
                $formatted_date = $date->format("Y-m-d");

                if (!in_array($formatted_date, $existingDates)) {
                    $day_status = $date->format('N') <= 5 ? "work_day" : "weekend";
                    $comment = in_array($date->format("m-d"), $publicHolidays) ? "Ünnep" : "";
                    if ($comment === "Ünnep") {
                        $day_status = "holiday"; // Adjust the day_status based on your requirements
                    }

                    // Use prepared statement for each insert
                    $insertStmt->execute([
                        ':work_id' => $workID,
                        ':date' => $formatted_date,
                        ':day_status' => $day_status,
                        ':comment' => $comment
                    ]);
                    $i++;
                }
            }
        }
        $conn->commit();
        echo "Megjegyzés: ", $i, " rekord változott. (új rekordok jöttek létre)";

    } catch (PDOException $e) {
        $conn->rollback();
        echo "Error during record creation: " . $e->getMessage();
    }
}else{
    echo "Az nincs megjeleníthető adat, az adott karhoz.";
}
?>

