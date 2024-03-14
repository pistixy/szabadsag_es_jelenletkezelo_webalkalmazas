<?php
$host = 'localhost';
$port = '5432';
$dbname = 'holidaycalendar';
$user = 'postgres';
$password = '123';

// PDO DSN (Data Source Name) format for PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";

try {
    // Create a PDO instance as db connection
    $conn = new PDO($dsn);

    // Set error mode to exception to handle errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   // echo "Connected to PostgreSQL successfully!";
} catch (PDOException $e) {
    // Catch any connection error and display it
    echo "Connection failed: " . $e->getMessage();
}
?>
