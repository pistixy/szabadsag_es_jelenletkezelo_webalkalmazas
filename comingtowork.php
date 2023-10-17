<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Munkábajárási</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
session_start();
include "nav-bar.php";
?>

<form action="newwork.php" method="post">
    <label for="date">Válasszon dátumot:</label>
    <input type="date" id="date" name="date" required>

    <label for="how">Válassza ki hogyan jött munkába aznap:</label>
    <div>
        <input type="radio" name="how" value="Car" checked>Autóval
    </div>
    <div>
        <input type="radio" name="how" value="PublicTransport">Közösségi közlekedéssel
    </div>
    <div>
        <input type="radio" name="how" value="Oda-Vissza">Oda-vissza egy nap alatt
    </div>

    <div id="file-upload" style="display: none;">
        <label for="transportReceipt">Közösségi közlekedés jegye (PDF):</label>
        <input type="file" id="transportReceipt" name="transportReceipt">
    </div>

    <input type="submit" name="newwork" value="Felvétel">
</form>

<script>
    const radioButtons = document.querySelectorAll('input[name="how"]');
    const fileUploadSection = document.getElementById("file-upload");

    radioButtons.forEach(function(radioButton) {
        radioButton.addEventListener("change", updateFileUploadSection);
    });

    updateFileUploadSection();

    function updateFileUploadSection() {
        if (radioButtons[1].checked) {
            fileUploadSection.style.display = "block";
        } else {
            fileUploadSection.style.display = "none";
        }
    }
</script>

</body>
</html>
