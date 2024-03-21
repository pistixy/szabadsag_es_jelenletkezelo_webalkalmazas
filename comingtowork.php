<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Munkábajárási</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSS a kiválasztott gombhoz */
        .selected {
            background-color: #333; /* Sötétebb háttérszín a kiválasztott gombhoz */
            color: #fff; /* Fehér szövegszín */
        }
    </style>
    <script>
        // Függvény az opciók kiválasztásának kezelésére
        function selectOption(option) {
            // 'selected' osztály eltávolítása minden gombról
            var buttons = document.querySelectorAll('.selector-option');
            buttons.forEach(function(button) {
                button.classList.remove('selected');
            });
            // 'selected' osztály hozzáadása a kattintott gombhoz
            document.getElementById(option).classList.add('selected');

            // URL frissítése a kiválasztott opcióval
            window.location.href = window.location.pathname + "?option=" + option;
        }

        // Függvény a kiválasztott állapot beállítására az URL paraméter alapján
        function setSelectedOption() {
            var params = new URLSearchParams(window.location.search);
            var option = params.get('option');
            if (option) {
                var selectedButton = document.getElementById(option);
                if (selectedButton) {
                    selectedButton.classList.add('selected');
                }
            } else {
                // Ha nincs kiválasztott opció, alapértelmezett 'egyszeri'
                var defaultButton = document.getElementById('egyszeri');
                if (defaultButton) {
                    defaultButton.classList.add('selected');
                }
            }
        }

        // Függvény meghívása az oldal betöltésekor
        window.onload = setSelectedOption;
    </script>
</head>
<body>
<div class="body-container">
    <div class="navbar">
        <?php
        include "session_check.php"; // Munkamenet ellenőrzése
        include "nav-bar.php"; // Navigációs sáv beillesztése
        ?>
    </div>
    <div class="main-content">
        <div class="my-commutes">
            <div class="selector-container">
                <!-- onclick esemény hozzáadása minden választó opcióhoz -->
                <div class="selector-option" id="egyszeri" onclick="selectOption('egyszeri')">Egyszeri munkábajárás</div>
                <div class="selector-option" id="berlet" onclick="selectOption('berlet')">Bérlet hozzáadása</div>
            </div>

            <?php
            // Ellenőrzi, hogy melyik opció van kiválasztva
            if (isset($_GET['option'])) {
                $selectedOption = $_GET['option'];
                if ($selectedOption === "egyszeri") {
                    include "egyszeri.php"; // 'Egyszeri munkábajárás' tartalmának beillesztése
                } elseif ($selectedOption === "berlet") {
                    include "berlethozzaadasa.php"; // 'Bérlet hozzáadása' tartalmának beillesztése
                }
            }
            else{
                $selectedOption ="egyszeri";
                include "egyszeri.php"; // Alapértelmezett tartalom beillesztése
            }
            ?>
        </div>
        <div class="footer-div">
            <?php
            include "footer.php"; // Lábléc beillesztése
            ?>
        </div>
    </div>
</div>
</body>
</html>
