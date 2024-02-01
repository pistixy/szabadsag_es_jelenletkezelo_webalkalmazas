<?php

function getStatusName($statusCode) {
    $statusNames = [
        "weekend" => "Hétvége",
        "work_day" => "Munkanap",
        "holiday" => "Ünnep",
        "payed_free" => "Fizetett szabadság",
        "payed_requested" => "Fizetett kérelmezett szabadság",
        "payed_planned" => "Fizetett betervezett szabadság",
        "payed_taken" => "Fizetett felhasznált szabadság",
        "payed_past_free" => "Fizetett előző évi szabadság",
        "payed_past_requested" => "Fizetett előző évi kérelmezett szabadság",
        "payed_past_planned" => "Fizetett előző évi betervezett szabadság",
        "payed_past_taken" => "Fizetett előző évi  felhasznált szabadság",
        "payed_edu_free" => "Fizetett oktatási szabadság",
        "payed_edu_requested" => "Fizetett oktatási kérelmezett szabadság",
        "payed_edu_planned" => "Fizetett oktatási betervezett szabadság",
        "payed_edu_taken" => "Fizetett oktatási felhasznált szabadság",
        "payed_award_free" => "Fizetett jutalmi szabadság",
        "payed_award_requested" => "Fizetett jutalmi kérelmezett szabadság",
        "payed_award_planned" => "Fizetett jutalmi betervezett szabadság",
        "payed_award_taken" => "Fizetett jutalmi felhasznált szabadság",
        "unpayed_sickness_taken" => "Fizetetlen betegszabadság",
        "unpayed_uncertified_taken" => "Fizetetlen igazolatlan távollét",
        "unpayed_dad_free" => "Fizetetlen apanap",
        "unpayed_dad_requested" => "Fizetetlen kérelmezett apanap",
        "unpayed_dad_planned" => "Fizetetlen betervezett apanap",
        "unpayed_dad_taken" => "Fizetetlen felhasznált apanap",
        "unpayed_home_free" => "Fizetetlen home office",
        "unpayed_home_requested" => "Fizetetlen kérelmezett home office",
        "unpayed_home_planned" => "Fizetetlen betervezett home office",
        "unpayed_home_taken" => "Fizetetlen felhasznált home office",
        "unpayed_free" => "Fizetés nélküli igazolt távollét",
        "unpayed_requested" =>"Fizetés nélküli kérelmezett igazolt távollét",
        "unpayed_planned" => "Fizetés nélküli betervezett igazolt távollét",
        "unpayed_taken" => "Fizetés nélküli felhasznált igazolt távollét",
        // ... other statuses
        "dad_day" => "Apanap",
        "home" => "Home office",
        "award" => "Fizetett jutalom szabadság",
        "edu" => "Fizetett oktatási szabadság",
        "payed" =>"Fizetett szabadság",
        "payed_past" => "Fizetett előző évi szabadság"
        //"work_day" => "Munknap"

    ];
    return $statusNames[$statusCode] ?? "Ismeretlen";
}
