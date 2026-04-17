<?php
header('Content-Type: text/html; charset=UTF-8');
include "dbconnect.php";  // Database connection
$conn->query("SET NAMES 'utf8mb4'");
error_reporting(0);

if (isset($_POST['bene_id'])) {
    $bene_id = $_POST['bene_id'];
    $user_id = $_POST['user_id'];
    $user_type = $_POST['user_type'];

    // Fetch beneficiary details
    $sql_bene = "SELECT * FROM ect_clean_list WHERE id = '$bene_id'";
    $result_bene = $conn->query($sql_bene);

    if ($result_bene->num_rows > 0) {
        echo "<form id='updateStatusForm'>";
        while ($bene = $result_bene->fetch_assoc()) {
?>
           <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>No:</label>
                        <input type="text" class="form-control" name="no"
                            value="<?php echo $bene['control_number']; ?>" disabled />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" class="form-control" name="fname" id="fname"
                            value="<?php echo strtoupper($bene['first_name']); ?>" disabled />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Middle Name:</label>
                        <input type="text" class="form-control" name="mname" id="mname"
                            value="<?php echo strtoupper($bene['middle_name']); ?>" disabled />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" class="form-control" name="lname" id="lname"
                            value="<?php echo strtoupper(str_replace('Ã', 'Ñ', $bene['last_name'])); ?>" disabled />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Extension Name:</label>
                        <input type="text" class="form-control" name="ename" id="ename"
                            value="<?php echo strtoupper($bene['extension_name']); ?>" disabled />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Sex:</label>
                        <select id="sex" class="form-control" required>
                            <option value="<?php echo $bene['sex']; ?>">
                                <?php echo strtoupper($bene['sex']); ?>
                            </option>
                            <option value="MALE">MALE</option>
                            <option value="FEMALE">FEMALE</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Birth Date:</label>
                        <input type="text" class="form-control" name="dob" id="dob"
                            value="<?php echo $bene['birth_day']; ?>" disabled />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Birth Month:</label>
                        <input type="text" class="form-control" name="dom" id="dom"
                            value="<?php echo $bene['birth_month']; ?>" disabled />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Birth Year:</label>
                        <input type="text" class="form-control" name="doy" id="doy"
                            value="<?php echo $bene['birth_year']; ?>" disabled />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>GCash Number:</label>
                        <input type="text" class="form-control" name="gcash" id="gcash"
                            value="<?php echo htmlspecialchars($bene['gcash'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled />
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>PhilSys Card Number (PCN):</label>
                        <input type="text" class="form-control" name="pcn" id="pcn"
                            value="<?php echo htmlspecialchars($bene['pcn'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"disabled />
                    </div>
                </div>

            </div>     
                <?php if ($bene['status'] == 'Disqualified') { ?>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Reason for Disqualification</label>
                            <input type="text" class="form-control" name="remarks" id="remarks" value="<?php echo $bene['remarks']; ?>" disabled />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <center>
                            <button type="button"
                                id="revertBtn"
                                class="btn btn-danger revert-details-btn equal-width-btn button-spacing"
                                style="<?php if ($user_type !== 'TEAM LEADER') echo 'display:none;'; ?>"
                                data-bene-id="<?= $bene['id']; ?>">
                                Revert
                            </button>


                                <script>
                                    document.getElementById("revertBtn").addEventListener("click", function() {
                                        var beneId = $(this).data('bene-id');

                                        // Set it in the hidden input field in the modal
                                        $('#modalBeneId').val(beneId);

                                        $('#payrollModal').modal('hide'); // Show the modal
                                        $('#tlPasswordModal').modal('show'); // Show the modal
                                    });
                                </script>
                            </center>
                        </div>

                    </div>
                <?php } ?>
            </div>

            <?php
            if ($bene['status'] == 'Correction') {
                // Fetch beneficiary details
                $sql_bene_cor = "SELECT * FROM tbl_correction_details WHERE bene_id = '$bene_id'";
                $result_bene_cor = $conn->query($sql_bene_cor);
                if ($bene_cor = $result_bene_cor->fetch_assoc())
            ?>
                <div class="row" id="new_details">
                    <div class="row" id="new_details" hidden>
                        &emsp;<label style="color:red">Add New Details</label><br>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>First Name:</label>
                                <input type="text" class="form-control" name="new_fname" id="new_fname" required value="<?php echo str_replace(['Ã', 'Ã±', 'ÃÂ', 'Ã'], 'Ñ', $bene['first_name']); ?>" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Middle Name:</label>
                                <input type="text" class="form-control" name="new_mname" id="new_mname" value="<?php echo str_replace(['Ã', 'Ã±', 'ÃÂ', 'Ã'], 'Ñ', $bene['middle_name']); ?>" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Last Name:</label>
                                <input type="text" class="form-control" name="new_lname" id="new_lname" required value="<?php echo str_replace(['Ã', 'Ã±', 'ÃÂ', 'Ã'], 'Ñ', $bene['last_name']); ?>" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date of Birth:</label>
                                <input type="date" class="form-control" name="new_dob" id="new_dob" value="<?php echo $bene_cor['new_dob']; ?>" readonly />
                            </div>
                        </div><br>
                    </div>
                <?php }
            if ($bene['status'] == 'Replacement') {
                // Fetch beneficiary details
                $sql_bene_cor = "SELECT * FROM tbl_replacement WHERE bene_id = '$bene_id'";
                $result_bene_cor = $conn->query($sql_bene_cor);
                if ($bene_cor = $result_bene_cor->fetch_assoc())
                ?>
                <?php }
                ?>
                <div class="row" id="new_details" hidden>
                    <br>&emsp;<label style="color:red">Update Beneficiary's Information</label><br>
                    <div class="col-md-3">
                    <div class="form-group">
                            <label>First Name:</label>
                            <input type="text" class="form-control text-uppercase" name="new_fname" id="new_fname" required 
                                value="<?php echo str_replace(['Ã', 'Ã±', 'ÃÂ', 'Ã'], 'Ñ', $bene['first_name']); ?>" />
                            <input type="hidden" class="form-control" name="new_amount" id="new_amount"  
                                value="<?php echo $bene['amount']; ?>" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Middle Name:</label>
                            <input type="text" class="form-control text-uppercase" name="new_mname" id="new_mname" 
                                value="<?php echo str_replace(['Ã', 'Ã±', 'ÃÂ', 'Ã'], 'Ñ', $bene['middle_name']); ?>" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Last Name:</label>
                            <input type="text" class="form-control text-uppercase" name="new_lname" id="new_lname" required 
                                value="<?php echo str_replace(['Ã', 'Ã±', 'ÃÂ', 'Ã'], 'Ñ', $bene['last_name']); ?>" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Extension Name:</label>
                            <select class="form-control text-uppercase" name="new_ename" id="new_ename" required>
                                <option value="">-- Select Extension --</option>

                                <?php
                                $extensions = ["JR", "SR", "I", "II", "III", "IV", "V"];

                                foreach ($extensions as $ext) {
                                    $selected = (strtoupper($bene['extension_name']) == $ext) ? "selected" : "";
                                    echo "<option value='$ext' $selected>$ext</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Birth Month:</label>
                                <input type="number" class="form-control" name="new_dom" id="new_dom"
                                    min="1" max="12"
                                    value="<?php echo htmlspecialchars($bene['birth_month'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Birth Date:</label>
                                <input type="number" class="form-control" name="new_dob" id="new_dob"
                                    min="1" max="31"
                                    value="<?php echo htmlspecialchars($bene['birth_day'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                            </div>
                        </div>                    
                        <div class="col-md-4">
                        <div class="form-group">
                            <label>Birth Year:</label>
                            <input type="number" class="form-control" name="new_doy" id="new_doy"
                                value="<?php echo htmlspecialchars($bene['birth_year'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                                <label>Sex:</label>
                                <select class="form-control text-uppercase" name="new_sex" id="new_sex" required>
                                    <option value="">-- Select Sex --</option>
                                    <?php
                                    $sexOptions = ["MALE", "FEMALE"];
                                    foreach ($sexOptions as $s) {
                                        $selected = (strtoupper($bene['sex']) == $s) ? "selected" : "";
                                        echo "<option value='$s' $selected>$s</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>GCash Number:</label>
                                <input type="text" class="form-control" name="gcash" id="gcash" 
                                    value="<?php echo htmlspecialchars($bene['gcash'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>PhilSys Card Number (PCN):</label>
                                <input type="text" class="form-control" name="pcn" id="pcn" 
                                    value="<?php echo htmlspecialchars($bene['pcn'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                            </div>
                        </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Province</label>
                            <select name='province' style='text-transform:uppercase' id='province' class='form-control' onchange="getIdA(this.value)">
                                <option value="<?php echo $bene['province']; ?>"> <?php echo $bene['province']; ?></option>
                                <?php
                                $query = "SELECT * FROM tbl_province where psgc like '11%'";
                                $results = mysqli_query($conn, $query);
                                //loop
                                while ($row = $results->fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $row["psgc"]; ?>"><?php echo $row["col_province"]; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <script>
                            function getIdA(val) {

                                //We create ajax function
                                $.ajax({
                                    type: "POST",
                                    url: "city.php",
                                    data: "psgc=" + val,
                                    success: function(data) {
                                        $("#citymuni").html(data);
                                    }
                                });
                            }
                        </script>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>City</label>
                            <select name='citymuni' style='text-transform:uppercase' id='citymuni' class='form-control' onchange="getIdB(this.value)">
                                <option value="<?php echo $bene['city_municipality']; ?>"> <?php echo $bene['city_municipality']; ?></option>
                                <?php
                                $queryc = "SELECT * FROM tbl_citymuni  where psgc like '11%'";
                                $resultsc = mysqli_query($conn, $queryc);
                                //loop
                                while ($rowc = $resultsc->fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $rowc["psgc"]; ?>"><?php echo $rowc["col_citymuni"]; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <script>
                            function getIdB(val) {

                                //We create ajax function
                                $.ajax({
                                    type: "POST",
                                    url: "brgy.php",
                                    data: "psgc=" + val,
                                    success: function(data) {
                                        $("#barangay").html(data);
                                    }
                                });
                            }
                        </script>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Barangay</label>
                            <select name='barangay' style='text-transform:uppercase' id='barangay' class='form-control'>
                                <option value="<?php echo $bene['barangay']; ?>"> <?php echo $bene['barangay']; ?></option>
                                <?php
                                $queryb = "SELECT * FROM tbl_brgy  where psgc like '11%'";
                                $resultsb = mysqli_query($conn, $queryb);
                                //loop
                                while ($rowb = $resultsb->fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $rowb["col_brgy"]; ?>"><?php echo $rowb["col_brgy"]; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Purok:</label>
                            <input type="text" class="form-control" name="purok" id="purok" />
                        </div>
                    </div>
                    <br>
                    <center>
                        <button type='button' class='btn btn-primary correction-details-btn equal-width-btn button-spacing' data-bene-id='<?php echo $bene['id']; ?>'>Save Details</button>
                        <center>
                </div>
                <div class="row" id="new_details_replace" hidden>
                    <hr>&emsp;<label style="color:red" hidden>Add New Details</label><br>
                    <div class="col-md-4" hidden>
                        <div class="form-group">
                            <label>First Name:</label>
                            <input type="text" class="form-control" name="new_fnamer" id="new_fnamer" />
                        </div>
                    </div>
                    <div class="col-md-4" hidden>
                        <div class="form-group">
                            <label>Middle Name:</label>
                            <input type="text" class="form-control" name="new_mnamer" id="new_mnamer" />
                        </div>
                    </div>
                    <div class="col-md-4" hidden>
                        <div class="form-group">
                            <label>Last Name:</label>
                            <input type="text" class="form-control" name="new_lnamer" id="new_lnamer" />
                        </div>
                    </div>
                    <div class="col-md-4" hidden>
                        <div class="form-group">
                            <label>Birth Date:</label>
                            <input type="number" class="form-control" name="new_birth_date" id="new_birth_date" />
                        </div>
                    </div>
                    <div class="col-md-4" hidden>
                        <div class="form-group">
                            <label>Birth Month:</label>
                            <input type="number" class="form-control" name="new_birth_month" id="new_birth_month" />
                        </div>
                    </div>
                    <div class="col-md-4" hidden>
                        <div class="form-group">
                            <label>Birth year:</label>
                            <input type="number" class="form-control" name="new_birth_year" id="new_birth_year" />
                        </div>
                    </div>
                    <br>
                    <center>
                        <button type='button' class='btn btn-primary replacement-details-btn equal-width-btn button-spacing' data-bene-id='<?php echo $bene['id']; ?>'>Save Details</button>
                </div>
                <hr>
                <div class="row">  <center>
                    <?php if ($bene['status'] == null) { ?>
                        <div class="col-md-12 buttonsdiv">
                                <button type='button' class='btn btn-primary validate-status-btn equal-width-btn button-spacing' data-bene-id='<?php echo $bene['id']; ?>'>Validate</button>
                                <?php
                                if ($user_type <> 'GO') {
                                ?>
                                    <button type='button' class='btn btn-warning replacement-status-btn equal-width-btn button-spacing' data-bene-id='<?php echo $bene['id']; ?>'>Replacement</button>
                                <?php } ?>
                                <?php
                                if ($user_type == 'TEAM LEADER' || $user_type == 'GO') {
                                ?>
                                    <button type='button' class='btn btn-warning correction-status-btn equal-width-btn button-spacing' data-bene-id='<?php echo $bene['id']; ?>'>Correction</button>
                                <?php } ?>
                                <button type='button' class='btn btn-danger disqualify-status-btn equal-width-btn button-spacing' data-bene-id='<?php echo $bene['id']; ?>'>Disqualify</button>
                        </div>
                </div>
            <?php } ?>
                </div>
                </form>
                <!-- Add the modal for inputting the reason for disqualification -->
                <div id="reasonModal" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Reason for Disqualification</h4>
                            </div>
                            <div class="modal-body">
                                <label for="reason">Reason:</label>
                                <textarea id="reason" class="form-control"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" id="submitReasonBtn">Submit</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
    <?php
        }
    }
}
    ?>
    <script>
        $('.replacement-status-btn').click(function() {
            var beneId = $(this).data('bene-id');
            if (confirm("Are you sure you want to replace details?")) {
                $('#new_details_replace').toggle();
            }
            document.querySelector(".buttonsdiv").style.display = "none";
            // You can also add some animation or effects here
        });

        $('.replacement-details-btn').click(function() {
            var beneId = $(this).data('bene-id');
            var sex = $('#sex').val();
            if (sex == "") {
                alert("Please select Sex");
                return false; // Prevent the AJAX request from being sent
            }

            var user_id = "<?php echo strtoupper($user_id); ?>";


            $.ajax({
                type: 'POST',
                url: 'save_replacement.php',
                data: {
                    bene_id: beneId,
                    user_id: user_id
                },
                success: function(response) {
                    alert("Replaced details saved successfully!");
                    location.reload();
                },
                error: function() {
                    alert("Error saving replacement details. Please try again.");
                }
            });
        });

        $('.correction-status-btn').click(function() {
            var beneId = $(this).data('bene-id');
            if (confirm("Are you sure you want to add details?")) {
                $('#new_details').toggle();
            }
            document.querySelector(".buttonsdiv").style.display = "none";
            // You can also add some animation or effects here
        });

        $('.correction-details-btn').click(function() {
            var beneId = $(this).data('bene-id');

            var newFname = $('#new_fname').val().trim();
            var newMname = $('#new_mname').val().trim(); // Optional
            var newLname = $('#new_lname').val().trim();
            var newEname = $('#new_ename').val().trim();
            var newDob = $('#new_dob').val().trim();
            var newDom = $('#new_dom').val().trim();
            var newDoy = $('#new_doy').val().trim();
            var newamount = $('#new_amount').val().trim();
            

            var province = $('#province option:selected').text().trim();
            var citymuni = $('#citymuni option:selected').text().trim();
            var barangay = $('#barangay option:selected').text().trim();
            var purok = $('#purok').val().trim();

            var sex = $('#sex').val();
            var gcash = $('#gcash').val();
            var pcn = $('#pcn').val();

            // Validation: All fields except newMname must be filled
            if (
                !newFname ||
                !newLname ||
                !newDob ||
                !newDom ||
                !newDoy ||
                !newamount ||
                province === 'Select Province' ||
                citymuni === 'Select City/Municipality' ||
                barangay === 'Select Barangay'
            ) {
                alert('Please fill in all required fields.');
                return;
            }

            var user_id = "<?php echo strtoupper($user_id); ?>";


            // Display the progress bar
            $('#progressContainer').show();
            const progressBar = $('#progressBar');
            progressBar.css('width', '0%');
            progressBar.text(`Loading Please wait...`);

            // Simulate progress (for demo purposes)
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                progressBar.css('width', `${progress}%`);
                progressBar.text(`Loading Please wait...`);
                if (progress >= 100) {
                    clearInterval(interval);
                }
            }, 200); // Adjust time as needed

            $.ajax({
                type: 'POST',
                url: 'save_correction_details.php',
                data: {
                    bene_id: beneId,
                    new_fname: newFname,
                    new_amount: newamount,
                    new_mname: newMname,
                    new_lname: newLname,
                    new_ename: newEname,
                    new_dob: newDob,
                    new_dom: newDom,
                    new_doy: newDoy,
                    sex: sex,
                    gcash: gcash,
                    pcn: pcn,
                    province: province,
                    citymuni: citymuni,
                    barangay: barangay,
                    purok: purok,
                    user_id: user_id
                },
                success: function(response) {
                    //alert(response);
                    // Stop progress bar and set to 100%
                    clearInterval(interval);
                    progressBar.css('width', '100%');
                    progressBar.text(`Loading Please wait...`);

                    // Show the modal
                    $('#payrollModal').modal('hide');
                    window.close();
                    window.open('runrdv.php', '_self');
                },
                error: function() {
                    // Stop progress bar and show error
                    clearInterval(interval);
                    progressBar.css('width', '100%').addClass('bg-danger');
                    progressBar.text('Error');

                    alert("Error saving correction details. Please try again.");
                },
                complete: function() {
                    // Hide the progress bar after 2 seconds

                }
            });
        });
        let isSubmitting = false;

        $(document).ready(function() {
            $(document).on('keydown', function(e) {
                if (e.key === "Enter" && !$(e.target).is("textarea")) {
                    e.preventDefault(); // Block Enter from submitting form
                    return false;
                }
            });
            $('.validate-status-btn').click(function() {
                if (isSubmitting) return; // Prevent double submit
                isSubmitting = true;

                var beneId = $(this).data('bene-id');
                var fname = $('#fname').val();
                var mname = $('#mname').val();
                var lname = $('#lname').val();
                var dob = $('#dob').val();
                var sex = $('#sex').val();
                var selectedStatus = "Validated";

                if (sex == "") {
                    alert("Please select Sex");
                    isSubmitting = false;
                    return false;
                }

                if (confirm("Are you sure you want to validate this beneficiary?")) {
                    $.ajax({
                        url: 'update_status.php',
                        type: 'POST',
                        data: {
                            bene_id: beneId,
                            fname: fname,
                            mname: mname,
                            lname: lname,
                            dob: dob,
                            sex: sex,
                            status: selectedStatus
                        },
                        success: function(response) {
                            $('#payrollModal').modal('hide');
                            isSubmitting = false;

                            if (response >= 1) {
                                $('#successModal .modal-title').text('Success');
                                $('#successModal .modal-body').html('<center><span style="color: green"><h2>Payroll ID: ' + response + '</h2></span></center>');
                                $('#successModal').modal('show');
                            } else if (response >= 4) {
                                $('#successModal .modal-title').text('Warning');
                                $('#successModal .modal-body').html('<center><span style="color: red"><h2>No Available SDO!</h2></span></center>');
                                $('#successModal').modal('show');
                            }

                            setTimeout(function() {
                                location.reload();
                            }, 5000);
                        },
                        error: function() {
                            isSubmitting = false;
                            alert('Error updating status and details. Please try again.');
                        }
                    });
                } else {
                    isSubmitting = false;
                    return false;
                }
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $('.disqualify-status-btn').click(function() {
                var beneId = $(this).data('bene-id');
                var fname = $('#fname').val();
                var mname = $('#mname').val();
                var lname = $('#lname').val();
                var dob = $('#dob').val();
                var sex = $('#sex').val();
                var selectedStatus = "Disqualified";

                if (sex == "") {
                    alert("Please select Sex");
                    return false; // Prevent the AJAX request from being sent
                }

                // Confirmation dialog
                if (confirm("Are you sure you want to disqualify this beneficiary?")) {
                    // Show a modal to input the reason for disqualification
                    $('#reasonModal').modal('show');

                    // Add an event listener to the "Submit" button in the modal
                    $('#submitReasonBtn').click(function() {
                        var reason = $('#reason').val().trim();

                        if (reason === "") {
                            alert("Please enter a reason.");
                            $('#reason').focus();
                            return false; // Prevent further actions
                        }

                        // AJAX request to update the details in the database
                        $.ajax({
                            url: 'update_status.php', // The PHP file that handles the update
                            type: 'POST',
                            data: {
                                bene_id: beneId,
                                fname: fname,
                                mname: mname,
                                lname: lname,
                                dob: dob,
                                sex: sex,
                                status: selectedStatus,
                                reason: reason
                            },
                            success: function(response) {
                                $('#payrollModal').modal('hide'); // Hide the original modal
                                $('#reasonModal').modal('hide'); // Hide the reason modal
                                if (response >= 1) {
                                    // Show the success message in a modal
                                    $('#successModal .modal-title').text('Success');
                                    $('#successModal .modal-body').html('<center><span style="color: green"><h2>Payroll ID #: ' + response + '</h2></span></center>');
                                    $('#successModal').modal('show');
                                } else {
                                    // Show the success message in a modal
                                    $('#successModal .modal-title').text('Warning');
                                    $('#successModal .modal-body').html('<center><span style="color: red"><h2> The beneficiary has been Disqualified!</h2></span></center>');
                                    $('#successModal').modal('show');
                                }

                                // Reload the page after a short delay
                                setTimeout(function() {
                                    location.reload();
                                }, 2000); // Reload the page after 2 seconds
                            },
                            error: function() {
                                alert('Error updating status and details. Please try again.');
                            }
                        });
                    });
                } else {
                    // If the user cancels the confirmation dialog, do nothing
                    return false;
                }
            });
        });
    </script>