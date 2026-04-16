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

	
	$sql2 = "SELECT team_leader from lib_users where empid='$emm'"; 
	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();
	$team_leader=$row2['team_leader'];
	
	$sql_bene_cor = "SELECT payroll_no FROM lib_users WHERE empid = '" . $emm . "'";
	$result_bene_cor = $conn->query($sql_bene_cor);
	if ($payrollNoRow = $result_bene_cor->fetch_assoc()) 
		$payrollNo =  $payrollNoRow['payroll_no'];


// SQL query to fetch payroll list from the database
$sql1 = "SELECT *
        FROM tbl_payroll_list as a
		LEFT JOIN lib_sdo as c on sdo=c.sdo_id
		LEFT JOIN lib_fund_source as d on fund_source=d.id
		WHERE  team_leader='$team_leader' ";  // Adjust the table name to your actual payroll table
$result1 = $conn->query($sql1);
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
		#pageNumberSelect {
		  display: inline-block;
		}
		#pageNumberSelect {
		  float: right;
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
				<li class="active">
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
	<div class='modal fade' id='addDetailsModal' tabindex='-1' role='dialog' aria-labelledby='addDetailsModalLabel' aria-hidden='true'>
	  <div class='modal-dialog' role='document'>
		<div class='modal-content'>
		  <div class='modal-header'>
			<h5 class='modal-title' id='addDetailsModalLabel'>Add Details</h5>
			<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
			  <span aria-hidden='true'>&times;</span>
			</button>
		  </div>
		  <div class='modal-body'>
			<table class="table table-bordered" style="width:80%">		
			  <tr>  
				<td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">SDO No.</label></td>  
				<td width="70%" >
				  <input class="form-control selectpicker" name="sdo" id="sdo" hidden></input>
				</td>  
			  </tr>				
			  <tr>  
				<td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Project Name</label></td>  
				<td width="70%" ><textarea  name="project_name"  class="form-control" placeholder="" required> </textarea> </td> 
			  </tr> 	
			  <tr>  
				<td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Partner</label></td>  
				<td width="70%" >
				  <select class="form-control selectpicker" name="fund_source" id="fund_source" hidden>
					<option disabled selected>----------</option>
					<?php
					  $sqlf = "SELECT * FROM lib_fund_source WHERE fs_status = 'Active'";
					  $resultf = mysqli_query($conn, $sqlf);

					  while ($rowf = mysqli_fetch_assoc($resultf)) {
						echo '<option value="' . $rowf['id'] . '">' . $rowf['fs_name'] . '</option>';
					  }
					?>
				  </select>
				</td>  
			  </tr> 
			
			  <tr>  
				<td width="30%">
				  <label  style="font-size: 20px;font-family:Arial Narrow;">Program</label>
				</td>  
				<td width="70%">
				  <select class="form-control selectpicker" name="program_title" id="program_title" hidden>
					<option disabled selected>----------</option>
					<?php
					  $sqlp = "SELECT * FROM lib_program WHERE prog_status = 1";
					  $resultp = mysqli_query($conn, $sqlp);

					  while ($rowp = mysqli_fetch_assoc($resultp)) {
						echo '<option value="' . $rowp['id'] . '">' . $rowp['prog_name'] . '</option>';
					  }
					?>
				  </select>

				</td> 
			  </tr>
			  <tr>  
				<td width="30%"><label  style="font-size:  20px;font-family:Arial Narrow;">Amount</label></td>  
				<td width="70%" ><input type="number"  class="form-control" name="amount" placeholder="Amount" required> </td> 
			  </tr> 
			</table>
		  </div>
		  <div class='modal-footer'>
			<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
			<button type='button' class='btn btn-primary'>Save changes</button>
		  </div>
		</div>
	  </div>
	</div>
		<!-- Modal -->
			<div id="payrollModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="payrollModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-xl" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="payrollModalLabel">Payroll Details</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div id="payroll-details" width="50%"></div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		<!-- Modal prompt for password verification -->
		<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="passwordModalLabel">Verify Password</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="password" id="passwordInput" class="form-control" placeholder="Enter your password">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="button" id="verifyPasswordBtn" class="btn btn-primary">Verify</button>
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

		<div class="content" style="width:100%">
        <div class="table-responsive">  
            <table id="payrollTable" style="width:100%" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Program</th>
                        <th>Province</th>
                        <th>Payroll Date</th>
                        <th>Amount</th>
                        <th>City/Municipality</th>
                        <th>SDO</th>
                        <th>Partner</th>
                        <th>No. of Claimed Benes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
				<?php
				$no = 1;
				while ($row1 = $result1->fetch_assoc()) {
					$sdonum        = strtoupper($row1['sdo']);
					$program       = $row1['program'];
					$province      = strtoupper($row1['province']);
					$date_created  = $row1['date_created'];
					$amount        = $row1['amount'];
					$city_muni     = $row1['city_muni'];
					$fs_name       = strtoupper($row1['fs_name']);
					$payroll_no    = $row1['payroll_no'];
				
					$sql3 = "SELECT count(id) as bene_count from ect_clean_list where payroll_no='$payroll_no' and status='Claimed'";
					$result3 = $conn->query($sql3);
					$row3 = $result3->fetch_assoc();
					$bene_count=$row3['bene_count'];

					$status        = $row1['payroll_status'];
					$row_class     = ($status == 'Active') ? 'success' : (($status == 'Closed') ? 'danger' : '');
				
					echo "<tr class='$row_class'>";
					echo "<td>" . $no++ . "</td>";
					echo "<td>" . $program . "</td>";
					echo "<td>" . $province . "</td>";
					echo "<td>" . $date_created . "</td>";
					echo "<td>" . number_format($amount, 2) . "</td>";
					echo "<td>" . ($city_muni ? $city_muni : 'N/A') . "</td>";
					echo "<td>" . ($sdonum ? $sdonum : 'N/A') . "</td>";
					echo "<td>" . ($fs_name ? $fs_name : 'N/A') . "</td>";
					echo "<td align='center'>" . ($bene_count ?? '0') . "</td>";
					echo "<td align='center'><button class='btn btn-primary view-payroll' data-payroll-no='" . $payroll_no . "' data-backdrop='false'>View</button></td>";
					echo "</tr>";
				
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

    // Event delegation: Attach the event handler to the document, but delegate the event to elements with the 'view-payroll' class
    $(document).on('click', '.view-payroll', function() {
        var payroll_no = $(this).data('payroll-no');
		//alert(payroll_no);
        // Log the payroll number for debugging
        console.log('Payroll No: ' + payroll_no);

        // AJAX request to fetch payroll details
        $.ajax({
            url: 'fetch_payroll_details.php',  // PHP file to fetch details
            type: 'POST',
            data: { payroll_no: payroll_no },
            success: function(response) {
                console.log(response); // Log the response to ensure data is fetched
                $('#payroll-details').html(response);
                $('#payrollModal').modal('show'); // Show the modal
            },
            error: function() {
                alert("Failed to fetch payroll details.");
            }
        });
    });

    // Rebind the click event after the modal is closed (optional if you want to reset modal content)
    $('#payrollModal').on('hidden.bs.modal', function () {
        $('#payroll-details').html(''); // Clear modal content when hidden
    });
});
    // Initialize DataTable
    $('#payrollTable').DataTable({
        "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
        "iDisplayLength": 10
    });
	

</script>
	<?php }?>