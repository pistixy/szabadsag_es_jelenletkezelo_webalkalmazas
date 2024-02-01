
<div>
    <fieldset>
        <h2>Válassza ki, mire módosítaná az adott napot!</h2>
        <form action="new_request.php" method="post">

            <label for="fizetett_szabadnap">
                <input type="radio" name="nap" id="fizetett_szabadnap" value="payed_leave" <?php
                if($calendarResult['day_status']=="payed_free" or
                    $calendarResult['day_status']=="payed_requested" or
                    $calendarResult['day_status']=="payed_planned" or
                    $calendarResult['day_status']=="payed_takend" or
                    $calendarResult['day_status']=="payed_past_free" or
                    $calendarResult['day_status']=="payed_past_requested" or
                    $calendarResult['day_status']=="payed_past_planned" or
                    $calendarResult['day_status']=="payed_past_taken")
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

            <label for="online_work">
                <input type="radio" name="nap" id="online_work" value="online_work"<?php
                if($calendarResult['day_status']=="unpayed_home_free" or
                    $calendarResult['day_status']=="unpayed_home_requested" or
                    $calendarResult['day_status']=="unpayed_home_planned" or
                    $calendarResult['day_status']=="unpayed_home_taken"){
                    echo "checked";
                }?>>
                Online Munka
            </label><br>
            <label for="award_leave">
                <input type="radio" name="nap" id="award_leave" value="award_leave"<?php
                if($calendarResult['day_status']=="payed_award_free" or
                    $calendarResult['day_status']=="payed_award_requested" or
                    $calendarResult['day_status']=="payed_award_planned" or
                    $calendarResult['day_status']=="payed_arard_taken"){
                    echo "checked";
                }?>>
                Jutalmi szabadság
            </label><br>
            <label for="dad_leave">
                <input type="radio" name="nap" id="dad_leave" value="dad_leave"<?php
                if($calendarResult['day_status']=="unpayed_dad_free" or
                    $calendarResult['day_status']=="unpayed_dad_requested" or
                    $calendarResult['day_status']=="unpayed_dad_planned" or
                    $calendarResult['day_status']=="unpayed_dad_taken"){
                    echo "checked";
                }?>>
                Apanap
            </label><br>
            <label for="edu_leave">
                <input type="radio" name="nap" id="edu_leave" value="edu_leave"<?php
                if($calendarResult['day_status']=="unpayed_edu_free" or
                    $calendarResult['day_status']=="unpayed_edu_requested" or
                    $calendarResult['day_status']=="unpayed_edu_planned" or
                    $calendarResult['day_status']=="unpayed_edu_taken"){
                    echo "checked";
                }?>>
                Tanulmányi szabadság
            </label><br>
            <label for="unpayed_leave">
                <input type="radio" name="nap" id="unpayed_leave" value="unpayed_leave"<?php
                if($calendarResult['day_status']=="unpayed_free" or
                    $calendarResult['day_status']=="unpayed_requested" or
                    $calendarResult['day_status']=="unpayed_planned" or
                    $calendarResult['day_status']=="unpayed_taken"){
                    echo "checked";
                }?>>
                Fizetés nélküli igazolt távollét
            </label><br>

            <label for="unpayed_sickness_taken">
                <input type="radio" name="nap" id="unpayed_sickness_taken" value="unpayed_sickness_taken"<?php if($calendarResult['day_status']=="unpayed_sickness_taken"){
                    echo "checked";
                }?>>
                Betegszabadság
            </label><br>


            <label>Ide írja le kérését és indokolja!
                <textarea name="message" style="width: 100%; height: 200px">
Tisztelt ...!
A <?php echo htmlspecialchars($calendarResult['date']); ?> napot szeretném...

Oka:

Tisztelettel,
<?php
if (isset($_SESSION['work_id'])) {
    $userWorkID = $_SESSION['work_id'];

    $sql = "SELECT * FROM users WHERE work_id = :userWorkID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) == 1) {
        $row = $result[0];
        $name = $row['name'];
        echo $name;
    } else {
        echo "User not found.";
    }
}
?>
    </textarea>
            </label>
            <?php $date=$calendarResult['date']; ?>
            <?php echo $date;
            ?>
            <input type="hidden" name="date" value="<?php echo $date; ?>">
            <input type="submit" name="submit" value="Submit">
    </fieldset>
    <input type="hidden" name="view" value="<?php echo htmlspecialchars($currentView); ?>">



    </form>


</div>
<?php
include "footer.php";
?>
</body>
</html>