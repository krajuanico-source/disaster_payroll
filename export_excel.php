<!doctype html>
<?php
session_start();
include "dbconnect.php";

$emm = $_SESSION['userid'] ?? '';
$user_type = $_SESSION['user_type'] ?? '';

if ($emm == '' || $emm == NULL) {
    echo "<script>window.open('index.php','_self')</script>";
    exit;
}

$stmt1 = $conn->prepare("SELECT user_type, empname FROM lib_users WHERE empid = ?");
$stmt1->bind_param("s", $emm);
$stmt1->execute();
$row1 = $stmt1->get_result()->fetch_assoc();
$user_type = $row1['user_type'];
$empname   = $row1['empname'];

?>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="images/icons/dswd_icon.jpg">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>DSWD FO XI - ECT Payroll System</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.min.css" rel="stylesheet" />
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet" />
    <link href="assets/css/demo.css" rel="stylesheet" />
    <link href='css/googleapis.css' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
		.btn {
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
                        <img src="images/icons/dswd_logo_white_2.png" style="width:82%; height:82%; margin-left:-3%" alt="..." />
                    </a>
                </div>
                <ul class="nav">
                    <?php if ($user_type == 'Team Leader') { ?>
                        <li>
                            <a href="dashboard.php?userid=<?php echo $emm; ?>"><i class="pe-7s-file"></i><span><b>Dashboard</b></span></a>
                        </li>
                        <li class="active">
                            <a href="export_excel.php?userid=<?php echo $emm; ?>"><i class="pe-7s-file"></i><span><b>Import CSV File</b></span></a>
                        </li>
                    <?php } ?>
                    <input hidden id="user_id" value="<?php echo $emm; ?>" />
                    <?php if ($user_type == 'Team Leader' || $user_type == 'Validator') { ?>
                        <li>
                            <a href="validation.php?userid=<?php echo $emm; ?>"><i class="pe-7s-folder"></i><span><b>Masterlist</b></span></a>
                        </li>
                    <?php } ?>
                    <?php if ($user_type == 'Team Leader' || $user_type == 'Validator' || $user_type == 'GO') { ?>
                        <li class="">
                            <a href="served_list.php?userid=<?php echo $emm; ?>"><i class="pe-7s-folder"></i><span><b>Served List</b></span></a>
                        </li>
                    <?php }
                    if ($user_type == 'Team Leader' || $user_type == 'Tagger') { ?>
                        <li>
                            <a href="tagger.php?userid=<?php echo $emm; ?>"><i class="pe-7s-folder"></i><span><b>Tagging</b></span></a>
                        </li>
                        <li>
                            <a href="claimed.php?userid=<?php echo $emm; ?>"><i class="pe-7s-folder"></i><span><b>Claimed</b></span></a>
                        </li>
                    <?php }
                    if ($user_type == 'Team Leader' || $user_type == 'Payroll') { ?>
                        <li>
                            <a href="payroll_list.php?userid=<?php echo $emm; ?>"><i class="pe-7s-folder"></i><span><b>List of Payroll</b></span></a>
                        </li>
                    <?php }
                    if ($user_type == 'Team Leader') { ?>
                        <li>
                            <a href="#subPages6" data-toggle="collapse" class="collapsed"><i class="pe-7s-user"></i><span><b>Team Leader</b></span></a>
                            <div id="subPages6" class="collapse">
                                <ul>
                                    <li>
                                        <a href="user.php?userid=<?php echo $emm; ?>">
                                            <i class="pe-7s-angle-right"></i>
                                            <p>User</p>
                                        </a>
                                        <a href="fund_source.php?userid=<?php echo $emm; ?>">
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
                <div class="content" style="width:100%">
                    <div class="container-fluid">
                        <form action="import_excel.php" method="post" target="_self" enctype="multipart/form-data">

                            <div class="table-responsive" style="background-color:#fff; ">
                                <br>
                                <br>

                                <table class="table table-bordered" style="width:70%; margin-left:15%">
                                    <tr>
                                        <td width="30%"><label style="font-size: 20px;font-family:Arial Narrow;">PREPARED BY</label></td>
                                        <td width="70%"><input type="text" name="prepared_by" class="form-control" value="<?php echo htmlspecialchars($empname); ?>" readonly required /></td>
                                    </tr>
                                    <tr>
                                        <td width="20%"><label style="font-size: 20px;font-family:Arial Narrow;">PAYROLL DATE</label></td>
                                        <td width="80%"><input type="date" name="payroll_date" class="form-control" required /></td>
                                    </tr>
                                    <tr>
                                        <td width="30%"><label style="font-size: 20px;font-family:Arial Narrow;">SDO</label></td>
                                        <td width="70%"><input type="text" name="sdo_name" class="form-control" required /></td>
                                    </tr>
                                    <tr>
                                        <td width="20%"><label style="font-size: 20px;font-family:Arial Narrow;">IMPORT FILE</label></td>
                                        <td width="80%"><input type="file" name="file" accept=".csv" required></td>
                                    </tr>
                        
                                </table>
								<div class="modal-footer" style="width:100%; margin-top:5%">
                                <center><input type="submit" name="submit" class="btn btn-primary" value="IMPORT LIST"></center>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/js/chartist.min.js"></script>
    <script src="assets/js/bootstrap-notify.js"></script>
    <script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>
    <script src="assets/js/demo.js"></script>

    <script>
        $(document).ready(function() {
            var max_fields = 100; 
            var wrapper = $(".input_party_wrap2");
            var add_button = $(".add_party_button2"); 

            var x = 1; 
            $(add_button).click(function(e) { 
                e.preventDefault();
                if (x < max_fields) { 
                    x++;
                    $(wrapper).append('<div class="row"><br>' +
                        '<div class="col-md-3">' +
                        '<input type="text" name="fname[]" class="form-control" placeholder="First Name" required>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<input type="text" name="mname[]" class="form-control" placeholder="Middle Name" >' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<input type="text" name="lname[]" class="form-control" placeholder="Last Name" required>' +
                        '</div>' +
                        '<div class="col-md-2">' +
                        '<select class="form-control" name="ename[]" id="ename" class="selectpicker">' +
                        '<option disabled selected>Ext. Name</option>' +
                        '<option value=""> None</option>' +
                        '<option value="Jr">Jr</option>' +
                        '<option value="Sr">Sr</option>' +
                        '<option value="I">I</option>' +
                        '<option value="II">II</option>' +
                        '<option value="III">III</option>' +
                        '<option value="IV">IV</option>' +
                        '</select>' +
                        '</div>' +
                        '<button style="width:40px" class="btn btn-danger remove_party2">-</button>' +
                        '</div>'); 
                }
            });

            $(wrapper).on("click", ".remove_party2", function(e) { 
                e.preventDefault();
                $(this).parent('div').remove();
                x--;
            });
        });
    </script>
</body>
</html>
<?php ?>