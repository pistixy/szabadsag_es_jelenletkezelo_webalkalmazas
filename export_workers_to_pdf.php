<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);           
require_once 'TCPDF-main/tcpdf.php';
include "connect.php"; // Adatbázis kapcsolatot biztosító fájl include-olása
include "function_translate_month_to_Hungarian.php";
//var_dump($_POST); //debug
// Ellenőrizzük, hogy az év és hónap paraméterek be vannak-e állítva
if (isset($_POST['year']) && isset($_POST['month'])&& isset($_POST['feltetel'])&& isset($_POST['work_ids'])) {
    // Az év, hónap és munkaazonosító értékeinek lekérése a POST paraméterekből
    $year = $_POST['year'];
    $month = $_POST['month'];
    $workIds = explode(',', $_POST['work_ids']);
    $feltetel =$_POST['feltetel'];

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
    // HTML tartalom definíciója
    $html = '<h1 style="text-align: center;"> '.$feltetel . " $year" . " $HungarianMonth ". '-i beosztás '. $feltetel . ' számára</h1>';

    // Táblázat kezdése
    $html .= '<table border="1" cellpadding="4">';

    $baseWidth = 16; // basewidth
    $totalDayColumns = cal_days_in_month(CAL_GREGORIAN, $month, $year); // Number of days in the month
    $summaryColumns = 9; // Number of summary columns (F, E, T, J, A, O, I, B, H)
    $totalColumns = $totalDayColumns + $summaryColumns;

    // A táblázat fejlécének megadása
    $html .= '<tr>';
    $html .= '<th width="' . (4 * $baseWidth) . '" colspan="2"></th>';
    $html .= '<th width="' . (4 * $baseWidth) . '" colspan="1"></th>';
    $html .= '<th style="text-align: center;" width="' . ($baseWidth * $totalDayColumns) . '" colspan="' . $totalDayColumns . '">LE NEM DOLGOZOTT NAPOK JELÖLÉSE</th>'; // Header for the days of the month
    $html .= '<th style="text-align: center;" width="' . ($baseWidth * $summaryColumns) . '" colspan="' . $summaryColumns . '">TÁVOLLET ÖSSZESÍTÉSE (nap)</th>'; // Header for the summary
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<th width="' . (4 * $baseWidth) . '">Adoazonosito</th>'; //3 szor szélesebb oszlop
    $html .= '<th width="' . (4 * $baseWidth) . '">Név</th>'; // 3 szor szélesebb oszlop
    
    // dinamukus fejlec generalas
    for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++) {
        $html .= '<th width="' . $baseWidth . '">' . $i . '</th>'; // Normal szelesség
    }
        $html .= "<th width=". $baseWidth ." >F</th>";
        $html .= "<th width=". $baseWidth ." >E</th>";
        $html .= "<th width=". $baseWidth ." >T</th>";
        $html .= "<th width=". $baseWidth ." >J</th>";
        $html .= "<th width=". $baseWidth ." >A</th>";
        $html .= "<th width=". $baseWidth ." >O</th>";
        $html .= "<th width=". $baseWidth ." >I</th>";
        $html .= "<th width=". $baseWidth ." >B</th>";
        $html .= "<th width=". $baseWidth ." >H</th>";
    $html .= '</tr>';

    // Adatok bejárása és táblázatba írása
    foreach ($workIds as $workId) {
        $sql ="SELECT * from users where work_id= :workId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':workId', $workId, PDO::PARAM_INT); // Corrected the case to match the placeholder
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $name=$user["name"];
        $adoazonosito=$user["adoazonosito"];

        $html .= '<tr>';
        $html .= '<td>' . $adoazonosito . '</td>'; // adoazonosito
        $html .= '<td>' . $name . '</td>'; // név

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
        $E=0;
        $T=0;
        $J=0;
        $A=0;
        $O=0;
        $I=0;
        $B=0;
        $H=0;
        // Generate empty cells for each day of the month
        for ($i = 1; $i < cal_days_in_month(CAL_GREGORIAN, $month, $year)+1; $i++) { // Adjusted loop to start from 1 and end at 16
            if (isset($calendar[$i - 1])) { // Subtract 1 to match array indexing
                $dayStatus = $calendar[$i - 1]['day_status']; // Subtract 1 to match array indexing
                // Append the day status to the HTML table cell
                switch ($dayStatus) {
                    case "payed_free":
                    case "payed_requested":
                    case "payed_planned":
                    case "payed_taken":
                        $output = "F";
                        $F++;
                        break;
        
                    case "payed_past_free":
                    case "payed_past_requested":
                    case "payed_past_planned":
                    case "payed_past_taken":
                        $output = "E";
                        $E++;
                        break;
        
                    case "payed_edu_free":
                    case "payed_edu_requested":
                    case "payed_edu_planned":
                    case "payed_edu_taken":
                        $output = "T";
                        $T++;
                        break;
        
                    case "payed_award_free":
                    case "payed_award_requested":
                    case "payed_award_planned":
                    case "payed_award_taken":
                        $output = "J";
                        $J++;
                        break;
        
                    case "unpayed_dad_free":
                    case "unpayed_dad_requested":
                    case "unpayed_dad_planned":
                    case "unpayed_dad_taken":
                        $output = "A";
                        $A++;
                        break;
        
                    case "unpayed_home_free":
                    case "unpayed_home_requested":
                    case "unpayed_home_planned":
                    case "unpayed_home_taken":
                        $output = "O";
                        $O++;
                        break;
        
                    case "unpayed_free":
                    case "unpayed_requested":
                    case "unpayed_planned":
                    case "unpayed_taken":
                        $output = "I";
                        $I++;
                        break;
        
                    case "work_day":
                        $output = "";
                        break;
        
                    case "weekend":
                        $output = "X";
                        break;
        
                    case "holiday":
                        $output = "";
                        break;
        
                    case "unpayed_sickness_taken":
                        $output = "B";
                        $B++;
                        break;
        
                    case "unpayed_uncertified_taken":
                        $output = "H";
                        $H++;
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
        $html .= "<td>$E</td>";
        $html .= "<td>$T</td>";
        $html .= "<td>$J</td>";
        $html .= "<td>$A</td>";
        $html .= "<td>$O</td>";
        $html .= "<td>$I</td>";
        $html .= "<td>$B</td>";
        $html .= "<td>$H</td>";
        $html .= '</tr>';
    }

    
    // Táblázat lezárása
    $html .= '</table>';



    // PDF-hez tartalom hozzáadása HTML formában
    $pdf->writeHTML($html, true, false, true, false, '');

    // PDF lezárása és kimenetele
    $pdf->Output($title . '.pdf', 'D');
} else {
    // Ha az év és hónap paraméterek nincsenek beállítva, hibaüzenet kiírása
    echo "Year and month parameters are required.";
    exit;
}
?>