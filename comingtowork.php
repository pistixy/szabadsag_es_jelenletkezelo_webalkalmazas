<?php
include "session_check.php";
include "connect.php";
include "function_get_name.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Munkába járás felvétele</title>
    <link rel="stylesheet" href="styles4.css">
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
<?php include "test_top-bar.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "test_nav-bar.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <div class="my-commutes">
                <div class="selector-container">
                    <!-- onclick esemény hozzáadása minden választó opcióhoz -->
                    <div class="selector-option" id="egyszeri" onclick="selectOption('egyszeri')">Egyszeri munkába járás</div>
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
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="collapse.js"></script>
</body>
</html>
