<?php
//Függvény a hónapnevek könnyebb olvashatóságához
function translateMonthToHungarian($monthName) {
$months = [
'January' => 'Január',
'February' => 'Február',
'March' => 'Március',
'April' => 'Április',
'May' => 'Május',
'June' => 'Június',
'July' => 'Július',
'August' => 'Augusztus',
'September' => 'Szeptember',
'October' => 'Október',
'November' => 'November',
'December' => 'December',
'01' => 'Január',
'02' => 'Február',
'03' => 'Március',
'04' => 'Április',
'05' => 'Május',
'06' => 'Június',
'07' => 'Július',
'08' => 'Augusztus',
'09' => 'Szeptember',
'1' => 'Január',
'2' => 'Február',
'3' => 'Március',
'4' => 'Április',
'5' => 'Május',
'6' => 'Június',
'7' => 'Július',
'8' => 'Augusztus',
'9' => 'Szeptember',
'10' => 'Október',
'11' => 'November',
'12' => 'December'
];
return $months[$monthName] ?? 'Ismeretlen';
}
?>