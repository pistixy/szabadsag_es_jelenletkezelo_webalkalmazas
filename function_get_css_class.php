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
            return ""; // default, amikor ismeretlen státuszt talál vagy nincs megadva
    }
}
//Nincs használatban
?>
