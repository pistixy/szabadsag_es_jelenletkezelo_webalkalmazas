
<div>
    <fieldset>
        <h2>Válassza ki, mire módosítaná az adott napot!</h2>
        <form action="new_request.php" method="post">

            <label for="fizetett_szabadnap">
                <input type="radio" name="nap" id="fizetett_szabadnap" value="paid_leave" <?php
                if($calendarResult['day_status']=="paid_free" or
                    $calendarResult['day_status']=="paid_requested" or
                    $calendarResult['day_status']=="paid_planned" or
                    $calendarResult['day_status']=="paid_taken" )
                    {
                    echo "checked";
                }?>>
                Fizetett Szabadnap
            </label><br>

            <label for="munkanap">
                <input type="radio" name="nap" id="munkanap" value="work_day" <?php if($calendarResult['day_status']=="work_day"){
                    echo "checked";
                }?>>
                Munkanap
            </label><br>
            <label for="home_office">   
                <input type="radio" name="nap" id="home_office" value="home_office"<?php if($calendarResult['day_status']=="home_office"){
                    echo "checked";
                }?>>
                Home Office
            </label><br>

            

            <label for="unpaid_sickness_taken">
                <input type="radio" name="nap" id="unpaid_sickness_taken" value="unpaid_sickness_taken"<?php if($calendarResult['day_status']=="unpaid_sickness_taken"){
                    echo "checked";
                }?>>
                Betegszabadság
            </label><br>


          
            <?php $date=$calendarResult['date']; ?>
            
            <input type="hidden" name="date" value="<?php echo $calendarResult['date']; ?>">
            <input type="hidden" name="day_status" value="<?php echo $calendarResult['day_status']; ?>">
           
            <input type="submit" name="submit" value="Submit">
    </fieldset>
    <input type="hidden" name="view" value="<?php echo htmlspecialchars($currentView); ?>">
    </form>
</div>

