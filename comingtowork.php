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

    <form action="newcommute.php" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>Munkába járás térítési űrlap</legend>
            <div>
                <label for="date">Válasszon dátumot:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div>
                <label for="honnan">Kiindulási pont:</label>
                <input type="text" id="honnan" name="honnan" required>
            </div>
            <div>
                <label for="hova">Cél:</label>
                <input type="text" id="hova" name="hova" value="Győr">
            </div>

            <div>
                <label for="how">Válassza ki hogyan jött munkába aznap:</label>
            </div>
            <div>
                <input type="radio" name="how" value="Car" checked>Autóval (fix összeg)
            </div>
            <div>
                <input type="radio" name="how" value="PublicTransport">Közösségi közlekedéssel (86%-os menetjegyár térítés)
            </div>
            <div>
                <input type="radio" name="how" value="Oda_Vissza">Oda-vissza egy nap alatt (távolság alapú elbírálás)
            </div>
            <div id="PublicTransport">
                <div>
                    <label>Adja meg e számlán szerelplő értéket! (ft)</label>
                    <input type="text" id="price" name="price" >
                </div>

                <div >
                    <label>Töltse fel jegyeit PDF formátumban:</label>
                    <input type="file" accept="image/gif, image/jpg, image/png, image/jpeg, application/pdf" name="receipt">

                </div>
            </div>
            <div id="Oda_Vissza">
                    <label>Adja meg a levezetett kilóméterek számát! A teljes út kilóméterszámát írja be!</label>
                    <input type="number" id="km" name="km" >
            </div>
            <input type="submit" name="upload_receipt" value="Feltöltés">
        </fieldset>
    </form>

    <script>
        const radioButtons = document.querySelectorAll('input[name="how"]');
        const publicTransportSection = document.getElementById("PublicTransport");
        const oda_visszaSection = document.getElementById("Oda_Vissza");

        radioButtons.forEach(function (radioButton) {
            radioButton.addEventListener("change", function () {
                updatepublicTransportSection();
                updateoda_visszaSection();
            });
        });

        updatepublicTransportSection();
        updateoda_visszaSection();

        function updatepublicTransportSection() {
            if (radioButtons[1].checked) {
                publicTransportSection.style.display = "block";
            } else {
                publicTransportSection.style.display = "none";
            }
        }

        function updateoda_visszaSection() {
            if (radioButtons[2].checked) {
                oda_visszaSection.style.display = "block";
            } else {
                oda_visszaSection.style.display = "none";
            }
        }
    </script>

    <?php
    include "footer.php";
    ?>
</body>
</html>
