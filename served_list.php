<!doctype html>
<?php
session_start();
error_reporting(0);
include "dbconnect.php";

$emm = $_SESSION['userid'] ?? '';
$user_type = $_SESSION['user_type'] ?? '';

if ($emm == '' || $emm == NULL) {
    echo "<script>window.open('index.php','_self')</script>";
    exit;
}

// Get user info
$stmt1 = $conn->prepare("SELECT user_type, empname FROM lib_users WHERE empid = ?");
$stmt1->bind_param("s", $emm);
$stmt1->execute();
$row1 = $stmt1->get_result()->fetch_assoc();
$user_type = $row1['user_type'];
$empname   = $row1['empname'];
$payroll_no = $user['payroll_no'];

if(!in_array($user_type, ['Team Leader', 'Validator', 'GO'])){
    echo "<script>window.open('index.php','_self')</script>";
    exit;
}

?>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>DSWD FO XI - ECT Payroll System</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="css/styless.css" rel="stylesheet" />
<link rel="icon" type="image/png" href="images/icons/dswd_icon.jpg">
<link href="assets/css/bootstrap.min.css" rel="stylesheet" />
<link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>
<link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
<link href="css/dataTables.min.css" rel="stylesheet" />


<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="assets/js/light-bootstrap-dashboard.js"></script>

<style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
        .table{
            background-color: #fff;
        }
</style>
</head>
<body>

<div class="wrapper">
     <div class="sidebar" data-color="blue" data-image="assets/img/sidebar-6.jpg">
        <div class="sidebar-wrapper">
            <div class="logo">
                <a href="#" class="simple-text">
                    <img src="images/icons/dswd_logo_white_2.png" style="width:82%; height:82%; margin-left:-3%" alt=""/>
                </a>
            </div>
            <ul class="nav">
                <?php if($user_type=='Team Leader'){ ?>
                <li><a href="dashboard.php?userid=<?php echo $emm;?>"><i class="pe-7s-file"></i><span><b>Dashboard</b></span></a></li>
                <li><a href="export_excel.php?userid=<?php echo $emm;?>"><i class="pe-7s-file"></i><span><b>Import CSV File</b></span></a></li>
                <?php } ?>
                <?php if(in_array($user_type,['Team Leader','Validator','GO'])){ ?>
                <li class=""><a href="validation.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Masterlist</b></span></a></li>
		
				<li class="active">
					<a href="served_list.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Served List</span></b> </a>
				</li>	                
<?php } ?>
                <?php if(in_array($user_type,['Team Leader','Tagger'])){ ?>
                <li><a href="tagger.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Tagging</b></span></a></li>
                <li><a href="claimed.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Claimed</b></span></a></li>
                <?php } ?>
                <?php if(in_array($user_type,['Team Leader','Payroll'])){ ?>
                <li><a href="payroll_list.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>List of Payroll</b></span></a></li>
                <?php } ?>
                <?php if($user_type == 'Team Leader'){ ?>
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

    <div class="main-panel">
        <nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                            <?= strtoupper(htmlspecialchars($empname ?? '')) ?> - <?= strtoupper(htmlspecialchars($user_type ?? '')) ?>
                        </a>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="logout.php"><p>Log out</p></a></li>
                </ul>
            </div>
        </nav>

        <div class="content">
            <div class="table-responsive">
                <table id="payrollTable" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Beneficiary Name</th>
                            <th>DOB</th>
                            <th>Province</th>
                            <th>Municipality</th>
                            <th>Last Served Location</th>
                            <th>Date last Served</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#payrollTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'fetch_served_list.php', // your server-side endpoint
            type: 'POST'
        },
        pageLength: 10,
        columns: [
            { data: 0 }, // No.
            { data: 1 }, // Fullname
            { data: 2 }, // DOB
            { data: 3 }, // Province
            { data: 4 },  // Municipality
            { data: 5 },  // Date Last Served
            { data: 6 }  // Last Served Location
        ],
        order: [[0,'asc']]
    });
});
</script>

</body>
</html>
