<?php
require_once 'vendor/TCPDF-main/tcpdf.php';
include "session_check.php";
include "app/config/connect.php";
include "app/helpers/function_translate_month_to_Hungarian.php";


// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}
// Check if the year and month parameters are set

if (isset($_POST['year']) && isset($_POST['month']) && isset($_POST['work_id'])) {
    // Get the year and month from the URL parameters
    $year = $_POST['year'];
    $month = $_POST['month'];
    $userWorkID =$_POST['work_id'];

    // Get the first day of the month
    $mindate = $year . '-' . $month . '-01';
    // Get the last day of the month
    $maxdate = $year . '-' . $month . '-' . date('t', strtotime($year . '-' . $month . '-01'));
    $lengthOfMonth = date('t', strtotime($mindate));
} else {
    echo "Year and month parameters are required.";
    exit; // Terminate the script if parameters are not set
}



// Fetch the users from the database
$sql ="SELECT * from users where work_id= :userWorkId";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':userWorkId', $userWorkID, PDO::PARAM_INT); // Corrected the case to match the placeholder
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if we got any user
if (!empty($users)) {
    // Assuming you're expecting one user, you'd pick the first one from the result set
    $user = $users[0]; // Get the first user

    // Now you can access the user data
    $faculty = $user['faculty'];
    $entity_id = $user['entity_id'];
    $name = $user['name'];
    $tax_number = $user['tax_number'];
    $lakcim=$user['cim'];
} else {
    // No users found, handle this case as needed
    echo "No user found with the specified work ID.";
}


// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$title=$userWorkID."_".str_replace(" ", "-", $name)."_"."$year"."_"."$month"."calendar";
// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle($title);
$pdf->SetSubject('Calendar Month Export');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('dejavusans', '', 10);

$HungarianMonth = strtoupper(translateMonthToHungarian($month));
$year = date("Y");
// Define the HTML content
$html = <<<EOD
<h1 style="text-align: center; font-size: 10px;">SZÉCHENYI ISTVÁN EGYETEM</h1>
<div style="font-size: 9px;">
<p><strong>Dolgozó szervezeti egysége:</strong> ...$faculty / $entity_id...</p>
<p style="text-align: center"><strong>DOLGOZÓ BEOSZTÁSA: $HungarianMonth HÓRA</strong></p>
<table style="width:100%; border-collapse: collapse; padding: 1px; margin: 1px;" border="1" >
<tr>
<td style="width:10%;"><strong>Név</strong></td>
<td style="width:50%;">$name</td>
<td style="width:20%;"><strong>Adóazonosító jel:</strong></td>
<td style="width:20%;">$tax_number</td>
</tr>
<tr>
<td style="width:10%"><strong>Lakcím</strong></td>
<td style="width:90%">$lakcim</td>
</tr>
</table>
</div>
EOD;

$sql = "SELECT date, day_status FROM calendar WHERE work_id = :userWorkId AND date <= :maxdate AND date >= :mindate ORDER BY date ASC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':userWorkId', $userWorkID, PDO::PARAM_INT);
$stmt->bindParam(':mindate', $mindate, PDO::PARAM_STR);
$stmt->bindParam(':maxdate', $maxdate, PDO::PARAM_STR);
$stmt->execute();
$calendar = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the number of records fetched is not equal to the length of the month
if (count($calendar) !== $lengthOfMonth) {
    $missingDays = $lengthOfMonth - count($calendar);
    $missingRecords = [];

    // Generate missing records with day_status set to "weekend"
    for ($i = 0; $i < $missingDays; $i++) {
        $date = date('Y-m-d', strtotime($mindate . " + $i days"));
        $missingRecords[] = ['date' => $date, 'day_status' => 'weekend'];
    }

    // Merge missing records with the fetched calendar records
    $calendar = array_merge($missingRecords, $calendar);
}
/*foreach($calendar as $cal){
    foreach ($cal as $c){
        echo $c." ";
    }
}*/


$html .= '<table border="1" cellpadding="4">';

// Generate first row with numbers 1-15
$html .= '<tr>';
for ($i = 1; $i <= 15; $i++) {
    $html .= "<td>$i</td>";
}
$html .= '<td></td>'; // The 16th cell in the first row to make it even
$html .= '</tr>';

// Generate second row with day status from the calendar
$html .= '<tr>';
for ($i = 1; $i < 16; $i++) { // Adjusted loop to start from 1 and end at 16
    if (isset($calendar[$i - 1])) { // Subtract 1 to match array indexing
        $dayStatus = $calendar[$i - 1]['day_status']; // Subtract 1 to match array indexing
        // Append the day status to the HTML table cell
        switch ($dayStatus) {
            case "paid_free":
            case "paid_requested":
            case "paid_planned":
            case "paid_taken":
                $output = "F";
                break;


            case "home_office":
                $output = "O";
                break;

            case "work_day":
                $output = "";
                break;

            case "weekend":
                $output = "";
                break;

            case "holiday":
                $output = "";
                break;

            case "unpaid_sickness_taken":
                $output = "B";
                break;



            default:
                $output = "";
                break;
        }

        $html .= "<td>$output</td>";
    } else {
        $html .= "<td></td>"; // Empty cell if day data is not available
    }
}
$html .= '</tr>';

// Generate third row with numbers 16-31
$html .= '<tr>';
for ($i = 16; $i <= 31; $i++) {
    $html .= "<td>$i</td>";
}
$html .= '</tr>';

// Generate fourth row with day status from the calendar
$html .= '<tr>';
for ($i = 16; $i <= 31; $i++) {
    if (isset($calendar[$i - 1])) { // Subtract 1 to match array indexing
        $dayStatus = $calendar[$i - 1]['day_status']; // Subtract 1 to match array indexing
        // Append the day status to the HTML table cell
        switch ($dayStatus) {
            case "paid_free":
            case "paid_requested":
            case "paid_planned":
            case "paid_taken":
                $output = "F";
                break;


            case "home_office":
                $output = "O";
                break;


            case "work_day":
                $output = "";
                break;

            case "weekend":
                $output = "";
                break;

            case "holiday":
                $output = "";
                break;

            case "unpaid_sickness_taken":
                $output = "B";
                break;

            default:
                $output = "";
                break;
        }

        $html .= "<td>$output</td>";
    } else {
        $html .= "<td></td>"; // Empty cell if day data is not available
    }
}
$html .= '</tr>';

$html .= '</table>';



$html .= <<<EOD
<div style=" font-size: 9px; padding: 0px; margin: 0px;">
    <p>Kérem, hogy a megfelelő naptári nap alatti kódkockákban jelölje az utazás jellegét a következők szerint:</p>
    <ul>
        <li><strong>F:</strong> Fizetett szabadság</li>
        <li><strong>O:</strong> Home office </li>
        <li><strong>B:</strong> Beteg szabadság </li>
   </li>
   </ul>
    <p>Alulírott utalványozásra jogosult vezető igazolom, hogy nevezett munkavállaló a fentiekben részletezettek szerint dolgozott a hónapban.</p>
    <p>Győr, 201.. ................................................</p>
    <p>............................................................ igénylő közalkalmazott ............................................................ munkahelyi vezető</p>
    <p style="text-align: center;"><strong>LEADÁSI HATÁRIDŐ A BÉR- ÉS MUNKÁÜGYI OSZTÁLYHOZ:
     TARGYHÓT KÖVETŐ HÓNAP 15. NAPJA!</strong></p>
</div>

<footer style="font-size: 6px;">
    <p style="font-size: 6px;"><strong>MUNKÁÜGY-6-2017</strong></p>
    <p style="font-size: 6px;"><strong>Széchenyi István Egyetem</strong></p>
    <p style="font-size: 6px;">Cím: 9026 Győr, Egyetem tér 1. 9007 Győr, Pf. 701</p>
    <p style="font-size: 6px;">Tel.: +36-96-503-400 Fax: +36-96-329-263 E-mail: [email protected] Web: <a href="http://uni.sze.hu">http://uni.sze.hu</a></p>
</footer>
EOD;

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($title.'.pdf', 'D');

//============================================================+
// END OF FILE
//============================================================+
