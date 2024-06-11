<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'vendor/TCPDF-main/tcpdf.php';
include "app/config/connect.php"; // Adatbázis kapcsolatot biztosító fájl include-olása
include "app/helpers/function_translate_month_to_Hungarian.php";
//var_dump($_POST); //debug
// Ellenőrizzük, hogy az év és hónap paraméterek be vannak-e állítva
if (isset($_POST['year']) && isset($_POST['month'])&& isset($_POST['feltetel'])&& isset($_POST['work_ids'])) {
    // Az év, hónap és munkaazonosító értékeinek lekérése a POST paraméterekből
    $year = $_POST['year'];
    $month = $_POST['month'];
    $workIds = explode(',', $_POST['work_ids']);
    $feltetel =$_POST['feltetel'];
    $position =  $_POST['position'];

    // PDF dokumentum létrehozása
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $title = "Naptár_" . $feltetel . "_$year" . "_$month"; // PDF fájl címe
    // Dokumentum információinak beállítása
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle($title);
    $pdf->SetSubject('Workers Calendar Export');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    $pdf->Rotate(90);
    // Új oldal hozzáadása a PDF-hez
    $pdf->AddPage();

    // Betűtípus beállítása
    $pdf->SetFont('dejavusans', '', 6);

    $HungarianMonth= translateMonthToHungarian($month);
    $path="unilogo.png";
    $html = <<<EOD
    <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
        <tr>
            <td style="width: 20%;">
                
            </td>
            <td style="width: 60%; text-align: center;">
                <h2>SZÉCHENYI EGYETEM</h2>
                <h2>UNIVERSITY OF GYŐR</h2>
                <h3>ADATSZOLGÁLTATÁS BÉRSZÁMFEJTÉSHEZ</h3>
            </td>
            <td style="width: 20%; text-align: right;">
                <!-- Any other header content on the right -->
            </td>
        </tr>
    </table>
EOD;

    // HTML tartalom definíciója
    $html .= '<h1 style="text-align: center;"> '.$feltetel . " $year" . " $HungarianMonth ". '-i beosztás '. $feltetel . ' számára</h1>';

    // Táblázat kezdése
    $html .= '<table border="1" cellpadding="4">';

    $baseWidth = 16; // basewidth
    $totalDayColumns = cal_days_in_month(CAL_GREGORIAN, $month, $year); // Number of days in the month
    $summaryColumns = 3; // Number of summary columns (F,O,B)
    $totalColumns = $totalDayColumns + $summaryColumns;

    // A táblázat fejlécének megadása
    $html .= '<tr>';
    $html .= '<th width="' . (4 * $baseWidth) . '" colspan="2"></th>';
    $html .= '<th width="' . (4 * $baseWidth) . '" colspan="1"></th>';
    $html .= '<th width="' . (4 * $baseWidth) . '" colspan="1"></th>';
    $html .= '<th style="text-align: center;" width="' . ($baseWidth * $totalDayColumns) . '" colspan="' . $totalDayColumns . '">LE NEM DOLGOZOTT NAPOK JELÖLÉSE</th>'; // Header for the days of the month
    $html .= '<th style="text-align: center;" width="' . ($baseWidth * $summaryColumns) . '" colspan="' . $summaryColumns . '">TÁVOLLET ÖSSZESÍTÉSE (nap)</th>'; // Header for the summary
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th width="' . (4 * $baseWidth) . '">Adoazonosito</th>'; //3 szor szélesebb oszlop
    $html .= '<th width="' . (4 * $baseWidth) . '">Név</th>'; // 3 szor szélesebb oszlop
    $html .= '<th width="' . (4 * $baseWidth) . '">Tanszék</th>'; // 3 szor szélesebb oszlop
    // dinamukus fejlec generalas
    for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++) {
        $html .= '<th width="' . $baseWidth . '">' . $i . '</th>'; // Normal szelesség
    }
        $html .= "<th width=". $baseWidth ." >F</th>";
        $html .= "<th width=". $baseWidth ." >O</th>";
        $html .= "<th width=". $baseWidth ." >B</th>";

    $html .= '</tr>';

    // Adatok bejárása és táblázatba írása
    foreach ($workIds as $workId) {
        $sql ="SELECT * from users where work_id= :workId order by szervezetszam";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':workId', $workId, PDO::PARAM_INT); // Corrected the case to match the placeholder
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $name=$user["name"];
        $adoazonosito=$user["adoazonosito"];
        $szervezetszam=$user["szervezetszam"];

        $html .= '<tr>';
        $html .= '<td>' . $adoazonosito . '</td>'; // adoazonosito
        $html .= '<td>' . $name . '</td>'; // név
        $html .= '<td>' . $szervezetszam . '</td>'; // tanszek

        // Get the first day of the month
        $mindate = $year . '-' . $month . '-01';
        // Get the last day of the month
        $maxdate = $year . '-' . $month . '-' . date('t', strtotime($year . '-' . $month . '-01'));
        $lengthOfMonth = date('t', strtotime($mindate));

        $sql = "SELECT date, day_status FROM calendar WHERE work_id = :work_id AND date <= :maxdate AND date >= :mindate ORDER BY date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);
        $stmt->bindParam(':mindate', $mindate, PDO::PARAM_STR);
        $stmt->bindParam(':maxdate', $maxdate, PDO::PARAM_STR);
        $stmt->execute();
        $calendar = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $F=0;
        $O=0;
        $B=0;

        // Generate empty cells for each day of the month
        for ($i = 1; $i < cal_days_in_month(CAL_GREGORIAN, $month, $year)+1; $i++) { // Adjusted loop to start from 1 and end at 16
            if (isset($calendar[$i - 1])) { // Subtract 1 to match array indexing
                $dayStatus = $calendar[$i - 1]['day_status']; // Subtract 1 to match array indexing
                // Append the day status to the HTML table cell
                switch ($dayStatus) {
                    case "paid_free":
                    case "paid_requested":
                    case "paid_planned":
                    case "paid_taken":
                        $output = "F";
                        $F++;
                        break;

                    case "home_office":
                        $output = "O";
                        $O++;
                        break;

                    case "work_day":
                        $output = "";
                        break;

                    case "weekend":
                        $output = "X";
                        break;

                    case "holiday":
                        $output = "X";
                        break;

                    case "unpaid_sickness_taken":
                        $output = "B";
                        $B++;
                        break;

                    default:
                        $output = "";
                        break;
                }
                if ($output=="X"){
                    $html .= "<td style='background-color: #D3D3D3;'></td>";
                }else{
                    $html .= "<td>$output</td>";
                }

            } else {
                $html .= "<td></td>"; // üres, ha nincs adat
            }
        }
        //$html .= "<td></td>";
        $html .= "<td>$F</td>";
        $html .= "<td>$O</td>";
        $html .= "<td>$B</td>";
        $html .= '</tr>';
    }


    // Táblázat lezárása
    $html .= '</table>';

    $html .= <<<EOD
<table cellpadding="4" cellspacing="0" border="0" style="font-size: 8pt; width: 100%;">
    <tr>
        <td>                               </td>
        <td>                               </td>
        <td>                               </td>
        <td>__________________      _______________</td>
    </tr>
    <tr>
        <td>Győr, 20.... . ....hó ....nap</td>
        <td>F = Fizetett tárgyévi szabadság</td>
        <td>I = Fiz. nélk. igazolt távollét</td>
        <td>küldősert felelős vezető dékán</td>
    </tr>
    <tr>
        <td></td>
        <td>E = Fizetett előző évi szabadság</td>
        <td>B = Betegség</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>T = Tanulmányi szabadság</td>
        <td>H = Igazolatlan távollét</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>J = Jutalomszabadság</td>
        <td>A = Apaság</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>O = Home-office</td>
        <td></td>
    </tr>
</table>
EOD;




    // PDF-hez tartalom hozzáadása HTML formában
    $pdf->writeHTML($html, true, false, true, false, '');

    // PDF lezárása és kimenetele
    $pdf->Output($title . '.pdf', 'D');

    // ... existing code ...

    if ($position == "dekan") {
        // Define the directory path based on year and month
        $dirPath = __DIR__ . "/storage/Beosztasok/$year/$month"; // Ensure full path is used

        // Check if the directory exists, if not, create it
        if (!file_exists($dirPath)) {
            if (!mkdir($dirPath, 0777, true)) {
                error_log("Failed to create directory: $dirPath");
                die("Hiba történt a könyvtár létrehozásakor: $dirPath");
            }
        }

        // Define the file path
        $filePath = "$dirPath/$title.pdf";

        // Attempt to save the PDF file on the server
        try {
            $pdf->Output($filePath, 'F');  // Use 'F' parameter to save the file
            echo "A fájl mentése sikeres volt: $filePath";
        } catch (Exception $e) {
            error_log("Error saving file: " . $e->getMessage());
            echo "Hiba történt a fájl mentésekor: " . $e->getMessage();
        }
    } else {
        // Output the PDF to the browser for download
        $pdf->Output($title . '.pdf', 'D');
    }

} else {
    // Ha az év és hónap paraméterek nincsenek beállítva, hibaüzenet kiírása
    echo "Év és hónap paraméterek hiányoznak.";
    exit;
}
?>