<!doctype html>
<?php
session_start();
error_reporting(0);
    include "dbconnect.php";
	
	$emm=$_SESSION['userid'];
	$user_type = $_SESSION['user_type'];
	if($emm==''||$emm==NULL){
		echo "<script>window.open('index.php','_self')</script>";
	}else{
	$sql2 = "SELECT team_leader, payroll_no, empname FROM lib_users WHERE empid='$emm'";
	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();
	$team_leader=$row2['team_leader'];
	$payroll_no=$row2['payroll_no'];
	$empname     = $row2['empname'];
	
$sql = "SELECT * FROM ect_clean_list WHERE status IN ('Claimed') and payroll_no='$payroll_no' order by status desc";
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

	<link href="css/styless.css" rel="stylesheet" />
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" />
	<link href="assets/css/animate.min.css" rel="stylesheet"/>
	<link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>
	<link href='css/googleapis.css' rel='stylesheet' type='text/css'>
	<link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="css/dataTables.min.css">

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript" charset="utf8" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" charset="utf8" src="js/dataTables.min.js"></script>

	<style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
		.modal {
			z-index: 1060;
		}
		.modal-xl {
			max-width: 70%;
			width: 70%;
		}
		tr.success {
			background-color: #dff0d8;
		}
		tr.danger {
			background-color: #f2dede;
		}

		/* Shared button styles */
		.add-btn,
		.update-btn,
		.update-status-btn,
		.view-payroll,
		.view-payrollunclaim,
		#claimButton,
		#unclaimButton {
			border-radius: 5px !important;
			padding: 6px 22px !important;
			background-color: #1d4bb6 !important;
			border-color: #1d4bb6 !important;
			color: #fff !important;
			min-width: 100px;
			letter-spacing: 0.3px;
		}
		.add-btn:hover,
		.update-btn:hover,
		.update-status-btn:hover,
		.view-payroll:hover,
		.view-payrollunclaim:hover,
		#claimButton:hover,
		#unclaimButton:hover {
			background-color: #5a7dce !important;
			border-color: #5a7dce !important;
			color: #fff !important;
		}

		/* Center Action column */
		#payrollTable td:last-child,
		#payrollTable th:last-child {
			text-align: center;
			vertical-align: middle;
		}
	</style>
</head>

<script>
	document.addEventListener("contextmenu", (event) => event.preventDefault());

	document.addEventListener("keydown", (event) => {
		if (event.shiftKey || event.ctrlKey || event.key === "F12") {
			event.preventDefault();
		}
	});

	document.addEventListener("keydown", (event) => {
		if (event.ctrlKey && event.shiftKey && ["I", "J", "C"].includes(event.key.toUpperCase())) {
			event.preventDefault();
		}
	});
</script>

<body oncontextmenu="return false">
<div class="wrapper">
	<div class="sidebar" data-color="blue" data-image="assets/img/sidebar-6.jpg">
		<input hidden id="teamLeader" value='<?php echo $team_leader;?>'/>
		<div class="sidebar-wrapper">
			<div class="logo">
				<a href="#" class="simple-text">
					<img src="images/icons/dswd_logo_white_2.png" style="width:82%; height:82%; margin-left:-3%" alt="..."/>
				</a>
			</div>
			<ul class="nav">
				<?php if($user_type=='Team Leader'){ ?>
				<li>
					<a href="dashboard.php?userid=<?php echo $emm;?>"><i class="pe-7s-file"></i><span><b>Dashboard</b></span></a>
				</li>
				<li>
					<a href="export_excel.php?userid=<?php echo $emm;?>"><i class="pe-7s-file"></i><span><b>Import CSV File</b></span></a>
				</li>
				<?php }?>
				<input hidden id="user_id" value="<?php echo $emm;?>"/>
				<?php if($user_type=='Team Leader'||$user_type=='Validator'){ ?>
				<li>
					<a href="validation.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Masterlist</b></span></a>
				</li>
				<?php }
				if($user_type=='Team Leader'||$user_type=='Validator'||$user_type=='GO'){ ?>
				<li>
					<a href="served_list.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Served List</b></span></a>
				</li>
				<?php }
				if($user_type=='Team Leader'||$user_type=='Tagger'){ ?>
				<li>
					<a href="tagger.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Tagging</b></span></a>
				</li>
				<li class="active">
					<a href="claimed.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Claimed</b></span></a>
				</li>
				<?php }
				if($user_type=='Team Leader'||$user_type=='Payroll'){ ?>
				<li>
					<a href="payroll_list.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>List of Payroll</b></span></a>
				</li>
				<?php }
				if($user_type=='Team Leader'){ ?>
				<li>
					<a href="#subPages6" data-toggle="collapse" class="collapsed"><i class="pe-7s-user"></i><span><b>Team Leader</b></span></a>
					<div id="subPages6" class="collapse">
						<ul>
							<li>
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
				<?php } ?>
			</ul>
		</div>
	</div>

	<!-- Claim Modal -->
	<div id="claimModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="claimModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="claimModalLabel">Client Details</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" class="form-control" id="bene-id-input" value="">
					<div id="payroll-details">
						<p>Beneficiary ID: <span id="bene-id-display"></span></p>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn" id="claimButton">Update</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Unclaim Modal -->
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
						<p>Beneficiary ID: <span id="bene-id-display1"></span></p>
					</div>
					<div class="form-group mt-3">
						<label for="password-input1">Enter TL Password:</label>
						<input type="password" id="password-input1" class="form-control" placeholder="Enter Team Leader Password">
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary-close" data-dismiss="modal">Close</button>
					<button class="btn" id="unclaimButton">Update</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Success Modal -->
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
						<?= strtoupper(htmlspecialchars($empname ?? '')) ?> - <?= strtoupper(htmlspecialchars($user_type ?? '')) ?>
					</a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav navbar-right">
						<li>
							<a href="logout.php"><p>Log out</p></a>
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
							<th>Validated By</th>
							<th>Tagged By</th>
							<th>Claimed Date</th>
							<th style="text-align:center;">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$ctr = 1;
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								$fname       = $row['first_name'];
								$mname       = $row['middle_name'];
								$lname       = $row['last_name'];
								$exname      = $row['extension_name'];
								$dob         = $row['birth_month'].'-'.$row['birth_day'].'-'.$row['birth_year'];
								$status      = $row['status'];
								$validated_by = $row['validated_by'];
								$tagged_by   = $row['tagged_by'];
								$claimed_date = $row['claimed_date'];

								echo "<tr>";
								echo "<td style='vertical-align:middle;'>" . $row['payroll_id'] . "</td>";
								echo "<td style='vertical-align:middle;'><span>" . strtoupper($fname . ' ' . $mname . ' ' . $lname . ' ' . $exname) . "</span></td>";
								echo "<td style='vertical-align:middle;'>" . strtoupper($dob) . "</td>";
								echo "<td style='vertical-align:middle;'>" . strtoupper($row['barangay'] . ', ' . $row['city_municipality']) . "</td>";
								echo "<td style='vertical-align:middle; text-align:center;'>" . strtoupper($status) . "</td>";
								echo "<td style='vertical-align:middle; text-align:center;'>" . strtoupper($validated_by) . "</td>";
								echo "<td style='vertical-align:middle; text-align:center;'>" . strtoupper($tagged_by) . "</td>";
								echo "<td style='vertical-align:middle; text-align:center;'>" . strtoupper($claimed_date) . "</td>";

								if ($status === 'Claimed') {
									echo "<td style='text-align:center; vertical-align:middle;'>
											<button class='btn view-payrollunclaim'
												data-bene-payroll_id='" . $row['payroll_id'] . "'
												data-bene-id='" . $row['id'] . "'
												data-fname='" . htmlspecialchars($fname, ENT_QUOTES) . "'
												data-mname='" . htmlspecialchars($mname, ENT_QUOTES) . "'
												data-lname='" . htmlspecialchars($lname, ENT_QUOTES) . "'
												data-status='" . $status . "'
												data-backdrop='false'>
											Unclaim
											</button>
										  </td>";
								} else {
									echo "<td style='text-align:center; vertical-align:middle;'>
											<button class='btn view-payroll'
												data-bene-payroll_id='" . $row['payroll_id'] . "'
												data-bene-id='" . $row['id'] . "'
												data-fname='" . htmlspecialchars($fname, ENT_QUOTES) . "'
												data-mname='" . htmlspecialchars($mname, ENT_QUOTES) . "'
												data-lname='" . htmlspecialchars($lname, ENT_QUOTES) . "'
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

<script src="js/bootstrap.bundle.min.js"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>
<script src="assets/js/demo.js"></script>

<script>
$(document).ready(function() {

	// Initialize DataTable
	$('#payrollTable').DataTable({
		"aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
		"iDisplayLength": 10,
		"columnDefs": [
			{ "orderable": false, "targets": 7 }
		]
	});

	// Open Claim Modal
	$(document).on('click', '.view-payroll', function() {
		var beneId   = $(this).data('bene-id');
		var fullName = $(this).data('lname') + ', ' + $(this).data('fname') + ' ' + $(this).data('mname');

		$('#bene-id-input').val(beneId);
		$('#bene-id-display').text(beneId);
		$('#payroll-details').html(
			'<p>Full Name: ' + fullName + '</p>' +
			'<p>Payroll ID: ' + beneId + '</p>'
		);

		$('#claimModal').modal('show');
	});

	// Handle Claim button click
	$('#claimButton').on('click', function() {
		if (confirm("Are you sure you want to proceed with the claim?")) {
			var beneId = $('#bene-id-input').val();

			if (beneId === "") {
				alert("No Beneficiary ID found.");
				return;
			}

			var claimStatus = $('.view-payroll[data-bene-id="' + beneId + '"]').data('status');

			$.ajax({
				type: "POST",
				url: "update_status_claim.php",
				data: {
					bene_id: beneId,
					status: claimStatus
				},
				success: function(response) {
					$('#claimModal').modal('hide');
					location.reload();
				},
				error: function() {
					alert('Error updating status');
				}
			});
		}
	});

	// Open Unclaim Modal
	$(document).on('click', '.view-payrollunclaim', function() {
		var beneId   = $(this).data('bene-id');
		var payrollId = $(this).data('bene-payroll_id');
		var fullName  = $(this).data('lname') + ', ' + $(this).data('fname') + ' ' + $(this).data('mname');

		if (beneId) {
			$('#bene-id-input1').val(beneId);
			$('#bene-id-display1').text(beneId);
			$('#payroll-details1').html(
				'<p>Full Name: ' + fullName + '</p>' +
				'<p>Payroll ID: ' + payrollId + '</p>'
			);
			$('#password-input1').val('');
			$('#unclaimModal').modal('show');
		} else {
			alert('No Beneficiary ID available.');
		}
	});

	// Handle Unclaim button click
	$('#unclaimButton').on('click', function() {
		var enteredPassword = $('#password-input1').val();

		if (!enteredPassword) {
			alert('Password is required.');
			return;
		}

		var teamLeader = $('#teamLeader').val();
		var beneId     = $('#bene-id-input1').val();

		$.get('getTeamLeaderPassword.php', {
			password:   enteredPassword,
			beneId:     beneId,
			teamLeader: teamLeader
		}, function(data) {
			if (data === 'Ok') {
				alert('Status updated successfully');
				location.reload();
			} else {
				alert('Invalid Password!');
			}
		}).fail(function() {
			alert('Failed to retrieve team leader password');
		});
	});

});
</script>

</body>
</html>
<?php }?>