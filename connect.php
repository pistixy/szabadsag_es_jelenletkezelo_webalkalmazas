<?php
$host = 'pg-2112129e-holidaycalendar.a.aivencloud.com'; //adatbázis host ip cime
$port = '23979'; //adatbázis portja
$dbname = 'holidaycalendar';// adatbázis neve
$user = 'avnadmin';//adatbázis felhasználója
$password = '123';//adatbázis jelszava //AVNS_v_kjZs2HVng0A48jdbt
// PDO DSN (Data Source Name) format for PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";

try {
    // Create a PDO instance as db connection 
    $conn = new PDO($dsn);

    // Set error mode to exception to handle errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   // echo "Sikeres csatlakozás!"; //Debug üzenet
} catch (PDOException $e) {
    // Csatlakozási hibák elkapása és kiírása
    echo "Sikertlen csatlakozás: " . $e->getMessage();
}
?>
