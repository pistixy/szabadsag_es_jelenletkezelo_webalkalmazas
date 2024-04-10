<?php
//Függvény a szépen olvasható kiírásokhoz
function getName($code) {
    $statusNames = [
        "weekend" => "Hétvége",
        "work_day" => "Munkanap",
        "holiday" => "Ünnep",
        "paid_free" => "Fizetett szabadság",
        "paid_requested" => "Fizetett kérelmezett szabadság",
        "paid_planned" => "Fizetett betervezett szabadság",
        "paid_taken" => "Fizetett felhasznált szabadság",
        "unpaid_sickness_taken" => "Fizetetlen betegszabadság",
        "home_office" => "Home Office",
  
        // ... más statuszok
        "pending" => "Függőben",
        "accepted" => "Elfogdva",
        "deleted" => "Törölve",
        "rejected" => "Elutasítva",

        "paid" =>"Fizetett szabadság",

        //Beosztások
        "user" => "Alkalmazott",
        "tanszekvezeto" => "Tanszékvezető",
        "dekan" => "Dékán",
        "admin" => "Adminisztrátor",

        //napok
        "Monday" => "Hétfő",
        "Tuesday" => "Kedd",
        "Wednesday" => "Szerda",
        "Thursday" => "Csütörtök",
        "Friday" => "Péntek",
        "Saturday" => "Szombat",
        "Sunday" => "Vasárnap",

        //munkábajárás típusok
        "Pass" => "Bérlet",
        "Car" => "Autó",
        "PublicTransport" => "Közösségi Közlekedés",
        "Oda_Vissza" => "Egy nap alatt oda-vissza"
        


        

    ];
    return $statusNames[$code] ?? "Ismeretlen"; //ha üres paraméterrel hívjuk, dobjon ismeretlent
}
