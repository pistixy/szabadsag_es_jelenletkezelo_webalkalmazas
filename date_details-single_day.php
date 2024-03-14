<p>Nap: <?php echo date('l', strtotime($calendarResult['date'])); ?></p>
<p>Státusz: <?php echo getStatusName($calendarResult['day_status'])?></p>
<p>Megjegyzés: <?php echo $calendarResult['comment']; ?></p>


<?php
include "active_requests.php";

if ($_SESSION['is_user']==false){
    include "list_day_users.php";
}

include "day_selector.php"
?>