<?php
require_once 'TCPDF-main/tcpdf.php';
include "session_check.php";
include "connect.php";

// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

$userWorkID = $_SESSION['work_id'];

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
    $kar = $user['kar'];
    $szervezetszam = $user['szervezetszam'];
    $name = $user['name'];
    $adoazonosito = $user['adoazonosito'];
    $lakcim=$user['cim'];
} else {
    // No users found, handle this case as needed
    echo "No user found with the specified work ID.";
}

$month = date("F");

// Fetch the commutes from the database
$sql = "SELECT * FROM commute WHERE work_id = :userWorkID ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$stmt->execute();
$commutes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if there are commutes to export
if (empty($commutes)) {
    echo "No commutes to export.";
    exit;
}

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('My Commutes');
$pdf->SetSubject('Commutes Export');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('dejavusans', '', 10);

// Define the HTML content
$html = <<<EOD
<h1 style="text-align: center; font-size: 10px;">SZÉCHENYI ISTVÁN EGYETEM</h1>
<div style="font-size: 9px;">
<p><strong>Dolgozó szervezeti egysége:</strong> ...$kar / $szervezetszam...</p>
<p style="text-align: center"><strong>IGÉNYBEJELENTÉS ÉS IGAZOLÁS A MUNKÁBAJÁRÁS KÖLTSÉGTÉRÍTÉSÉHEZ</strong></p>
<p style="text-align: center;">...$month... HÓRA</p>
<table style="width:100%; border-collapse: collapse; padding: 1px; margin: 1px;" border="1" >
<tr>
<td style="width:10%;"><strong>Név</strong></td>
<td style="width:50%;">$name</td>
<td style="width:20%;"><strong>Adóazonosító jel:</strong></td>
<td style="width:20%;">$adoazonosito</td>
</tr>
<tr>
<td style="width:10%";><strong>Lakcím</strong></td>
<td style="width:90%";>$lakcim</td>
</tr>
</table>
</div>
<div style="margin: 0px; padding: 0px; font-size: 9px;">
    <p style="margin: 0px; padding: 0px;"><strong>Közösségi közlekedéssel utazók:</strong></p>
    <p style="margin: 0px; padding: 0px;">Bizonylattal elszámolt bérlet, ill. jegyek teljes ára: ................................</p>
    <p style="margin: 0px; padding: 0px;">Térített összeg (86 %): ................................</p>
    <p style="margin: 0px; padding: 0px;"><strong>Bérlettel nem rendelkezők:</strong></p>
  <p>A bizonylatokat kérjük dátum szerinti sorba rendezve mellékelni!</p>
    <p>Munkában töltött napok száma (amikor a dolgozó költségtérítésre jogosult):</p>
    <p>201... ................................hónap</p>
</div>
EOD;

$commuteSql = "SELECT * FROM commute WHERE work_id = :workId";
$commuteStmt = $conn->prepare($commuteSql);
$commuteStmt->bindParam(':workId', $userWorkID, PDO::PARAM_INT);
$commuteStmt->execute();
$commuteEntries = $commuteStmt->fetchAll(PDO::FETCH_ASSOC);

// Create an array to hold dates with commutes
$commuteDates = [];
foreach ($commuteEntries as $entry) {
    // Assume the date is stored in a column named 'date' and the mode of commute in 'how'
    $date = new DateTime($entry['date']);
    $day = $date->format('j'); // Day of the month
    $commuteDates[$day] = [
        'day' => $day,
        'how' => $entry['how'] // Storing the mode of commute
    ];
}

$html .= '<table border="1" cellpadding="4">';

// Generate first row with numbers 1-15
$html .= '<tr>';
for ($i = 1; $i <= 15; $i++) {
    $html .= "<td>$i</td>";
}
$html .= '<td></td>'; // The 16th cell in the first row to make it even
$html .= '</tr>';

// Generate second row with empty cells or 'EE'/'E'/'K' for commute days
$html .= '<tr>';
for ($i = 1; $i <= 16; $i++) {
    if ($i==16){
        $html .= "<td></td>";
        break;
    }
    if (isset($commuteDates[$i])) {
        switch ($commuteDates[$i]['how']) { // Assume 'how' is a column in your commute table
            case "Oda_Vissza":
                $html .= "<td>EE</td>";
                break;
            case "Car":
                $html .= "<td>E</td>";
                break;
            case "PublicTransport":
                $html .= "<td>K</td>";
                break;
            default:
                $html .= "<td></td>"; // Default case for unexpected values
        }
    } else {
        $html .= "<td></td>";
    }
}
$html .= '</tr>';

// Generate third row with numbers 16-31
$html .= '<tr>';
for ($i = 16; $i <= 31; $i++) {
    $html .= "<td>$i</td>";
}
$html .= '</tr>';

// Generate fourth row with empty cells or 'X'/'EE'/'E'/'K' for commute days
$html .= '<tr>';
for ($i = 1; $i <= 16; $i++) {
    $index = $i + 15; // Add 15 to index to match dates 16-31
    if (isset($commuteDates[$index])) {
        switch ($commuteDates[$index]['how']) { // Assume 'how' is a column in your commute table
            case "Oda_Vissza":
                $html .= "<td>EE</td>";
                break;
            case "Car":
                $html .= "<td>E</td>";
                break;
            case "PublicTransport":
                $html .= "<td>K</td>";
                break;
            default:
                $html .= "<td></td>"; // Default case for unexpected values
        }
    } else {
        $html .= "<td></td>";
    }
}
$html .= '</tr>';

$html .= '</table>';
$html .= <<<EOD
<div style=" font-size: 9px; padding: 0px; margin: 0px;">
    <p>Kérem, hogy a megfelelő naptári nap alatti kódkockákban jelölje az utazás jellegét a következők szerint:</p>
    <ul>
        <li><strong>K:</strong> közösségi közlekedés (busz, vonat, hajó, komp, stb.)</li>
        <li><strong>E:</strong> egyéb eszközzel (gépkocsi, kerékpár stb.) csak egyszeri oda vagy visszaút</li>
        <li><strong>EE:</strong> egyéb eszközzel közlekedés egy napon belül oda-vissza út</li>
    </ul>
    <p>Alulírott utalványozásra jogosult vezető igazolom, hogy nevezett munkavállaló a fentiekben részletezettek szerint jogosult a munkába járás költségtérítésére.</p>
    <p>Győr, 201.. ................................................</p>
    <p>............................................................ igénylő közalkalmazott ............................................................ munkahelyi vezető</p>
    <p style="text-align: center;"><strong>LEADÁSI HATÁRIDŐ A BÉR- ÉS MUNKÁÜGYI OSZTÁLYHOZ:
     TARGYHÓT KÖVETŐ HÓNAP 15. NAPJA!</strong></p>
    <p  style="font-style: italic; font-size: 8px;">Munkába járáshoz kapcsolódó utazási költségértés első igénybejelentése előtt (egy alkalommal) ki kell tölteni a MUNKÁÜGY-13-2017.sz. nyomtatványt, egyúttal szíveskedjenek megismerni a 7/2016. számú rektori-kancellári körlevél ide vonatkozó rendelkezéseit.</p>
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
$pdf->Output('my_commutes.pdf', 'D');

//============================================================+
// END OF FILE
//============================================================+
