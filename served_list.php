<!doctype html>
<?php
session_start();
error_reporting(0);
include "dbconnect.php";

$emm = $_SESSION['userid'];
if(empty($emm)){
    echo "<script>window.open('index.php','_self')</script>";
    exit;
}

// Get user info
$user = $conn->query("SELECT user_type, team_leader, payroll_no FROM lib_users WHERE empid='$emm'")->fetch_assoc();
$user_type = $user['user_type'];
$team_leader = $user['team_leader'];
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
body { font-size:16px; }
.modal { z-index:1060; }
tr.success { background-color: #dff0d8; }
tr.danger { background-color: #f2dede; }
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
                <div class="navbar-header">
                    <a class="navbar-brand" href="#"><?php echo strtoupper($user_type); ?></a>
                </div>
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
            { data: 5 }  // Municipality
        ],
        order: [[0,'asc']]
    });
});
</script>

</body>
</html>
