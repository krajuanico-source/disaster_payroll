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

$canDelete = ($user_type === 'Team Leader');

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
            <?php if ($canDelete) { ?>
            <div style="margin-bottom:10px; display:flex; flex-wrap:wrap; gap:20px; align-items:flex-end;">

                <div>
                    <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm" disabled>
                        Delete Selected (<span id="selectedCount">0</span>)
                    </button>
                </div>


                <div>
                    <button type="button" id="btnDeleteAll" class="btn btn-danger btn-sm">
                        Delete All Records
                    </button>
                </div>

            </div>

            <!-- Password confirmation modal — shared by "Delete Selected" and "Delete All" -->
            <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteConfirmTitle">Confirm Delete</h4>
                        </div>
                        <div class="modal-body">
                            <p id="deleteConfirmMessage"></p>
                            <input type="password" id="deleteConfirmPassword" class="form-control" placeholder="Your password" autocomplete="off">
                            <p id="deleteConfirmError" class="text-danger" style="margin-top:8px; display:none;"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="button" id="btnConfirmDelete" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="table-responsive">
                <table id="payrollTable" class="table table-bordered table-striped" style="width:100%;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Beneficiary Name</th>
                            <th>DOB</th>
                            <th>Province</th>
                            <th>Municipality</th>
                            <th>Last Served Location</th>
                            <th>Date last Served</th>
                            <?php if ($canDelete) { ?>
                            <th><input type="checkbox" id="selectAllServed" title="Select all on this page"></th>
                            <?php } ?>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const CAN_DELETE = <?php echo $canDelete ? 'true' : 'false'; ?>;

$(document).ready(function() {
    const columns = [
        { data: 0 }, // No.
        { data: 1 }, // Fullname
        { data: 2 }, // DOB
        { data: 3 }, // Province
        { data: 4 }, // Municipality
        { data: 5 }, // Last Served Location
        { data: 6 }  // Date Last Served
    ];

    if (CAN_DELETE) {
        columns.push({
            data: 7,
            orderable: false,
            searchable: false
        });
    }

    const table = $('#payrollTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'fetch_served_list.php',
            type: 'POST'
        },
        pageLength: 10,
        columns: columns,
        order: [[0, 'asc']]
    });

    if (!CAN_DELETE) {
        return;
    }

    const $bulkBtn = $('#btnBulkDelete');
    const $selectedCount = $('#selectedCount');
    const $selectAll = $('#selectAllServed');
    const $btnDeleteAll = $('#btnDeleteAll');

    function updateBulkButtonState() {
        const checkedCount = $('.row-select-served:checked').length;
        $selectedCount.text(checkedCount);
        $bulkBtn.prop('disabled', checkedCount === 0);
    }

    // Shared helper: POST to delete_served.php, reload table on success.
    // onError(message) lets the caller decide where to show the error
    // (e.g. inline in the password modal) instead of always alert()ing.
    function runDelete(payload, $triggerBtn, originalLabel, onSuccess, onError) {
        $triggerBtn.prop('disabled', true).text('Deleting...');

        $.ajax({
            url: 'delete_served.php',
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function(res) {
                if (res.success) {
                    onSuccess(res);
                } else {
                    onError(res.message || 'Unable to delete records.');
                }
            },
            error: function(xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'An error occurred while deleting records. Please try again.';
                onError(msg);
            },
            complete: function() {
                $triggerBtn.prop('disabled', false).text(originalLabel);
                updateBulkButtonState();
            }
        });
    }

    // Individual row checkbox toggled
    $('#payrollTable tbody').on('change', '.row-select-served', function() {
        // If any row is unchecked, the "select all" box shouldn't appear checked
        if (!$(this).is(':checked')) {
            $selectAll.prop('checked', false);
        }
        updateBulkButtonState();
    });

    // Select-all checkbox (applies to rows currently rendered on the page)
    $selectAll.on('change', function() {
        const checked = $(this).is(':checked');
        $('.row-select-served').prop('checked', checked);
        updateBulkButtonState();
    });

    // Reset selection state whenever the table redraws (new page, search, sort, reload)
    table.on('draw', function() {
        $selectAll.prop('checked', false);
        updateBulkButtonState();
    });

    // ------------------------------------------------------------------
    // Shared password-confirmation modal for both "Delete Selected" and
    // "Delete All". pendingDelete holds whichever action is awaiting the
    // TL's password.
    // ------------------------------------------------------------------
    const $modal = $('#deleteConfirmModal');
    const $modalTitle = $('#deleteConfirmTitle');
    const $modalMessage = $('#deleteConfirmMessage');
    const $modalPassword = $('#deleteConfirmPassword');
    const $modalError = $('#deleteConfirmError');
    const $btnConfirmDelete = $('#btnConfirmDelete');

    let pendingDelete = null; // { mode, ids?, label, triggerBtn, originalLabel }

    function openDeleteConfirm(title, message, pending) {
        pendingDelete = pending;
        $modalTitle.text(title);
        $modalMessage.text(message);
        $modalPassword.val('');
        $modalError.hide().text('');
        $modal.modal('show');
        setTimeout(() => $modalPassword.trigger('focus'), 300);
    }

    // Allow pressing Enter in the password field to submit
    $modalPassword.on('keypress', function(e) {
        if (e.which === 13) {
            $btnConfirmDelete.trigger('click');
        }
    });

    $btnConfirmDelete.on('click', function() {
        if (!pendingDelete) {
            return;
        }

        const password = $modalPassword.val();
        if (!password) {
            $modalError.text('Please enter your password.').show();
            return;
        }

        $modalError.hide().text('');
        $btnConfirmDelete.prop('disabled', true).text('Deleting...');

        const payload = Object.assign({ password: password }, pendingDelete.payload);

        runDelete(
            payload,
            pendingDelete.triggerBtn,
            pendingDelete.originalLabel,
            function(res) {
                $modal.modal('hide');
                $selectAll.prop('checked', false);
                table.ajax.reload(null, false); // reload, keep current page
                $btnConfirmDelete.prop('disabled', false).text('Delete');
                alert(res.message);
                pendingDelete = null;
            },
            function(message) {
                $modalError.text(message).show();
                $btnConfirmDelete.prop('disabled', false).text('Delete');
            }
        );
    });

    // Bulk delete button (selected checkboxes)
    $bulkBtn.on('click', function() {
        const ids = $('.row-select-served:checked').map(function() {
            return $(this).val();
        }).get();

        if (ids.length === 0) {
            return;
        }

        const label = ids.length === 1
            ? 'this record'
            : `these ${ids.length} records`;

        openDeleteConfirm(
            'Confirm Delete Selected Records',
            `This will move ${label} to the archive. Enter your password to confirm.`,
            {
                payload: { mode: 'selected', ids: ids },
                triggerBtn: $bulkBtn,
                originalLabel: 'Delete Selected (' + ids.length + ')'
            }
        );
    });

    // Delete all records
    $btnDeleteAll.on('click', function() {
        openDeleteConfirm(
            'Confirm Delete All Records',
            'This will move every served list record to the archive. Enter your password to confirm.',
            {
                payload: { mode: 'all' },
                triggerBtn: $btnDeleteAll,
                originalLabel: 'Delete All Records'
            }
        );
    });
});
</script>

</body>
</html>