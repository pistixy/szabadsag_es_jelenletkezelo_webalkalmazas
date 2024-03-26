<!-- Ez a rész a tagolhatóság és késöbbi fejlesztések miatt van Külön -->
<p>Nap: <?php echo getName(date('l', strtotime($calendarResult['date']))); ?></p>
<p>Státusz: <?php echo getName($calendarResult['day_status'])?></p>
<p>Megjegyzés: <?php echo $calendarResult['comment']; ?></p>


<?php
if($calendarResult['day_status']=="holiday" or $calendarResult['day_status']=="weekend"){
    Echo "Hétvégén vagy ünnepnapon nem lehet módosításokat eszközölni.";
}else{
    include "active_requests.php";

    if ($_SESSION['is_user']==false){
        include "list_day_users.php";
    }

    include "day_selector.php";
}

?>