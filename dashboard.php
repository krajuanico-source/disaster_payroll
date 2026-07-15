<!doctype html>
<?php
session_start();
include "dbconnect.php";

$emm = $_GET['userid'] ?? '';
$user_type = $_SESSION['user_type'] ?? '';

if ($emm == '' || $emm == NULL) {
    echo "<script>window.open('index.php','_self')</script>";
    exit;
}

$stmt2 = $conn->prepare("SELECT team_leader, payroll_no, empname FROM lib_users WHERE empid = ?");
$stmt2->bind_param("s", $emm);
$stmt2->execute();
$row2 = $stmt2->get_result()->fetch_assoc();
$empname = $row2['empname'] ?? '';

$stmt2b = $conn->prepare("SELECT payroll_no FROM tbl_payroll_list WHERE team_leader = ? AND payroll_status = 'Active' ORDER BY payroll_no DESC LIMIT 1");
$stmt2b->bind_param("s", $emm);
$stmt2b->execute();
$activeRow = $stmt2b->get_result()->fetch_assoc();
$payroll_no = $activeRow['payroll_no'] ?? $row2['payroll_no'] ?? null;
function getCount($conn, $payroll_no, $status = null)
{
    if ($status === null) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM ect_clean_list e
            INNER JOIN tbl_payroll_list p ON e.payroll_no = p.payroll_no
            WHERE e.payroll_no = ? 
              AND p.payroll_status = 'Active'
              AND e.status IS NULL
        ");
        $stmt->bind_param("s", $payroll_no);
    } else {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM ect_clean_list e
            INNER JOIN tbl_payroll_list p ON e.payroll_no = p.payroll_no
            WHERE e.status = ? 
              AND e.payroll_no = ?
              AND p.payroll_status = 'Active'
        ");
        $stmt->bind_param("ss", $status, $payroll_no);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'];
}

$toBeServed  = getCount($conn, $payroll_no);
$validated   = getCount($conn, $payroll_no, 'Validated');
$claimed     = getCount($conn, $payroll_no, 'Claimed');
$replacement = getCount($conn, $payroll_no, 'Replacement');
$correction  = getCount($conn, $payroll_no, 'Correction');
$disqualfied = getCount($conn, $payroll_no, 'Disqualified');
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
    <link href='css/googleapis.css' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" charset="utf8" src="js/jquery.dataTables.min.js"></script>
    <script src="js/chart.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        .modal {
            z-index: 1060;
        }

        .modal-xl {
            max-width: 90%;
            width: 90%;
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
            margin-bottom: 5px;
            font-size: 12px;
        }

        .card {
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

        #validatorBarChart {
            width: 100%;
            height: 10%;
        }

        #runImportbtn,
        #syncDataButton1 {
            border-radius: 5px !important;
            padding: 6px 22px !important;
            background-color: #1d4bb6 !important;
            border-color: #1d4bb6 !important;
            color: #fff !important;
            min-width: 100px;
            letter-spacing: 0.3px;
        }

        #runImportbtn:hover,
        #syncDataButton1:hover {
            background-color: #5a7dce !important;
            border-color: #5a7dce !important;
            color: #fff !important;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 25px;
            padding: 25px;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            padding: 25px;
            color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .15);
            transition: all .3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .25);
        }

        .stat-card h2 {
            font-size: 42px;
            font-weight: bold;
            margin: 0;
            color: #fff;
        }

        .stat-card p {
            margin-top: 10px;
            font-size: 16px;
            color: rgba(255, 255, 255, .9);
            font-weight: 900;
        }

        .stat-card .icon {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 65px;
            opacity: .2;
        }

        .served {
            background: linear-gradient(135deg, #1d4bb2, #4c78db);
            background: linear-gradient(135deg, #17a2b8, #39c9e0);
        }

        .validated {
            background: linear-gradient(135deg, #1d4bb2, #4c78db);
        }

        .claimed {
            background: linear-gradient(135deg, #379520, #58c23e);
        }

        .replacement {
            background: linear-gradient(135deg, #eec703, #f7dc54);
        }

        .correction {
            background: linear-gradient(135deg, #dc3545, #f05b69);
        }

        .disqualified {
            background: linear-gradient(135deg, #ff7f50, #ff9f7a);
        }

        .dashboard-wrapper {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-top: 50px;
        }

        .dashboard-title {
            color: black;
            margin: 0;
            padding: 20px;
            text-align: center;
            font-weight: 700;
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

    <div class="modal" id="modal" tabindex="-1" aria-labelledby="confirmSyncModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmSyncModalLabel" style="color:red">Reminders: Import Served File first!</h5>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <input type="file" class="form-control" id="ImportfileInput" name="csvFile" accept=".csv, .xlsx, .xls" />
                        <button type="button" class="btn" style="margin-top:2%" id="runImportbtn" onclick="runImport()">Update Served List</button>
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
            <div class="sidebar-wrapper">
                <div class="logo">
                    <a href="#" class="simple-text">
                        <img src="images/icons/dswd_logo_white_2.png" style="width:82%; height:82%; margin-left:-3%" alt="..." />
                    </a>
                </div>
                <ul class="nav">
                    <?php if ($user_type == 'Team Leader') { ?>
                        <li class="active">
                            <a href="dashboard.php?userid=<?php echo $emm; ?>"><i class="pe-7s-file"></i><span><b>Dashboard</b></span></a>
                        </li>
                        <li>
                            <a href="export_excel.php?userid=<?php echo $emm; ?>"><i class="pe-7s-file"></i><span><b>Import CSV File</b></span></a>
                        </li>
                    <?php } ?>
                    <input hidden id="user_id" value="<?php echo $emm; ?>" />
                    <?php if ($user_type == 'Team Leader' || $user_type == 'Validator') { ?>
                        <li>
                            <a href="validation.php?userid=<?php echo $emm; ?>"><i class="pe-7s-folder"></i><span><b>Masterlist</b></span></a>
                        </li>
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
                </div>
            </div>
        </div>

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
            <nav class="navbar navbar-default navbar-fixed" style="background-color:#fff">
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

            <div class="content" style="width:100%;">
                <input hidden id="payrollNo" value="<?php echo $payroll_no; ?>" />

                <div class="row">
                    <div class="form-inline">
                        <div class="col-md-19" style="float: left;">
                            <button type="button" class="btn" id="syncDataButton1">
                                <i class="fas fa-file-export"></i> Export Payroll
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3" style="float: left">
                            <select class="form-control" id="payroll_no_tl" name="payroll_no_tl">
                                <option disabled selected>Select Payroll No.</option>
                                <?php
                                $stmt3 = $conn->prepare("SELECT payroll_no FROM tbl_payroll_list WHERE team_leader = ?");
                                $stmt3->bind_param("s", $emm);
                                $stmt3->execute();
                                $result3 = $stmt3->get_result();
                                while ($row3 = $result3->fetch_assoc()) { ?>
                                    <option value="<?php echo $row3['payroll_no']; ?>">
                                        <?php echo $row3['payroll_no']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="container-fluid">
                        <div class="dashboard-wrapper">
                            <h3 class="dashboard-title">BENEFICIARY'S DASHBOARD</h3>
                        </div>

                        <div class="dashboard">
                            <div class="stat-card served">
                                <i class="pe-7s-users icon"></i>
                                <h2><?= $toBeServed ?></h2>
                                <p>To Be Served</p>
                            </div>
                            <div class="stat-card validated">
                                <i class="pe-7s-check icon"></i>
                                <h2><?= $validated ?></h2>
                                <p>Validated</p>
                            </div>
                            <div class="stat-card claimed">
                                <i class="pe-7s-cash icon"></i>
                                <h2><?= $claimed ?></h2>
                                <p>Claimed</p>
                            </div>
                            <div class="stat-card replacement">
                                <i class="pe-7s-refresh-2 icon"></i>
                                <h2><?= $replacement ?></h2>
                                <p>For Replacement</p>
                            </div>
                            <div class="stat-card correction">
                                <i class="pe-7s-note2 icon"></i>
                                <h2><?= $correction ?></h2>
                                <p>For Correction</p>
                            </div>
                            <div class="stat-card disqualified">
                                <i class="pe-7s-close-circle icon"></i>
                                <h2><?= $disqualfied ?></h2>
                                <p>Disqualified</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>
    <script src="assets/js/demo.js"></script>

    <script>
        $(document).ready(function() {
            // Show the "import served file first" reminder modal on page load
            $('#modal').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });

            $('#closeModal2').on('click', function() {
                $('#modal').modal('hide');
            });

            // Open export confirmation modal
            $('#syncDataButton1').on('click', function() {
                $('#confirmSyncModal').modal('show');
            });

            // Reload dashboard stat cards whenever the payroll number changes
            $('#payroll_no_tl').on('change', function() {
                var payroll_no = $(this).val();
                $('#payrollNo').val(payroll_no);

                $.ajax({
                    type: 'POST',
                    url: 'fetch_dashboard_data.php',
                    data: { payroll_no: payroll_no },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            $('.dashboard').html(`
                                <div class="stat-card served">
                                    <i class="pe-7s-users icon"></i>
                                    <h2>${data.toBeServed}</h2>
                                    <p>To Be Served</p>
                                </div>
                                <div class="stat-card validated">
                                    <i class="pe-7s-check icon"></i>
                                    <h2>${data.validated}</h2>
                                    <p>Validated</p>
                                </div>
                                <div class="stat-card claimed">
                                    <i class="pe-7s-cash icon"></i>
                                    <h2>${data.claimed}</h2>
                                    <p>Claimed</p>
                                </div>
                                <div class="stat-card replacement">
                                    <i class="pe-7s-refresh-2 icon"></i>
                                    <h2>${data.replacement}</h2>
                                    <p>For Replacement</p>
                                </div>
                                <div class="stat-card correction">
                                    <i class="pe-7s-note2 icon"></i>
                                    <h2>${data.correction}</h2>
                                    <p>For Correction</p>
                                </div>
                                <div class="stat-card disqualified">
                                    <i class="pe-7s-close-circle icon"></i>
                                    <h2>${data.disqualified}</h2>
                                    <p>Disqualified</p>
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

            // Proceed with export
            $('#exportClaimedBenes').on('click', function() {
                var teamLeader = "<?php echo $emm; ?>";
                var payrollNo = $('#payroll_no_tl').val() || $('#payrollNo').val();

                if (!payrollNo) {
                    alert('Please select a payroll number before exporting.');
                    return;
                }

                $('#confirmSyncModal').modal('hide');
                window.open(
                    "exportClaimedBenes.php?teamLeader=" + encodeURIComponent(teamLeader) +
                    "&payrollNo=" + encodeURIComponent(payrollNo),
                    "_blank"
                );
            });
        });

        // Handle CSV/Excel import
        function runImport() {
            let fileInput = document.getElementById("ImportfileInput");
            let file = fileInput.files[0];

            if (!file || !/\.csv$/i.test(file.name)) {
                alert("Please upload a valid CSV file only.");
                return;
            }

            let closeModalBtn = document.getElementById("closeModal2");
            closeModalBtn.style.display = "inline-block";
            closeModalBtn.disabled = true;
            closeModalBtn.classList.remove("btn-primary", "btn-danger");
            closeModalBtn.classList.add("btn-info");
            closeModalBtn.innerText = "Please wait...";

            document.getElementById('runImportbtn').disabled = true;

            let formData = new FormData();
            formData.append("csvFile", file);

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "upload.php", true);

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    let percentComplete = (e.loaded / e.total) * 100;
                    document.getElementById("progressBar1").style.width = percentComplete + "%";
                    document.getElementById("progressPercentage1").innerText = Math.round(percentComplete) + "%";
                }
            };

            xhr.onload = function() {
                document.getElementById('runImportbtn').disabled = false;
                closeModalBtn.disabled = false;

                if (xhr.status != 200) {
                    alert("Upload Failed! Server error...");
                    resetToFailedState(closeModalBtn);
                    return;
                }

                let response = xhr.responseText.trim();

                if (response === "success") {
                    document.getElementById("progressBar1").style.width = "100%";
                    document.getElementById("progressPercentage1").innerText = "100%";
                    alert("Served list imported successfully");
                    closeModalBtn.classList.remove("btn-danger", "btn-info");
                    closeModalBtn.classList.add("btn-primary");
                    closeModalBtn.innerText = "Done";
                } else if (response === "invalid_format" || response === "invalid_file_type") {
                    alert("Invalid File Format, please follow the format..");
                    resetToFailedState(closeModalBtn);
                } else if (response === "file_exists") {
                    alert("This file has already been uploaded!!");
                    resetToFailedState(closeModalBtn);
                } else {
                    alert("Upload Failed! Server error...");
                    resetToFailedState(closeModalBtn);
                }
            };

            xhr.onerror = function() {
                document.getElementById('runImportbtn').disabled = false;
                closeModalBtn.disabled = false;
                alert("Upload Failed! Server error...");
                resetToFailedState(closeModalBtn);
            };

            xhr.send(formData);
        }

        // Reset the progress bar and Cancel button after a failed/invalid import,
        // so the modal doesn't show 100% / "Done" for something that didn't succeed.
        function resetToFailedState(closeModalBtn) {
            document.getElementById("progressBar1").style.width = "0%";
            document.getElementById("progressPercentage1").innerText = "0%";
            closeModalBtn.classList.remove("btn-info", "btn-primary");
            closeModalBtn.classList.add("btn-danger");
            closeModalBtn.innerText = "Cancel";
        }
    </script>
</body>
</html>
<?php ?>