<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Munkábajárási</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSS for the selected button */
        .selected {
            background-color: #333; /* Darker color for the selected button */
            color: #fff; /* White text color */
        }
    </style>
    <script>
        // Function to handle option selection
        function selectOption(option) {
            // Remove the 'selected' class from all buttons
            var buttons = document.querySelectorAll('.selector-option');
            buttons.forEach(function(button) {
                button.classList.remove('selected');
            });
            // Add the 'selected' class to the clicked button
            document.getElementById(option).classList.add('selected');

            // Update the URL with the selected option
            window.location.href = window.location.pathname + "?option=" + option;
        }

        // Function to set the selected state based on the URL parameter
        // Function to set the selected state based on the URL parameter
        function setSelectedOption() {
            var params = new URLSearchParams(window.location.search);
            var option = params.get('option');
            if (option) {
                var selectedButton = document.getElementById(option);
                if (selectedButton) {
                    selectedButton.classList.add('selected');
                }
            } else {
                // If no option is selected, default to 'egyszeri'
                var defaultButton = document.getElementById('egyszeri');
                if (defaultButton) {
                    defaultButton.classList.add('selected');
                }
            }
        }


        // Call the function when the page loads
        window.onload = setSelectedOption;
    </script>
</head>
<body>
<div class="body-container">
    <div class="navbar">
        <?php
        include "session_check.php";
        include "nav-bar.php";
        ?>
    </div>
    <div class="main-content">
        <div class="my-commutes">
            <div class="selector-container">
                <!-- Add onclick event to each selector option -->
                <div class="selector-option" id="egyszeri" onclick="selectOption('egyszeri')">Egyszeri munkábajárás</div>
                <div class="selector-option" id="berlet" onclick="selectOption('berlet')">Bérlet hozzáadása</div>
            </div>

            <?php
            // Check which option is selected
            if (isset($_GET['option'])) {
                $selectedOption = $_GET['option'];
                if ($selectedOption === "egyszeri") {
                    include "egyszeri.php";
                } elseif ($selectedOption === "berlet") {
                    include "berlethozzaadasa.php";
                }
            }
            else{
                $selectedOption ="egyszeri";
                include "egyszeri.php";
            }
            ?>
        </div>
        <div class=footer-div>
            <?php
            include "footer.php";
            ?>
        </div>
    </div>
</div>
</body>
</html>
