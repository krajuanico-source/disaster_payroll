<!doctype html>
<?php
session_start();
error_reporting(0);
    include "php/db.php";
    include "dbconnect.php";
	
	$emm=$_SESSION['userid'];	
	$user_type = $_SESSION['user_type']; // get the user type from the database or session
	if($emm==''||$emm==NULL){
		echo "<script>window.open('index.php','_self')</script>";
	}else{
		
	$sql1= "select empnum from tbl_employment where empid='$emm'";
	$result1 = $con->query($sql1);
	$row1 = $result1->fetch_assoc();
	$id1=$row1['empnum'];
	$sql = "SELECT  distinct e.empfname,e.empmname,e.emplname,e.empext,a.empid,e.emppass,e.empuser
			FROM tbl_employment a 
			inner join employee_info e using (empnum)
			where a.empnum= '$id1'";
	$result = $con->query($sql);
	$row = $result->fetch_assoc();
	
	$sql_bene_cor = "SELECT payroll_no FROM lib_users";
	$result_bene_cor = $conn->query($sql_bene_cor);
	if ($payrollNoRow = $result_bene_cor->fetch_assoc()) 
		$payrollNo =  $payrollNoRow['payroll_no'];
	
	
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
	
	  <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

	<!-- Include jQuery first -->
	<script src="jquery-3.6.0.min.js"></script>

	<!-- Include Bootstrap JS only once -->
	<script src="js/bootstrap.bundle.min.js"></script>

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

<body>
<div class="wrapper">
    <div class="sidebar" data-color="blue" data-image="assets/img/sidebar-6.jpg">

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
				<li class="">
					<a href="served_list.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Served List</span></b> </a>
				</li>	
				<?php }
				if($user_type=='Team Leader'||$user_type=='Tagger'){ ?>
				<li>
					<a href="tagger.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Tagging</span></b> </a>
				</li>
				<?php }
				 if($user_type=='Team Leader'||$user_type=='Payroll'){ ?>
				<li>
					<a href="payroll_list.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>List of Payroll</span></b> </a>
				</li>
				<?php }
				 if($user_type=='Team Leader'){ ?>
				 <li class="active">
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
				<?php } ?>
			</ul>
    </div>
</div>
<!-- Modal -->
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
                        <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Employee Name</label></td>  
                        <td width="70%" >
                            <select class="form-control selectpicker" name="empid" id="empid">
                                <option value="" disabled selected>----------</option>
							<?php
							$sql3 = "SELECT * FROM tbl_employment as a 
									INNER JOIN employee_info as b on a.empnum = b.empnum
									where empid<>'' and empid<>'xxx' order by empid asc";
							$result3 = mysqli_query($con, $sql3);
							while ($row3 = mysqli_fetch_assoc($result3)) { ?>
								<option value="<?php echo $row3['empid']; ?>" ><?php echo $row3['emplname'].', '.$row3['empfname'].' '.$row3['empmname']; ?></option>
							<?php }?>
                            </select>
                        </td>   
                    </tr> 
                    <tr>  
                        <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">User Type</label></td>  
                        <td width="70%" >
                            <select class="form-control selectpicker" name="user_type" id="user_type">
                                <option value="" disabled selected>----------</option>
                                <option value="Validator" >Validator</option>
                                <option value="Payroll" >Payroll</option>
                                <option value="Tagger" >Tagger</option>
                            </select>
                        </td>  
                    </tr> 	
                    <tr hidden>  
                        <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Payroll Date</label></td>  
                        <td width="70%" >
                            <input type="text" class="form-control selectpicker" name="payroll_no" id="payroll_no" value='<?php echo $payrollNo;?>' />
                        </td>  
                    </tr> 	
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                    <a class="navbar-brand" href="#"><?php echo strtoupper($row["emplname"]).", ".strtoupper($row["empfname"])." ".strtoupper($row["empmname"]); ?> </a>
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

         <div class="main-panel" style="width:100%">
			<div class="content">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<button type="button" class="btn btn-primary add-btn">
								Add New User
							</button>
						</div>
					</div>
					<div class="table-responsive">  
					<br>
					<br>
					<table id="userTable" class="table table-bordered" style="width:100%">		   
						<thead>
							<tr>
								<th>Employee ID</th>
								<th>User Type</th>
								<th>Team Leader</th>
								<th>Employee Name</th>
								<th>Password</th>
								<th>Payroll No.</th>
								<th>Update</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sql = "SELECT * FROM lib_users";
							$result = mysqli_query($conn, $sql);
							while ($row = mysqli_fetch_assoc($result)) {
								$user_id=$row['empid'];
								$sqlu = "SELECT  distinct e.empfname,e.empmname,e.emplname,e.empext,a.empid,e.emppass,e.empuser
										FROM tbl_employment a 
										inner join employee_info e using (empnum)
										where a.empid= '$user_id'";
								$resultu = $con->query($sqlu);
								$rowu = $resultu->fetch_assoc();
	
								echo "<tr>";
								echo "<td>" . $row['empid'] . "</td>";
								echo "<td>" . $row['user_type'] . "</td>";
								echo "<td>" . $row['team_leader'] . "</td>";
								echo "<td>" . $rowu['empuser'] . "</td>";
								echo "<td>" . $rowu['emppass'] . "</td>";
								echo "<td>" . $row['payroll_no'] . "</td>";
								echo "<td><button class='btn btn-info update-btn' data-empid='" . $row['empid'] . "' data-user_type='" . $row['user_type'] . "'>Update</button></td>";
								echo "</tr>";
								
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div> 

</body>

    <!--   Core JS Files   -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
	
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

	<!--  Charts Plugin -->
	<script src="assets/js/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="assets/js/bootstrap-notify.js"></script>

    <!--  Google Maps Plugin    -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>

    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

	<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
	<script src="assets/js/demo.js"></script>
<script>
    $(document).ready(function() {
        $('#userTable').DataTable({
            "columnDefs": [
                { "width": "30%", "targets": 0 },
                { "width": "40%", "targets": 1 },
                { "width": "30%", "targets": 2 }
            ]
        });
    });
</script>
</html>
<script>
$(document).ready(function() {
	$('.update-btn').click(function() {
		var empid = $(this).data('empid');
		var userType = $(this).data('user_type');
		var tl_id = $('#user_id').val();
		var payroll_no = $('#payroll_no').val();

		// Set the values of the select elements in the modal
		$('#empid').val(empid);
		$('#user_type').val(userType);
		// Open the modal
		
		$('#assignUserModal').modal('show');
	});

    // Handle update status button click
	$('.update-status-btn').click(function() {
		var empid = $('#empid').val();
		var selectedType = $('#user_type').val();
		var tl_id = $('#user_id').val();
		var payroll_no = $('#payroll_no').val();
		//alert(selectedType);
		// AJAX request to update the details in the database
		 if (!selectedType || !tl_id) {
            alert("User type or Team Leader ID cannot be empty.");
            return; // Exit if any of the required fields are missing
        }else{
			// Open the modal
			$.ajax({
			url: 'save_user.php',  // The PHP file that handles the update
			type: 'POST',
			data: {
				empid: empid,
				selectedType: selectedType,
				payroll_no: payroll_no,
				tl_id: tl_id
			},
			success: function(response) {
					alert('Status and details updated successfully!');
					location.reload();
					// Reload the payrollTable without reloading the whole page
					$('#payrollTable').DataTable().ajax.reload(null, false);  // Reload DataTable, keeping the page position
			},
			error: function() {
				alert('Error updating status and details. Please try again.');
			}
		});
		}
	});
});
$(document).ready(function() {
	$('.add-btn').click(function() {
		$('#assignUserModal').modal('show');
	});
    // Handle Add New SDO button click
    $('#saveSdoBtn').click(function() {
        var newSdoNo = $('#newSdoNo').val();
        var newSdoName = $('#newSdoName').val();
        var newSdoStatus = $('#newSdoStatus').val();
        var tl_id = $('#user_id').val();

        // Validate the form
        if (newSdoNo === '' || newSdoName === '' || newSdoStatus === '') {
            alert('Please fill all the fields.');
            return;
        }
	
        // AJAX request to save the new SDO in the database
        $.ajax({
            url: 'save_new_user.php',  // PHP file that handles saving the new SDO
            type: 'POST',
            data: {
                sdo_no: newSdoNo,
                sdo_name: newSdoName,
                sdo_status: newSdoStatus,
                tl_id: tl_id
            },
            success: function(response) {
				
                alert('New SDO added successfully!');
				location.reload();
                // Close the modal
                $('#assignUserModal').modal('hide');
                // Reload the table to show the new SDO
                $('#userTable').DataTable().ajax.reload(null, false);
				location.reload();
            },
            error: function() {
                alert('Error saving new SDO. Please try again.');
            }
        });
    });
});
</script>
	<?php }?>