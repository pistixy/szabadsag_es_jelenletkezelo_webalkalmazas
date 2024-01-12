<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Jelenlétiív</title>
    <link rel="stylesheet" href="jelenletiivstyles.css">
</head>
<body>
<h1>Válasszon dátumot!</h1>
<form action="process_date.php" method="post">
    <label for="selectedDate">Dátum:</label>
    <input type="date" id="selectedDate" name="selectedDate" required>
    <br>
    <label for="szervezetszam">Adjon meg szervezetszámot</label>
        <input type="number" id="szervezetszam" name="szervezetszam">


    <fieldset>
        <legend>Válassza ki a státuszt!</legend>

        <label for="workingDay">
            <input type="radio" id="workingDay" name="status" value="1" required>
            Munkanap
        </label>

        <label for="vacationDay">
            <input type="radio" id="vacationDay" name="status" value="0" required>
            Szabadnap
        </label>

        <label for="onlineDay">
            <input type="radio" id="onlineDay" name="status" value="2" required>
            Online Munka
        </label>

        <label for="sickLeave">
            <input type="radio" id="sickLeave" name="status" value="3" required>
            Betegszabadság
        </label>

        <label for="nonPayedLeave">
            <input type="radio" id="nonPayedLeave" name="status" value="4" required>
            Fizetetlen szabadság
        </label>

        <label for="plannedVacation">
            <input type="radio" id="plannedVacation" name="status" value="5" required>
            Tervezett szabadság
        </label>
    </fieldset>

    <br>
    <input type="submit" value="Submit" name="submit">
</form>
</body>
</html>

