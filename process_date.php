<?php
include "session_check.php";
include "connect.php";
include "nav-bar.php";
include "function_get_status_name.php";

if (isset($_POST['submit'])) {
    $selectedDate = $_POST['selectedDate']; // Sanitize this input
    $status = $_POST['status']; // Ensure this is a valid status
    $szervezetszam = $_POST['szervezetszam']; // Sanitize and validate this input
    $searchstatus = [];

    switch ($status){
        case 'work_day':
            $searchstatus=['work_day', 'payed_requested', 'payed_past_requested', 'payed_edu_requested', 'payed_award_requested', 'unpayed_dad_requested','unpayed_home_requested'];
            break;
        case 'dad_day':
            $searchstatus=['unpayed_dad_planned', 'unpayed_dad_taken'];
            break;
        case 'home':
            $searchstatus=['unpayed_home_planned', 'unpayed_home_taken'];
            break;
        case 'unpayed_sickness_taken':
            $searchstatus=['unpayed_sickness_taken'];
            break;
        case 'award':
            $searchstatus=['payed_award_planned','payed_award_taken'];
            break;
        case 'edu':
            $searchstatus=['payed_edu_planned','payed_edu_taken'];
            break;
        case 'unpayed_uncertified_taken':
            $searchstatus='unpayed_uncertified_taken';
            break;
        case 'payed':
            $searchstatus=['payed_planned','payed_taken'];
            break;
        case 'payed_past':
            $searchstatus=['payed_past_planned','payed_past_taken'];
            break;
    }

    if (!empty($searchstatus)) {
        $statusPlaceholders = implode(',', array_fill(0, count($searchstatus), '?'));

        // Prepare SQL statement
        $sql = "SELECT c.work_id, u.name, u.email, u.szervezetszam
                FROM calendar AS c
                LEFT JOIN users AS u ON c.work_id = u.work_id
                WHERE c.date = ? AND c.day_status IN ($statusPlaceholders) AND u.szervezetszam = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $selectedDate);
        foreach ($searchstatus as $index => $value) {
            $stmt->bindValue($index + 2, $value); // Correct the binding index
        }
        $stmt->bindValue(count($searchstatus) + 2, $szervezetszam);

        if ($stmt->execute()) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($result)) {
                // Display results in a table
                echo "<h2>Dolgozók a $szervezetszam számú szervezetben a $selectedDate napon ". getStatusName($status) . " státuszban voltak:</h2>";
                echo "<table><tr><th>work_id</th><th>Név</th><th>Email cím</th><th>Szervezetszám</th></tr>";
                foreach ($result as $row) {
                    echo "<tr><td><a href='profile.php?work_id=" . htmlspecialchars($row['work_id']) . "'>" . htmlspecialchars($row['work_id']) . "</a></td>";
                    echo "<td><a href='profile.php?work_id=" . htmlspecialchars($row['work_id']) . "'>" . htmlspecialchars($row['name']) . "</a></td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['szervezetszam']) . "</td></tr>";
                }
                echo "</table>";
                echo "<br>";
                echo "Összesen a kijelölt napon ($selectedDate) a $szervezetszam számú szervezetben ennyien voltak " . getStatusName($status). " státuszban: " . count($result);

                // "Jelentés küldése" button
                echo '<form action="send_report.php" method="post">';
                echo '<input type="hidden" name="selectedDate" value="' . $selectedDate . '">';
                echo '<input type="hidden" name="status" value="' . $status . '">';
                echo '<input type="hidden" name="szervezetszam" value="' . $szervezetszam . '">';
                echo '<input type="submit" value="Jelentés küldése" name="sendReport">';
                echo '</form>';

                // "Jelentés letöltése" button
                echo '<form action="download_report.php" method="post">';
                echo '<input type="hidden" name="selectedDate" value="' . $selectedDate . '">';
                echo '<input type="hidden" name="status" value="' . $status . '">';
                echo '<input type="hidden" name="szervezetszam" value="' . $szervezetszam . '">';
                echo '<input type="submit" value="Jelentés letöltése" name="downloadReport">';
                echo '</form>';
            } else {
                echo "Nincs az adott napon adott státuszban lévő dolgozó az adott szervezetből.";
            }
        }  else {
            echo "Error executing query.";
        }
    } else {
        echo "Invalid status selected.";
    }
}

include "footer.php";
?>
