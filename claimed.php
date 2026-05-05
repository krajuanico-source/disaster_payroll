<!doctype html>
<?php
session_start();
error_reporting(0);
    include "dbconnect.php";
	
	$emm=$_SESSION['userid'];
	$user_type = $_SESSION['user_type']; // get the user type from the database or session
	if($emm==''||$emm==NULL){
		echo "<script>window.open('index.php','_self')</script>";
	}else{
	$sql2 = "SELECT team_leader, payroll_no, empname FROM lib_users WHERE empid='$emm'";
	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();
	$team_leader=$row2['team_leader'];
	$payroll_no=$row2['payroll_no'];
	$empname     = $row2['empname'];
	
// SQL query to fetch payroll list from the database
$sql = "SELECT * FROM ect_clean_list WHERE status IN ('Claimed') and payroll_no='$payroll_no' order by status desc";  // Adjust the table name to your actual payroll table
$result = $conn->query($sql);
?>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="images/icons/dswd_icon.jpg">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>DSWD FO XI - ECT Payroll System</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />


    <!-- Bootstrap core CSS     -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>
	
    <link href='css/googleapis.css' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
	

	<!-- DataTables JS -->
	<script type="text/javascript" charset="utf8" src="js/jquery.dataTables.min.js"></script>

	<script src="js/jquery.js"></script>
	<style>
		body {
		  font-size: 16px;
		}
		.modal {
		  z-index: 1060; /* increase the z-index to be higher than the page's content */
		}
		/* Custom CSS to force modal to be extra large */
		.modal-xl {
			max-width: 70%;  /* Adjust the width as per your requirement */
			width: 70%;      /* Set width to 90% of the screen */
		}
		tr.success {
			background-color: #dff0d8; /* green */
		}

		tr.danger {
			background-color: #f2dede; /* red */
		}
	</style>
	
</head>
<script>
        // Disable right-click context menu
        document.addEventListener("contextmenu", (event) => event.preventDefault());

        // Disable specific key combinations and F12
        document.addEventListener("keydown", (event) => {
            if (
                event.shiftKey || // Disable Shift key
                event.ctrlKey ||  // Disable Ctrl key
                event.key === "F12" // Disable F12
            ) {
                event.preventDefault();
                //alert("This action is disabled!");
            }
        });

        // Prevent Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C
        document.addEventListener("keydown", (event) => {
            if (event.ctrlKey && event.shiftKey && ["I", "J", "C"].includes(event.key.toUpperCase())) {
                event.preventDefault();
                //alert("Developer tools shortcuts are disabled!");
            }
        });
    </script>
<body oncontextmenu="return false">

<div class="wrapper">
    <div class="sidebar" data-color="blue" data-image="assets/img/sidebar-6.jpg">
<input hidden id="teamLeader" value='<?php echo $team_leader;?>'></input>
    <!--   you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple" -->
	<div class="sidebar-wrapper">
             <div class="logo">
			
                <a href="#" class="simple-text">
                      <img src="images/icons/dswd_logo_white_2.png" style="width:82%; height:82%; margin-left:-3%" alt="..."/>
                    </a>
            </div>
			<ul class="nav">
				
				<?php if($user_type=='Team Leader'){ ?>
                <li>
					<a href="dashboard.php?userid=<?php echo $emm;?>"><i class="pe-7s-file"></i><span><b>Dashboard</span></b> </a>
				</li>
				<?php } if($user_type=='Team Leader'){ ?>
                <li>
					<a href="export_excel.php?userid=<?php echo $emm;?>"><i class="pe-7s-file"></i><span><b>Import CSV File</span></b> </a>
				</li>
				<?php }?>
					<input hidden id="user_id" value="<?php echo $emm;?>"/>
				<?php if($user_type=='Team Leader'||$user_type=='Validator'){ ?>
				<li>
					<a href="validation.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Masterlist</span></b> </a>
				</li>
				<?php }?>
					<input hidden id="user_id" value="<?php echo $emm;?>"/>
				<?php if($user_type=='Team Leader'||$user_type=='Validator'||$user_type=='GO'){ ?>
				<li class="">
					<a href="served_list.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Served List</span></b> </a>
				</li>				
				<?php }
				if($user_type=='Team Leader'||$user_type=='Tagger'){ ?>
				<li>
					<a href="tagger.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Tagging</span></b> </a>
				</li>
                <li class="active">
					<a href="claimed.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Claimed</span></b> </a>
				</li>
				<?php }
				 if($user_type=='Team Leader'||$user_type=='Payroll'){ ?>
				<li>
					<a href="payroll_list.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>List of Payroll</span></b> </a>
				</li>
				<?php }
				 if($user_type=='Team Leader'){ ?>
				 <li>
					<a href="#subPages6" data-toggle="collapse" class="collapsed"><i class="pe-7s-user"></i><span><b>Team Leader</span></b> </a>
					<div id="subPages6" class="collapse ">
						<ul>		
							<li >
								<a href="user.php?userid=<?php echo $emm;?>">
									<i class="pe-7s-angle-right"></i>
									<p>User</p>
								</a>
								<a href="fund_source.php?userid=<?php echo $emm;?>">
									<i class="pe-7s-angle-right"></i>
									<p>Partner</p>
								</a>
							</li>
						</ul>						
					</div>
				</li>
				<!-- <li>
					<a href="https://172.31.176.49/ect/public"><i class="pe-7s-folder"></i><span><b>Liquidation</span></b> </a>
				</li> -->
				<?php } ?>
			</ul>			
    </div>
</div>

			<!-- Modal -->
			<div id="claimModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="payrollModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="payrollModalLabel">Client Details</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<input type="hidden" class="form-control" id="bene-id-input" value="">
							<div id="payroll-details">
								<!-- Payroll details content will be dynamically loaded here -->
								<p>Beneficiary ID: <span id="bene-id-display"></span></p>
							</div>
							
						</div>
						<div class="modal-footer">
						   <button class="btn btn-primary" id="claimButton">Update</button>
						</div>
					</div>
				</div>
			</div>
			<div id="unclaimModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="unclaimModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="unclaimModalLabel">Client Details</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<input type="hidden" class="form-control" id="bene-id-input1" value="">
							<div id="payroll-details1">
								<!-- Payroll details content will be dynamically loaded here -->
								<p>Beneficiary ID: <span id="bene-id-display1"></span></p>
							</div>
							<div class="form-group mt-3">
								<label for="password-input1">Enter TL Password:</label>
								<input type="password" id="password-input1" class="form-control" placeholder="Enter Team Leader Password">
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-primary" id="unclaimButton">Update</button>
							<button class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>

			<!-- Modal -->
			<div id="successModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
			  <div class="modal-dialog" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="successModalLabel">Success</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">
					<p id="successMessage"></p>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				  </div>
				</div>
			  </div>
			</div>
    <div class="main-panel">
		<nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">
						<?= strtoupper(htmlspecialchars($empname ?? '')) ?> - <?=  strtoupper(htmlspecialchars($user_type ?? '')) ?>
					</a>
                </div>
                <div class="collapse navbar-collapse">
                     <ul class="nav navbar-nav navbar-right">
                        
                        <li>
                            <a href="logout.php">
                                <p>Log out</p>
                            </a>
                        </li>
						<li class="separator hidden-lg hidden-md"></li>
                    </ul>
                </div>
            </div>
        </nav>
		<div class="content" style="width:100%">
        <div class="table-responsive">  
            <table id="payrollTable" style="width:100%" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Beneficiary Name</th>
                        <th>Date of Birth</th>
                        <th>Barangay</th>
                        <th>Status</th>
                        <th>Tagged By</th>
                        <th>Claimed Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
					<?php
					$ctr=1;
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) {
							// Fetch necessary values
							$fname 	= $row['first_name'];
							$mname 	= $row['middle_name'];
							$lname 	= $row['last_name'];
							$exname = $row['extension_name'];
							$dob 	= $row['birth_month'].'-'.$row['birth_day'].'-'.$row['birth_year'];
							$status = $row['status'];

							$tagged_by = $row['tagged_by'];
							$claimed_date = $row['claimed_date'];
							

							echo "<tr>";
							echo "<td>" . $row['payroll_id'] . "</td>";
							echo "<td><span>" . strtoupper($fname . ' ' . $mname . ' ' . $lname . ' ' . $exname) . "</span></td>";
							echo "<td>" . strtoupper($dob) . "</td>";
							echo "<td>" . strtoupper($row['barangay'] . ', ' . $row['city_municipality']) . "</td>";
							echo "<td align='center'>" . strtoupper($status) . "</td>";
							echo "<td align='center'>" . strtoupper($tagged_by) . "</td>";
							echo "<td align='center'>" . strtoupper($claimed_date) . "</td>";



							// If the status is 'Claimed', disable the button
							if ($status === 'Claimed') {
								echo "<td align='center'>
										<button class='btn btn-primary view-payrollunclaim' 
												data-bene-payroll_id='" . $row['payroll_id'] . "' 
												data-bene-id='" . $row['id'] . "' 
												data-fname='" . $fname . "' 
												data-mname='" . $mname . "' 
												data-lname='" . $lname . "' 
												data-status='" . $status . "' 
												data-backdrop='false' 
												>
											Unclaim
										</button>
									  </td>";
							} else {
								echo "<td align='center'>
										<button class='btn btn-primary view-payroll' 
												data-bene-payroll_id='" . $row['payroll_id'] . "' 
												data-bene-id='" . $row['id'] . "' 
												data-fname='" . $fname . "' 
												data-mname='" . $mname . "' 
												data-lname='" . $lname . "' 
												data-status='" . $status . "' 
												data-backdrop='false'>
											Claim
										</button>
									  </td>";
							}

							echo "</tr>";
							$ctr++;
						}
					} else {
						echo "<tr>
						<td class='text-center'>No payroll records found</td>
						<td class='text-center'>No payroll records found</td>
						<td class='text-center'>No payroll records found</td>
						<td class='text-center'>No payroll records found</td>
						<td class='text-center'>No payroll records found</td>
						<td class='text-center'>No payroll records found</td>
						<td class='text-center'>No payroll records found</td>
						<td class='text-center'>No payroll records found</td>
						</tr>";
					}
					?>
				</tbody>

            </table>
        </div>  
		
		</div>
	</div>
</div>
</body>


    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="css/dataTables.min.css">

    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="js/dataTables.min.js"></script>
	
	<script src="js/bootstrap.bundle.min.js"></script>
	
    <!--   Core JS Files   -->
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

	<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
	<script src="assets/js/demo.js"></script>

</html>
<script>
$(document).ready(function() {
    // Use event delegation for dynamically added .view-payroll elements
    $(document).on('click', '.view-payroll', function() {
        var beneId = $(this).data('bene-id'); // Get beneficiary ID
        var fullName = `${$(this).data('lname')}, ${$(this).data('fname')} ${$(this).data('mname')}`; // Get full name

        // Populate modal content with the beneficiary details
        $('#bene-id-input').val(beneId); // Set hidden input value
        $('#bene-id-display').text(beneId); // Display Beneficiary ID
        $('#payroll-details').html(`
            <p>Full Name: ${fullName}</p>
            <p>Payroll ID: ${beneId}</p>
        `);

        // Show the modal for claiming
        $('#claimModal').modal('show');
    });

    // Event listener for the 'Claim' button click to show confirmation
    $('#claimButton').on('click', function() {
        // Show a confirmation dialog
        if (confirm("Are you sure you want to proceed with the claim?")) {
            var beneId = $('#bene-id-input').val(); // Get beneficiary ID from hidden input
            var claimStatus = $('.view-payroll[data-bene-id="' + beneId + '"]').data('status'); // Get status from button

            // Optional: Debugging to ensure the input has the correct value on submission
            console.log("bene-id-input value on submit: " + beneId);

            // Exit if no Beneficiary ID
            if (beneId === "") {
                alert("No Beneficiary ID found.");
                return;
            }

            // Send the data via AJAX to PHP for processing
            $.ajax({
                type: "POST",
                url: "update_status_claim.php",  // PHP file that handles the update
                data: {
                    bene_id: beneId,
                    status: claimStatus
                },
                success: function(response) {
                    $('#claimModal').modal('hide'); // Close modal after successful submission
                    location.reload(); // Reload the page
                },
                error: function() {
                    alert('Error updating status');
                }
            });
        } else {
            console.log('Claim was not submitted.'); // Log if user cancels
        }
    });
});



$(document).ready(function() {
    // Use event delegation for dynamically added .view-payrollunclaim elements
    $(document).on('click', '.view-payrollunclaim', function() {
        var beneId = $(this).data('bene-id'); // Get beneficiary ID
        var payrollId = $(this).data('bene-payroll_id'); // Get beneficiary ID
        var fullName = `${$(this).data('lname')}, ${$(this).data('fname')} ${$(this).data('mname')}`;

        if (beneId) {
            // Populate modal content
            $('#bene-id-input1').val(beneId); // Set hidden input value
            $('#bene-id-display1').text(beneId); // Display Beneficiary ID
            $('#payroll-details1').html(`
                <p>Full Name: ${fullName}</p>
                <p>Payroll ID: ${payrollId}</p>
            `);

            // Show modal
            $('#unclaimModal').modal('show');
        } else {
            alert('No Beneficiary ID available.');
        }
    });

    // Handle 'Update' button click event to validate password
    $('#unclaimButton').on('click', function() {
        var enteredPassword = $('#password-input1').val(); // Get the entered password

			if (!enteredPassword) {
			alert('Password is required.');
			return; // Stop further execution
			}
			
        var teamLeader = $('#teamLeader').val(); // Get the entered password		
        var beneId = $('#bene-id-input1').val(); // Get the beneficiary ID from the hidden input
		//alert(teamLeader);
        // Fetch the team leader password from the server for validation
        $.get('getTeamLeaderPassword.php', {password: enteredPassword, beneId: beneId,teamLeader:teamLeader}, function(data) {
            // Log the raw response for debugging
            console.log('Raw response:', data);
			//alert(data);
            // Check if password is valid
            if (data === 'Ok') {
                alert('Status updated successfully');
                location.reload(); // Reload the page
                // You can add AJAX calls to perform the actual update here
            } else {
                alert('Invalid Password!');
            }
        }).fail(function() {
            alert('Failed to retrieve team leader password');
        });
    });
});

// Initialize DataTable
    $('#payrollTable').DataTable({
        "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
        "iDisplayLength": 10
    });
	
</script>


	<?php }?>