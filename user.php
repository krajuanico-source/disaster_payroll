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
	
	$sql_user = "SELECT empname, user_type, payroll_no 
             FROM lib_users 
             WHERE empid = '$emm'";

        $result_user = $conn->query($sql_user);

        if ($result_user && $row = $result_user->fetch_assoc()) {
            $empname   = $row['empname'];
            $user_type = $row['user_type'];
            $payrollNo = $row['payroll_no'];
        } else {
            $empname   = 'GUEST';
            $user_type = '';
            $payrollNo = '';
        }
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
			background-color: #f2dede !important;
		}
		tr.danger td {
			background-color: #f2dede !important;
		}
		/* Center Action column */
		#userTable td:last-child,
		#userTable th:last-child {
			text-align: center;
			vertical-align: middle;
		}
		.btn.view-payroll, .btn.btn-primary, #claimButton, .btn.update-btn, .btn.add-btn{
			border-radius: 5px !important;
			padding: 6px 22px !important;
			background-color: #1d4bb2 !important;
			border-color: #1d4bb6 !important;
			color: #fff !important;
			min-width: 100px;
			letter-spacing: 0.3px;
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
			<li>
				<a href="claimed.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Claimed</b></span></a>
			</li>
			<?php }
			if($user_type=='Team Leader'||$user_type=='Payroll'){ ?>
			<li>
				<a href="payroll_list.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>List of Payroll</b></span></a>
			</li>
			<?php }
			if($user_type=='Team Leader'){ ?>
			<li class="active">
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

	<!-- Assign / Update User Modal -->
	<div class="modal fade" id="assignUserModal" tabindex="-1" role="dialog" aria-labelledby="assignUserModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="assignUserModalLabel">Assign User</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<table class="table table-bordered" style="width:100%">
						<tr>
							<td width="30%"><label style="font-size: 20px; font-family: Arial Narrow;">Employee ID</label></td>
							<td width="70%">
								<input class="form-control" name="empid" id="empid" required/>
							</td>
						</tr>
						<tr>
							<td width="30%"><label style="font-size: 20px; font-family: Arial Narrow;">Employee Name</label></td>
							<td width="70%">
								<input class="form-control" name="empname" id="empname" required/>
							</td>
						</tr>
						<tr>
							<td width="30%"><label style="font-size: 20px; font-family: Arial Narrow;">User Type</label></td>
							<td width="70%">
								<select required class="form-control" name="user_type" id="user_type">
									<option value="" disabled selected>----------</option>
									<option value="Validator">Validator</option>
									<option value="Payroll">Payroll</option>
									<option value="Tagger">Tagger</option>
									<option value="GO">Grievance Officer</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="30%"><label style="font-size: 20px; font-family: Arial Narrow;">Password</label></td>
							<td width="70%">
								<input class="form-control" name="emppass" id="emppass" required value="dswd12345"/>
							</td>
						</tr>
						<tr hidden>
							<td width="30%"><label style="font-size: 20px; font-family: Arial Narrow;">Payroll Date</label></td>
							<td width="70%">
								<input type="text" class="form-control" name="payroll_no" id="payroll_no" value="<?php echo $payrollNo;?>"/>
							</td>
						</tr>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary-close" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary update-status-btn">Assign User</button>
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

		<div class="main-panel" style="width:100%">
			<div class="content">
				<div class="container-fluid">
					<div class="row" style="margin-bottom:10px;">
						<div class="col-md-12">
							<!-- Same as fund_source: btn btn-primary -->
							<button type="button" class="btn add-btn">
								Add New User
							</button>
						</div>
					</div>
					<div class="table-responsive">
					<br>
					<table id="userTable" class="table table-bordered" style="width:100%">
						<thead>
							<tr>
								<th style="width:15%;">Employee ID</th>
								<th style="width:20%;">Employee Name</th>
								<th style="width:15%;">User Type</th>
								<th style="width:15%;">Team Leader</th>
								<th style="width:15%;">Payroll No.</th>
								<th style="width:10%; text-align:center;">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sql = "SELECT * FROM lib_users WHERE team_leader='$emm'";
							$result = mysqli_query($conn, $sql);
							while ($row = mysqli_fetch_assoc($result)) {
								echo "<tr>";
								echo "<td style='vertical-align:middle;'>" . htmlspecialchars($row['empid']) . "</td>";
								echo "<td style='vertical-align:middle;'>" . htmlspecialchars($row['empname']) . "</td>";
								echo "<td style='vertical-align:middle;'>" . htmlspecialchars($row['user_type']) . "</td>";
								echo "<td style='vertical-align:middle;'>" . htmlspecialchars($row['team_leader']) . "</td>";
								echo "<td style='vertical-align:middle;'>" . htmlspecialchars($row['payroll_no']) . "</td>";
								echo "<td style='text-align:center; vertical-align:middle;'>
										<button class='btn update-btn'
											data-empid='" . htmlspecialchars($row['empid'], ENT_QUOTES) . "'
											data-user_type='" . htmlspecialchars($row['user_type'], ENT_QUOTES) . "'
											data-empname='" . htmlspecialchars($row['empname'], ENT_QUOTES) . "'>Update</button>
									  </td>";
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
					</div>
				</div>
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
		$('#userTable').DataTable({
			"columnDefs": [
				{ "width": "15%", "targets": 0 },
				{ "width": "20%", "targets": 1 },
				{ "width": "15%", "targets": 2 },
				{ "width": "15%", "targets": 3 },
				{ "width": "15%", "targets": 4 },
				{ "width": "10%", "targets": 5, "orderable": false }
			],
			"autoWidth": false
		});
	});
</script>

<script>
	$(document).ready(function() {

		// Open Update Modal
		$(document).on('click', '.update-btn', function () {
			var empid    = $(this).data('empid');
			var empname  = $(this).data('empname');
			var userType = $(this).data('user_type');

			$('#empid').val(empid);
			$('#empname').val(empname);
			$('#user_type').val(userType);
			$('#emppass').val('dswd12345');

			$('#assignUserModalLabel').text('Update User');
			$('#assignUserModal').modal('show');
		});

		// Handle Assign / Update button click
		$('.update-status-btn').click(function() {
			var empid        = $('#empid').val().trim();
			var empname      = $('#empname').val().trim();
			var emppass      = $('#emppass').val().trim();
			var selectedType = $('#user_type').val();
			var tl_id        = $('#user_id').val();
			var payroll_no   = $('#payroll_no').val();

			if (!empid || !empname || !emppass || !selectedType) {
				alert('Please fill in all required fields.');
				return;
			}

			$.ajax({
				url: 'save_user.php',
				type: 'POST',
				data: {
					empid:        empid,
					empname:      empname,
					emppass:      emppass,
					selectedType: selectedType,
					payroll_no:   payroll_no,
					tl_id:        tl_id
				},
				success: function(response) {
					alert('User saved successfully!');
					$('#assignUserModal').modal('hide');
					location.reload();
				},
				error: function() {
					alert('Error saving user. Please try again.');
				}
			});
		});

		// Open Add New User Modal
		$('.add-btn').click(function() {
			$('#empid').val('').prop('readonly', false);
			$('#empname').val('');
			$('#user_type').val('');
			$('#emppass').val('dswd12345');
			$('#assignUserModalLabel').text('Add New User');
			$('#assignUserModal').modal('show');
		});

	});
</script>

</body>
</html>
<?php }?>