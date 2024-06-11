<div>
    <fieldset class="styled-fieldset">
        <h2>Válassza ki, mire módosítaná az adott napot!</h2>
        <form action="new_request.php" method="post">
            <label class="styled-label" for="fizetett_szabadnap">
                <input type="radio" name="nap" id="fizetett_szabadnap" value="paid_leave" <?php
                if($calendarResult['day_status']=="paid_free" or
                    $calendarResult['day_status']=="paid_requested" or
                    $calendarResult['day_status']=="paid_planned" or
                    $calendarResult['day_status']=="paid_taken" )
                {
                    echo "checked";
                }?>>
                Fizetett Szabadnap
            </label>

            <label class="styled-label" for="munkanap">
                <input type="radio" name="nap" id="munkanap" value="work_day" <?php if($calendarResult['day_status']=="work_day"){
                    echo "checked";
                }?>>
                Munkanap
            </label class="styled-label">
            <label class="styled-label"for="home_office">
                <input type="radio" name="nap" id="home_office" value="home_office"<?php if($calendarResult['day_status']=="home_office"){
                    echo "checked";
                }?>>
                Home Office
            </label>
            <label class="styled-label" for="unpaid_sickness_taken">
                <input type="radio" name="nap" id="unpaid_sickness_taken" value="unpaid_sickness_taken"<?php if($calendarResult['day_status']=="unpaid_sickness_taken"){
                    echo "checked";
                }?>>
                Betegszabadság
            </label>

            <?php $date=$calendarResult['date']; ?>

            <input type="hidden" name="date" value="<?php echo $calendarResult['date']; ?>">
            <input type="hidden" name="day_status" value="<?php echo $calendarResult['day_status']; ?>">
            <br>
            <button class="action-button" type="submit" name="submit">
                <img src="public/images/icons/publish_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Publish">
                Kérelem elküldése
            </button>
    </fieldset>
    <input type="hidden" name="view" value="<?php echo htmlspecialchars($currentView); ?>">
    </form>
</div>
<h1 style="margin-bottom: 20px"></h1>
