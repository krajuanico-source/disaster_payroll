<!doctype html>
<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
error_reporting(0);
include "dbconnect.php";
	$date_val = date("Y-m-d");	
	$date_yesterday = date("Y-m-d",strtotime("yesterday"));
	$emm=$_SESSION['userid'];
	$user_type = $_SESSION['user_type']; // get the user type from the database or session
	if($emm==''||$emm==NULL){
		echo "<script>window.open('index.php','_self')</script>";
	}else{
		
	$sql1= "select user_type from lib_users where empid='$emm'";
	$result1 = $conn->query($sql1);
	$row1 = $result1->fetch_assoc();
	$user_type=$row1['user_type'];
	
	$sql2 = "SELECT team_leader,payroll_no from lib_users where empid='$emm'";
	$result2 = $conn->query($sql2);
	$row2 = $result2->fetch_assoc();
	$team_leader=$row2['team_leader'];
	$payroll_no=$row2['payroll_no'];
	

	
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
	<script src="js/jquery-3.6.0.min.js"></script>

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
				<?php if($user_type=='Team Leader'||$user_type=='Validator'||$user_type=='GO'){ ?>
				<li class="active">
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
			<div id="payrollModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="payrollModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="payrollModalLabel">Beneficiary's Information</h4>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div id="payroll-details"></div>
						</div>
					</div>
				</div>
			</div>
		     <!-- TL Password Modal -->
             <div class="modal fade" id="tlPasswordModal" tabindex="-1" aria-labelledby="tlPasswordModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<form id="tlPasswordForm" method="POST" action="revert_action.php?user_id=<?php echo $emm;?>">
					<div class="modal-content">
						<div class="modal-header">
						<h5 class="modal-title" id="tlPasswordModalLabel">Enter TL Password</h5>
						<!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
						</div>
						<div class="modal-body">
						<input type="hidden" name="bene_id" id="modalBeneId">
						<div class="mb-3">
							<label for="tlPassword" class="form-label">Password</label>
							<input type="password" class="form-control" id="tlPassword" name="tl_password" required>
						</div>
						</div>
						<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Submit</button>
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						</div>
					</div>
					</form>
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
                    <a class="navbar-brand" href="#"><?php echo strtoupper($user_type); ?> </a>
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
		<div class="mt-3"><br>
							<div class="progress" style="height: 20px; display: none;" id="progressContainer">
								<div 
									class="progress-bar progress-bar-striped progress-bar-animated" 
									role="progressbar" 
									style="width: 0%;" 
									id="progressBar">
								</div>
							</div>
						</div>
          <div class="content" style="width:100%">
            <div class="table-responsive">
                <table id="payrollTable" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Beneficiary Name</th>
                            <th>Date of Birth</th>
                            <th>Barangay</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Updated by</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
    var table = $('#payrollTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: 'fetch_bene_payroll.php',
        type: 'POST',
        data: function(d) {
            d.payroll_no = <?php echo json_encode($payroll_no); ?>; // Safe encoding
        }
    },
    pageLength: 10,
columns: [
    { title: "No." },
    { 
        title: "Beneficiary Name",
        render: function(data, type, row) {
            if (type === 'display') {
                return data.replace(/\b(JR|SR|I|II|III|IV|V)\s*$/i, function(match) {
                    return match.trim().toUpperCase() + '.';
                });
            }
            return data; // raw data unchanged for sort/filter/save
        }
    },
    { title: "DOB" },
    { title: "Barangay" },
    { title: "Amount" },
    { title: "Status" },
    { title: "Updated By" },
    { title: "Actions" }
]
});


    $(document).on('click', '.view-payroll', function() {
        var bene_id = $(this).data('payroll-no');
        $.ajax({
            url: 'fetch_bene_details.php',
            type: 'POST',
            data: { 
                bene_id: bene_id, 
                user_type: "<?php echo strtoupper($user_type); ?>", 
                user_id: "<?php echo strtoupper($emm); ?>" 
            },
            success: function(response) {
                $('#payroll-details').html(response);
				$('#payrollModal').modal('show');
            },
            error: function() {
                alert("Failed to fetch beneficiary details.");
            }
        });
    });
});
</script>
	<?php }?>