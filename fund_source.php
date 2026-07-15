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
		
		.btn.update-status-btn{
			border-radius: 5px !important;
			padding: 6px 22px !important;
			background-color: #1d4bb2 !important;
			border-color: #1d4bb6 !important;
			color: #fff !important;
			min-width: 100px;
			letter-spacing: 0.3px;
		}
		.btn.btn-secondary{
			border-radius: 5px !important;
			padding: 6px 22px !important;
			background-color: #6B7280!important;
			border-color: #6B7280 !important;
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

	<!-- Update Partner Modal -->
	<div class="modal fade" id="assignUserModal" tabindex="-1" role="dialog" aria-labelledby="assignUserModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="assignUserModalLabel">Update Partner</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<table class="table table-bordered" style="width:100%">
						<tr hidden>
							<td width="30%"><label style="font-size: 20px; font-family: Arial Narrow;">ID</label></td>
							<td width="70%">
								<input class="form-control" name="fs_id" id="fs_id"/>
							</td>
						</tr>
						<tr>
							<td width="30%"><label style="font-size: 20px; font-family: Arial Narrow;">Partner Name</label></td>
							<td width="70%">
								<input class="form-control" name="fs_name" id="fs_name"/>
							</td>
						</tr>
						<tr>
							<td width="30%"><label style="font-size: 20px; font-family: Arial Narrow;">Status</label></td>
							<td width="70%">
								<select class="form-control" name="fs_stat" id="fs_stat">
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn update-status-btn">Update Partner</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Add New Partner Modal -->
	<div class="modal fade" id="addSdoModal" tabindex="-1" role="dialog" aria-labelledby="addSdoModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addSdoModalLabel">Add New Partner</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="newfsname" style="font-size: 20px; font-family: Arial Narrow;">Partner Name</label>
						<input type="text" class="form-control" id="newfsname" name="newfsname">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="saveSdoBtn">Save Partner</button>
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
					<a class="navbar-brand" href="#" style="font-size: 20px; font-family: Arial;">
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
							<button type="button" class="btn add-btn">
								Add New Partner
							</button>
						</div>
					</div>
					<div class="table-responsive">
					<br>
					<table id="userTable" class="table table-bordered" style="width:100%">
						<thead>
							<tr>
								<th>Partner Name</th>
								<th>Status</th>
								<th style="text-align:center;">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sql = "SELECT * FROM lib_fund_source ORDER BY fs_status ASC, fs_name ASC";
							$result = mysqli_query($conn, $sql);
							while ($row = mysqli_fetch_assoc($result)) {
								$rowClass = ($row['fs_status'] == 'Inactive') ? 'danger' : '';
								echo "<tr class='" . $rowClass . "'>";
								echo "<td style='vertical-align:middle;'>" . htmlspecialchars($row['fs_name']) . "</td>";
								echo "<td style='vertical-align:middle;'>" . htmlspecialchars($row['fs_status']) . "</td>";
								echo "<td style='text-align:center; vertical-align:middle;'>
										<button class='btn update-btn'
											data-fs-stat='" . $row['fs_status'] . "'
											data-fs-id='" . $row['id'] . "'
											data-fs-name='" . htmlspecialchars($row['fs_name'], ENT_QUOTES) . "'>Update</button>
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
				{ "width": "55%", "targets": 0 },
				{ "width": "20%", "targets": 1 },
				{ "width": "25%", "targets": 2, "orderable": false }
			],
			"order": [[1, "asc"]]
		});
	});
</script>

<script>
	$(document).ready(function() {

		// Open Update Modal
		$(document).on('click', '.update-btn', function () {
			var fs_id   = $(this).data('fs-id');
			var fs_name = $(this).data('fs-name');
			var fs_stat = $(this).data('fs-stat');

			$('#fs_id').val(fs_id);
			$('#fs_name').val(fs_name);

			var selectElement = document.getElementById("fs_stat");
			selectElement.innerHTML = "";
			selectElement.add(new Option("Active", "Active"), 0);
			selectElement.add(new Option("Inactive", "Inactive"), 1);
			selectElement.value = fs_stat;

			$('#assignUserModal').modal('show');
		});

		// Handle Update Source button click
		$('.update-status-btn').click(function() {
			var fs_id_2   = $('#fs_id').val();
			var fs_name_2 = $('#fs_name').val().trim();
			var fs_stat_2 = $('#fs_stat').val();
			var tl_id     = $('#user_id').val();

			if (!fs_name_2) {
				alert('Partner Name is required.');
				$('#fs_name').focus();
				return false;
			}

			$.ajax({
				url: 'save_fund_source.php',
				type: 'POST',
				data: {
					fs_id_2:   fs_id_2,
					fs_name_2: fs_name_2,
					fs_stat_2: fs_stat_2,
					tl_id:     tl_id
				},
				success: function(response) {
					alert('Status and details updated successfully!');
					$('#assignUserModal').modal('hide');
					location.reload();
				},
				error: function() {
					alert('Error updating status and details. Please try again.');
				}
			});
		});

		// Open Add Partner Modal
		$('.add-btn').click(function() {
			$('#newfsname').val('');
			$('#addSdoModal').modal('show');
		});

		// Handle Save Partner button click
		$('#saveSdoBtn').click(function() {
			var newfsname = $('#newfsname').val().trim();

			if (newfsname === '') {
				alert('Please enter a Partner Name.');
				$('#newfsname').focus();
				return;
			}

			$.ajax({
				url: 'save_new_fs.php',
				type: 'POST',
				data: {
					newfsname: newfsname
				},
				success: function(response) {
					alert('New Partner added successfully!');
					$('#addSdoModal').modal('hide');
					location.reload();
				},
				error: function() {
					alert('Error saving new partner. Please try again.');
				}
			});
		});

	});
</script>

</body>
</html>
<?php }?>