<!doctype html>
<?php
session_start();
    include "dbconnect.php";
	
	$emm=$_GET['userid'];
	$user_type = $_SESSION['user_type']; // get the user type from the database or session
	if($emm==''||$emm==NULL){
		echo "<script>window.open('index.php','_self')</script>";
	}else{
		$sql2 = "SELECT payroll_no,user_type from lib_users where empid='$emm'";
		$result2 = $conn->query($sql2);
		$row2 = $result2->fetch_assoc();
		$payroll_no=$row2['payroll_no'];

		// db connection
		$conn = new mysqli("localhost", "root", "", "disaster_db");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		function getCount($conn, $payroll_no, $status = null) {
			if ($status === null) {
				// Count all beneficiaries for a specific payroll_no
				$stmt = $conn->prepare("SELECT COUNT(*) as total FROM ect_clean_list WHERE payroll_no = ?");
				$stmt->bind_param("s", $payroll_no);
			} else {
				// Count beneficiaries with specific status and payroll_no
				$stmt = $conn->prepare("SELECT COUNT(*) as total FROM ect_clean_list WHERE status = ? AND payroll_no = ?");
				$stmt->bind_param("ss", $status, $payroll_no);
			}
		
			$stmt->execute();
			$result = $stmt->get_result()->fetch_assoc();
			return $result['total'];
		}
		
// Total beneficiaries regardless of status
$toBeServed  = getCount($conn, $payroll_no); // all for this payroll

// Others by specific status
$validated   = getCount($conn, $payroll_no, 'validated');
$claimed     = getCount($conn, $payroll_no, 'claimed');
$replacement = getCount($conn, $payroll_no, 'replacement');
$correction  = getCount($conn, $payroll_no, 'correction');
$disqualfied  = getCount($conn, $payroll_no, 'disqualfied');

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
    <!--     Fonts and icons     -->
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
	<!-- Chart.js Library -->
    <script src="js/chart.js"></script>	
	<style>
		body {
		  font-size: 16px;
		}
		.modal {
		  z-index: 1060; /* increase the z-index to be higher than the page's content */
		}
		/* Custom CSS to force modal to be extra large */
		.modal-xl {
			max-width: 90%;  /* Adjust the width as per your requirement */
			width: 90%;      /* Set width to 90% of the screen */
		}
		.notification {
			display: flex;
			flex-direction: column;
			align-items: center;
		}

		.notification .badge {
			background-color: salmon;
			color: white;
			padding: 5px 10px;
			border-radius: 50%;
			margin-bottom: 5px; /* Space between badge and name */
			font-size: 12px;
		}
        .card {
            padding: 20px;
            margin-bottom: 20px;
        }
		#validatorBarChart {
			width: 100%;   /* Set the width to 100% of the parent */
			height: 10%;   /* Set the height to 50% of the parent */
		}
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f8;
      margin: 0;
      padding: 20px;
    }
    h1 {
      text-align: center;
    }
    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }
    .card {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
    }
    .card h2 {
      font-size: 2em;
      color: #007bff;
    }
    .card p {
      font-size: 1.1em;
      margin: 10px 0 0;
      color: #555;
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

    <!-- Modal for file import -->
    <div class="modal" id="modal" tabindex="-1" aria-labelledby="confirmSyncModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmSyncModalLabel" style="color:red">Reminders: Import Served File first!</h5>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <input type="file" class="form-control" id="ImportfileInput" name="csvFile" accept=".csv, .xlsx, .xls"/>
                        <div id="fileError" style="color: red; display: none;">Please upload a valid CSV file.</div><br>
                        <button type="button" class="btn btn-primary" id="runImportbtn" onclick="runImport()">Update Served List</button>
                    </form>
                    <div id="progressContainer1" style="display: none; margin-top: 20px;">
                        <div class="progress1">
                            <div id="progressBar1" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <span id="progressPercentage1" style="font-weight: bold;">0%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="closeModal2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
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
                <li class="active">
					<a href="dashboard.php?userid=<?php echo $emm;?>"><i class="pe-7s-file"></i><span><b>Dashboard</span></b> </a>
				</li>
				<?php }?>
				<?php if($user_type=='Team Leader'){ ?>
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
				<!-- <li hidden >
					<a href="https://172.31.176.49/ect/public"><i class="pe-7s-folder"></i><span><b>Liquidation</span></b> </a>
				</li> -->
				<?php } ?>
			</ul>
    </div>
</div>

<div class="modal fade" id="confirmSyncModal" tabindex="-1" aria-labelledby="confirmSyncModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmSyncModalLabel" style="color:red">Reminders before Export</h4>
            </div>
            <div class="modal-body">
						<div class="mt-3">
						  <ol>
							<li>Upon successful export, an Excel file will be generated.</li>
							<li>An XLSX file will be created and generated.</li>
						  </ol>
						</div>
            </div>
			
            <div class="modal-footer">
				<button type="button" class="btn btn-info" id="exportClaimedBenes">Proceed Exporting</button>
			</div>

			<script>
			document.getElementById("exportClaimedBenes").addEventListener("click", function() {
				// Get venue input value
				let teamLeader ="<?php echo $emm;?>";
				
				var payrollNo = $('#payroll_no_tl').val();
                var payroll_date = $('#payroll_date').val();
				$('#confirmSyncModal').modal('hide'); // Hide the modal
				// Redirect to export script with venue as a query parameter
				window.open("exportClaimedBenes.php?teamLeader=" + teamLeader+"&payrollNo=" + payrollNo+"&payroll_date=" + payroll_date, "_blank");
			});
			</script>

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
							<div id="payroll-details"></div>
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
                    <a class="navbar-brand" href="#"><?php echo  $user_type; ?> </a>
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
			<div class="content" style="width:99%">
			<input hidden id="payrollNo" value="<?php echo $payroll_no;?>"></input>
			<div class="content" style="width:99%">
				<div class="row" > <div class="form-inline">
					<div class="col-md-19" style="float: left;">
						<button type="button" class="btn btn-primary" id="syncDataButton1">
							<i class="fas fa-file-export"></i> Export All Data
						</button>
			</div>
			<div class="row">
                <!-- Payroll No. Dropdown -->
                <div class="col-md-3" style="float: left">
                    <select class="form-control" id="payroll_no_tl" name="payroll_no_tl">
                        <option disabled selected>Select Payroll No.</option>
                        <?php
                            $sql3 = "SELECT payroll_no FROM tbl_payroll_list WHERE team_leader='$emm'";
                            $result3 = mysqli_query($conn, $sql3);
                            while ($row3 = mysqli_fetch_assoc($result3)) { ?>
                                <option value="<?php echo $row3['payroll_no']; ?>">
                                    <?php echo $row3['payroll_no']; ?>
                                </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Date Input Field -->
                <div class="col-md-3" style="float: left; margin-left: -210px;">
                    <input type="date" class="form-control" id="payroll_date" name="payroll_date" placeholder="Select Date">
                </div>
            </div>

			<div class="container-fluid">
							
				<h1>Beneficiary Dashboard</h1>
				<div class="dashboard">
                    <div class="card" style="background-color: #007bff; color: white;">
                        <h2 style="color: white;" ><?= $toBeServed ?></h2>
                        <p style="color: white;" >To Be Served</p>
                    </div>
                    <div class="card" style="background-color: #28a745; color: white;">
                        <h2 style="color: white;" ><?= $validated ?></h2>
                        <p style="color: white;" >Validated</p>
                    </div>
                    <div class="card" style="background-color: #17a2b8; color: white;">
                        <h2 style="color: white;" ><?= $claimed ?></h2>
                        <p style="color: white;" >Claimed</p>
                    </div>
                    <div class="card" style="background-color: #eec703; color: white;">
                        <h2 style="color: white;" ><?= $replacement ?></h2>
                        <p style="color: white;" >For Replacement</p>
                    </div>
                    <div class="card" style="background-color: #dc3545; color: white;">
                        <h2 style="color: white;" ><?= $correction ?></h2>
                        <p style="color: white;" >For Correction</p>
                    </div>
                    <div class="card" style="background-color: salmon; color: white;">
                        <h2 style="color: white;" ><?= $correction ?></h2>
                        <p style="color: white;" >Disqualified</p>
                    </div>
                </div>



            </div>
		</div>
	</div>
</div>
</body>


	
    <!--   Core JS Files   -->
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

	<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
	<script src="assets/js/demo.js"></script>
	<script>
 document.getElementById("syncDataButton1").onclick = function () {
 $('#confirmSyncModal').modal('show'); // Show the modal
 }
 </script>
	<script>
$('#exportfile').click(function() {
    var reportType = $('#export_rep').val();
    var payrollNo = $('#payroll_no_tl').val();
    var payroll_date = $('#payroll_date').val();
    $.ajax({
        type: 'POST',
        url: 'generate_report.php',
        data: { report_type: reportType},
        success: function(response) {
            var blob = new Blob([response], { type: 'text/csv' });
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'report_' + new Date().getTime() + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        },
        error: function() {
            alert('Error generating report. Please try again.');
        }
    });
});
</script>
<script>
        // Show modal on page load
            $(document).ready(function() {
            $('#modal').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });

            $('#closeModal2').click(function() {
                $('#modal').modal('hide');
            });
        });

        // Close modal on click
        document.getElementById('closeModal2').addEventListener('click', () => {
            document.getElementById('modal').style.display = 'none';
        });

        // Function to handle file import
        function runImport() {
            let fileInput = document.getElementById("ImportfileInput");
            let file = fileInput.files[0];

            if (!file || !/\.(csv|xlsx?|xls)$/i.test(file.name)) {
                document.getElementById("fileError").style.display = "block";
                return;
            }

            let closeModalBtn = document.getElementById("closeModal2");
            closeModalBtn.style.display = "inline-block";
            closeModalBtn.disabled = false;
            closeModalBtn.classList.remove("btn-primary");
            closeModalBtn.classList.add("btn-info");
            closeModalBtn.innerText = "PAGHULAT!";

            document.getElementById('runImportbtn').disabled = true;
            document.getElementById("fileError").style.display = "none";

            let formData = new FormData();
            formData.append("csvFile", file);

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "upload.php", true);

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    let percentComplete = (e.loaded / e.total) * 100;
                    document.getElementById("progressBar1").style.width = percentComplete + "%";
                    document.getElementById("progressPercentage1").innerText = Math.round(percentComplete) + "%";
                }
            };

            xhr.onload = function () {
                if (xhr.status == 200) {
                    let response = xhr.responseText.trim();
                   alert(response);

                    if (response === "file_exists") {
                        alert("Error: This file has already been uploaded!");
                    } else if (response === "success") {
                        alert("Import Done");
                    } else {
                        document.getElementById("progressBar1").style.width = "100%";
                        document.getElementById("progressPercentage1").innerText = "100%";
                        alert("Import Done");
                    }

                    closeModalBtn.classList.remove("btn-danger");
                    closeModalBtn.classList.add("btn-primary");
                    closeModalBtn.innerText = "Done";
                    closeModalBtn.disabled = false;
                } else {
                    alert("Upload Failed! Server error...");
                }
            };
            xhr.send(formData);
        }
    </script>
	<script>
$(document).ready(function() {
    $('#payroll_no_tl').change(function() {
        var payroll_no = $(this).val();

        $.ajax({
            type: 'POST',
            url: 'fetch_dashboard_data.php', // create this file
            data: { payroll_no: payroll_no },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Update dashboard cards
                    $('.dashboard').html(`
                        <div class="card" style="background-color: #007bff; color: white;">
                            <h2  style="color: white;" >${data.toBeServed}</h2>
                            <p  style="color: white;" >To Be Served</p>
                        </div>
                        <div class="card" style="background-color: #28a745; color: white;">
                            <h2  style="color: white;" >${data.validated}</h2>
                            <p  style="color: white;" >Validated</p>
                        </div>
                        <div class="card" style="background-color: #17a2b8; color: white;">
                            <h2  style="color: white;" >${data.claimed}</h2>
                            <p  style="color: white;" >Claimed</p>
                        </div>
                        <div class="card" style="background-color: #eec703; color: #212529;">
                            <h2  style="color: white;" >${data.replacement}</h2>
                            <p  style="color: white;" >For Replacement</p>
                        </div>
                        <div class="card" style="background-color: #dc3545; color: white;">
                            <h2  style="color: white;" >${data.correction}</h2>
                            <p  style="color: white;"  >For Correction</p>
                        </div>
                        <div class="card" style="background-color: salmon; color: white;">
                            <h2  style="color: white;" >${data.disqualified}</h2>
                            <p  style="color: white;"  >Disqualified</p>
                        </div>
                    `);
                } else {
                    alert("No data returned.");
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert("Error fetching dashboard data.");
            }
        });
    });
});
</script>
<script>
$('#generate_rep').click(function() {
    var reportType = $('#export_rep').val();
    var payrollNo = $('#payroll_no_tl').val();

    // Validate if payroll number is selected
    if (!payrollNo) {
        alert('Please select a payroll number.');
        return; // Stop further execution
    }

    $.ajax({
        type: 'POST',
        url: 'generate_report.php',
        data: { report_type: reportType, payrollNo: payrollNo },
        success: function(response) {
            var blob = new Blob([response], { type: 'text/csv' });
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'report_' + new Date().getTime() + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        },
        error: function() {
            alert('Error generating report. Please try again.');
        }
    });
});

</script>
</html>
<?php }?>