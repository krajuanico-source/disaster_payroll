<?php
include "dbconnect.php";  // Database connection

if (isset($_POST['bene_id'])) {
    $bene_id = $_POST['bene_id'];

// Fetch beneficiary details
$sql_bene = "SELECT * FROM tbl_bene WHERE id = '$bene_id'";
$result_bene = $conn->query($sql_bene);

if ($result_bene->num_rows > 0) {
    echo "<form id='updateStatusForm'>";

    while ($bene = $result_bene->fetch_assoc()) {
?>

       <div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label>No:</label>
					<input type="text" class="form-control" name="no" value="<?php echo $bene['no']; ?>" disabled />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label>First Name:</label>
					<input type="text" class="form-control" name="fname" id="fname" value="<?php echo $bene['fname']; ?>" disabled />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label>Middle Name:</label>
					<input type="text" class="form-control" name="mname" id="mname" value="<?php echo $bene['mname']; ?>" disabled />
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label>Last Name:</label>
					<input type="text" class="form-control" name="lname" id="lname" value="<?php echo $bene['lname']; ?>" disabled />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label>Date of Birth:</label>
					<input type="text" class="form-control" name="dob" id="dob" value="<?php echo $bene['dob']; ?>" disabled />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label>Gender</label>
					<select id="sex" class="form-control" required>
						<option value="<?php echo $bene['bene_sex']; ?>"><?php echo $bene['bene_sex']; ?></option>
						<option value="Male">Male</option>
						<option value="Female">Female</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
		<?php if($bene['status']=='Validated'){?>
			<div class="col-md-12"><center>
					<button type='button' class='btn btn-primary claimed-status-btn equal-width-btn button-spacing' data-bene-id='<?php echo $bene['id']; ?>'>Claim</button>
				</div>
			</div>
		<?php }?>
		</div>


    </form>
	<?php
}
}
}
?>
<script>
$(document).ready(function() {
    // Handle update status button click
    $('.claimed-status-btn').click(function() {
    var beneId = $(this).data('bene-id');
    var fname = $('#fname').val();
    var mname = $('#mname').val();
    var lname = $('#lname').val();
    var dob = $('#dob').val();
    var sex = $('#sex').val();
    var selectedStatus = "Claimed";
	
	if (sex == "") {
			alert("Please select Gender");
			return false; // Prevent the AJAX request from being sent
		}
    // Confirmation dialog
    if (confirm("Are you sure you want to validate this beneficiary?")) {
		
        // AJAX request to update the details in the database
        $.ajax({
            url: 'update_status.php',  // The PHP file that handles the update
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
                $('#payrollModal').modal('hide'); // Show the modal
                if (response >= 1) {
                    // Show the success message in a modal
                    $('#successModal .modal-title').text('Success');
                    $('#successModal .modal-body').html('<center><span style="color: green"><h2>Payroll ID: ' + response + '</h2></span></center>');
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
                }, 5000); // Reload the page after 2 seconds
            },
            error: function() {
                alert('Error updating status and details. Please try again.');
            }
        });
    } else {
        // If the user cancels the confirmation dialog, do nothing
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
			alert("Please select Gender");
			return false; // Prevent the AJAX request from being sent
		}
		
    // Confirmation dialog
    if (confirm("Are you sure you want to disqualify this beneficiary?")) {

        // AJAX request to update the details in the database
        $.ajax({
            url: 'update_status.php',  // The PHP file that handles the update
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
                $('#payrollModal').modal('hide'); // Show the modal
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
    } else {
        // If the user cancels the confirmation dialog, do nothing
        return false;
    }
});
});
</script>
<script>	
$(document).ready(function() {	
	$('.replacement-status-btn').click(function() {
    var beneId = $(this).data('bene-id');
    var fname = $('#fname').val();
    var mname = $('#mname').val();
    var lname = $('#lname').val();
    var dob = $('#dob').val();
    var sex = $('#sex').val();
    var selectedStatus = "Replacement";
	
	if (sex == "") {
		alert("Please select Gender");
		return false; // Prevent the AJAX request from being sent
	}
		
    // Confirmation dialog
    if (confirm("Are you sure you want to Replace this beneficiary?")) {


        // AJAX request to update the details in the database
        $.ajax({
            url: 'update_status.php',  // The PHP file that handles the update
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
                $('#payrollModal').modal('hide'); // Show the modal
                if (response >= 1) {
                    // Show the success message in a modal
                    $('#successModal .modal-title').text('Success');
                    $('#successModal .modal-body').html('<center><span style="color: green"><h2>Payroll ID #: ' + response + '</h2></span></center>');
                    $('#successModal').modal('show');
                } else {
                    // Show the success message in a modal
                    $('#successModal .modal-title').text('Warning');
                    $('#successModal .modal-body').html('<center><span style="color: red"><h2> The beneficiary’s status has been updated to Replacement!</h2></span></center>');
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
    } 
	else {
        // If the user cancels the confirmation dialog, do nothing
        return false;
    }
	});
});
</script>

