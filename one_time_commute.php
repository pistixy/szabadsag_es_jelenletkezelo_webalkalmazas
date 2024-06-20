<div class="main-content">
    <form action="newcommute.php" method="post" enctype="multipart/form-data">
        <fieldset class="comming-to-work styled-fieldset">
            <div class="form-group">
                <label for="date">Válasszon dátumot:</label>
                <input type="date" id="date" name="date" required class="styled-input">
            </div>

            <div class="form-group">
                <label for="how">Válassza ki hogyan jött munkába aznap:</label>
            </div>
            <div class="radio-container">
                <label class="styled-label">
                    <input type="radio" name="how" value="Car" class="styled-radio" checked>
                    <span>Autóval (fix összeg)</span>
                </label>
                <label class="styled-label">
                    <input type="radio" name="how" value="PublicTransport" class="styled-radio">
                    <span>Közösségi közlekedéssel (86%-os menetjegyár térítés)</span>
                </label>
                <label class="styled-label">
                    <input type="radio" name="how" value="Oda_Vissza" class="styled-radio">
                    <span>Oda-vissza egy nap alatt (távolság alapú elbírálás)</span>
                </label>
            </div>

            <div id="PublicTransport" class="form-group" style="display: none;">
                <div>
                    <label>Adja meg a számlán szereplő értéket! (ft)</label>
                    <input type="text" id="price" name="price" class="styled-input">
                </div>
                <div>
                    <label>Töltse fel jegyeit:</label>
                    <input type="file" accept="image/gif, image/jpg, image/png, image/jpeg, application/pdf" name="receipt" class="styled-input">
                </div>
            </div>

            <button class="action-button action-button-bigger" type="submit" name="upload_receipt">
                <img src="public/images/icons/upload_2_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Upload">
                Feltöltés
            </button>
        </fieldset>
    </form>
</div>

<script>
    const radioButtons = document.querySelectorAll('input[name="how"]');
    const publicTransportSection = document.getElementById("PublicTransport");

    radioButtons.forEach(function (radioButton) {
        radioButton.addEventListener("change", function () {
            updatepublicTransportSection();
        });
    });

    function updatepublicTransportSection() {
        if (document.querySelector('input[name="how"][value="PublicTransport"]').checked) {
            publicTransportSection.style.display = "block";
        } else {
            publicTransportSection.style.display = "none";
        }
    }

    updatepublicTransportSection();
</script>
