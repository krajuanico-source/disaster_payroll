<?php
include 'dbconnect.php';  // Include your database connection file
date_default_timezone_set('Asia/Manila');
$dateToday = date("Y-m-d H:i:s");

$payrollNo = $_POST['payroll_no'];

// Query to retrieve payroll information
$sqlPay = "SELECT * FROM tbl_payroll_list WHERE payroll_no = '$payrollNo'";
$resultPay = $conn->query($sqlPay);

if ($pay = $resultPay->fetch_assoc()) {
    $dateCreated 	= $pay['date_created'];
    $cityMuni 		= $pay['city_muni'];
    $teamLeader 	= $pay['team_leader'];
    $auditNo 		= $pay['audit_no'];
    $action 		= "Re-open Payroll $payrollNo";


    // Save the audit log
    $saveToDtr1 = "INSERT INTO tbl_audit_log (description, updated_by, date_updated, audit_no) VALUES ('$action', '$teamLeader', '$dateToday', '$auditNo')";
    mysqli_query($conn, $saveToDtr1);

    // Update payroll status to 'Active'
    $updatePayrollStatus = "UPDATE tbl_payroll_list SET payroll_status = 'Active', audit_no = '$auditNo' WHERE payroll_no = '$payrollNo' AND payroll_status = 'Closed'";
    mysqli_query($conn, $updatePayrollStatus);

    // Update beneficiary status to 'null'
    $updateBeneficiaryStatus = "UPDATE tbl_bene SET status = null WHERE team_leader = '$teamLeader' AND payroll_date = '$dateCreated' AND city_muni = '$cityMuni' AND status = 'Closed'";
    mysqli_query($conn, $updateBeneficiaryStatus);
}
?>