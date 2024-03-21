<?php
$host = 'localhost'; //adatbázis host ip cime
$port = '5432'; //adatbázis potja
$dbname = 'holidaycalendar';// adatbázis neve
$user = 'postgres';//adatbázis felhasználója
$password = '123';//adatbázis jelszava

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
