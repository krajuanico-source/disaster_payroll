<?php
include 'dbconnect.php';  // Include your database connection file
date_default_timezone_set('Asia/Manila');
$date_today = date("Y-m-d H:i:s");

$payrollNo = $_POST['payroll_no'];

// Query to retrieve payroll information
$sqlPay = "SELECT * FROM tbl_payroll_list WHERE payroll_no = '$payrollNo'";
$resultPay = $conn->query($sqlPay);

if ($pay = $resultPay->fetch_assoc()) {
    $dateCreated = $pay['date_created'];
    $cityMuni = $pay['city_muni'];
    $teamLeader = $pay['team_leader'];
    $audit_no = $pay['audit_no'];
    $action = 'Closed Payroll ' . $payrollNo;

    // Get the current audit log ID
    $sql1 = "SELECT MAX(id) as ctr_audit FROM tbl_audit_log";
    $result1 = $conn->query($sql1);
    $row1 = $result1->fetch_assoc();
    $ctr_audit = $row1['ctr_audit'];

    // Increment the audit log ID if audit_no is not empty
    if ($audit_no <> '' || $audit_no <> null) {
        $ctr_audit = $audit_no;
    } else {
        $ctr_audit = $ctr_audit + 1;
		// Save the audit log
		
    }
	//INSERT audit_log
    $save_to_dtr1 = "INSERT INTO tbl_audit_log (description, updated_by, date_updated, audit_no) VALUES ('$action', '$teamLeader', '$date_today', '$ctr_audit')";
	$rundtr1 = mysqli_query($conn, $save_to_dtr1);

    // Update payroll status to 'Active'
    $updatePayrollStatus = "UPDATE tbl_payroll_list SET payroll_status = 'Closed', audit_no = '$ctr_audit' WHERE payroll_no = '$payrollNo'";
    $run1 = mysqli_query($conn, $updatePayrollStatus);

    // Update beneficiary status to 'null'
    $updateBeneficiaryStatus = "UPDATE tbl_bene SET status = 'Closed' WHERE team_leader = '$teamLeader' AND payroll_date = '$dateCreated' AND city_muni = '$cityMuni' AND status = null";
    $run2 = mysqli_query($conn, $updateBeneficiaryStatus);
}
?>