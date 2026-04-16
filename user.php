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
	
	$sql_bene_cor = "SELECT payroll_no FROM lib_users WHERE empid = '" . $emm . "'";
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
				<?php }
				if($user_type=='Team Leader'||$user_type=='Tagger'){ ?>
				<li>
					<a href="tagger.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Tagging</span></b> </a>
				</li>
                <li>
					<a href="claimed.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Claimed</span></b> </a>
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
				<!-- <li>
					<a href="https://172.31.176.49/ect/public"><i class="pe-7s-folder"></i><span><b>Liquidation</span></b> </a>
				</li> -->
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
                        <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Employee ID</label></td>  
                        <td width="70%" >
                            <input class="form-control" name="empid" id="empid" required></input>
                        </td>   
                    </tr> 
					<tr>  
                        <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Employee Name</label></td>  
                        <td width="70%" >
                            <input class="form-control" name="empname" id="empname" required></input>
                        </td>   
                    </tr> 
                    <tr>  
                        <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">User Type</label></td>  
                        <td width="70%" >
                            <select required class="form-control selectpicker" name="user_type" id="user_type">
                                <option value="" disabled selected>----------</option>
                                <option value="Validator" >Validator</option>
                                <option value="Payroll" >Payroll</option>
                                <option value="Tagger" >Tagger</option>
                                <option value="GO" >Grievance Officer</option>
                            </select>
                        </td>  
                    </tr> 	
					<tr>  
                        <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Password</label></td>  
                        <td width="70%" >
                            <input class="form-control" name="emppass" id="emppass" required value='dswd12345'></input>
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
                    <a class="navbar-brand" href="#"><?php echo $emm; ?> </a>
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
					<table id="userTable" class="table table-bordered " style="width:100%">		   
						<thead>
							<tr>
								<th style="width: 15%;">Employee ID</th>
								<th style="width: 20%;">Employee Name</th>
								<th style="width: 15%;">User Type</th>
								<th style="width: 15%;">Team Leader</th>
								<th style="width: 15%;">Payroll No.</th>
								<th style="width: 10%;">Update</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sql = "SELECT * FROM lib_users where team_leader='$emm'";
							$result = mysqli_query($conn, $sql);
							while ($row = mysqli_fetch_assoc($result)) {
								echo "<tr>";
								echo "<td>" . $row['empid'] . "</td>";
								echo "<td>" . $row['empname'] . "</td>";
								echo "<td>" . $row['user_type'] . "</td>";
								echo "<td>" . $row['team_leader'] . "</td>";
								echo "<td>" . $row['payroll_no'] . "</td>";
								echo "<td><button class='btn btn-info update-btn' 
									data-empid='" . $row['empid'] . "' 
									data-user_type='" . $row['user_type'] . "' 
									data-empname='" . $row['empname'] . "'>Update</button></td>";
								echo "</tr>";
								
							}
							?>
						</tbody>
					</table>
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
<script>
    $(document).ready(function() {
    $('#userTable').DataTable({
        "columnDefs": [
            { "width": "15%", "targets": 0 },
            { "width": "20%", "targets": 1 },
            { "width": "15%", "targets": 2 },
            { "width": "15%", "targets": 3 },
            { "width": "15%", "targets": 4 },
            { "width": "10%", "targets": 5 }
        ],
        "autoWidth": false  // ← add this so DataTable respects your widths
    });
});
</script>
</html>
<script>
$(document).ready(function() {

    $(document).on('click', '.update-btn', function () {
        var empid = $(this).data('empid');
        var empname = $(this).data('empname');  
        var userType = $(this).data('user_type');

        $('#empid').val(empid);
        $('#empname').val(empname);
        $('#user_type').val(userType);

        $('#assignUserModal').modal('show');
    });

    // Handle update status button click
    $('.update-status-btn').click(function() {
        var empid = $('#empid').val();
        var empname = $('#empname').val();
        var emppass = $('#emppass').val();
        var selectedType = $('#user_type').val();
        var tl_id = $('#user_id').val();
        var payroll_no = $('#payroll_no').val();

        if (!selectedType || !tl_id || !emppass || !empid || !empname) {
            alert("User type, Password or Team Leader ID cannot be empty.");
            return;
        } else {
            $.ajax({
                url: 'save_user.php',
                type: 'POST',
                data: {
                    empid: empid,
                    empname: empname,
                    emppass: emppass,
                    selectedType: selectedType,
                    payroll_no: payroll_no,
                    tl_id: tl_id
                },
                success: function(response) {
                    alert('Status and details updated successfully!');
                    location.reload();
                },
                error: function() {
                    alert('Error updating status and details. Please try again.');
                }
            });
        }
    });

    // Add New User button
    $('.add-btn').click(function() {
        // Clear fields before opening for new entry
        $('#empid').val('');
        $('#empname').val('');
        $('#user_type').val('');
        $('#emppass').val('dswd12345');
        $('#assignUserModal').modal('show');
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