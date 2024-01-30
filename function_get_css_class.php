<?php
function getCssClass($dayStatus) {
    switch ($dayStatus) {
        case "weekend":
            return "weekend";
        case "work_day":
            return "work_day";
        case "holiday":
            return "holiday";
        /////////////HERE TODO
        default:
            return ""; // Default case if status is not recognized
    }
}
//Nincs használatban
?>
