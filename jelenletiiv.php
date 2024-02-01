<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Jelenlétiív</title>
    <link rel="stylesheet" href="jelenletiivstyles.css">
</head>
<body>
<?php
include "connect.php";
include "session_check.php";
include "nav-bar.php";
?>

<h1>Válasszon dátumot!</h1>
<form action="process_date.php" method="post">
    <label for="selectedDate">Dátum:</label>
    <input type="date" id="selectedDate" name="selectedDate" required>
    <br>
    <label for="szervezetszam">Adjon meg szervezetszámot</label>
        <input type="number" id="szervezetszam" name="szervezetszam">


    <fieldset>
        <legend>Válassza ki a státuszt!</legend>

        <label for="work_day">
            <input type="radio" id="work_day" name="status" value="work_day" required>
            Munkanap
        </label>

        <label for="dad_day">
            <input type="radio" id="dad_day" name="status" value="dad_day" required>
            Apanap
        </label>

        <label for="home">
            <input type="radio" id="home" name="status" value="home" required>
            Home office
        </label>

        <label for="unpayed_sickness_taken">
            <input type="radio" id="unpayed_sickness_taken" name="status" value="unpayed_sickness_taken" required>
            Betegszabadság
        </label>

        <label for="unpayed_uncertified_taken">
            <input type="radio" id="unpayed_uncertified_taken" name="status" value="unpayed_uncertified_taken" required>
            Igazolatlan távollét
        </label>

        <label for="award">
            <input type="radio" id="award" name="status" value="award" required>
            Jutalom szabadság
        </label>
        <label for="edu">
            <input type="radio" id="edu" name="status" value="edu" required>
            Tanulmányi szabadság
        </label>
        <label for="payed">
            <input type="radio" id="payed" name="status" value="payed" required>
            Fizettett szabadság
        </label>
        <label for="unpayed">
            <input type="radio" id="unpayed" name="status" value="unpayed" required>
            Fizettlen igazolt távollét
        </label>
        <label for="payed_past">
            <input type="radio" id="payed_past" name="status" value="payed" required>
            Fizetett előző évi szabadság
        </label>
        <label for="unpayed">
            <input type="radio" id="unpayed" name="status" value="unpayed" required>
            Fizettlen igazolt távollét
        </label>

    </fieldset>

    <br>
    <input type="submit" value="Submit" name="submit">
</form>

<?php
include "footer.php";
?>
</body>
</html>

