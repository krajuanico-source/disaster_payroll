<?php
include "dbconnect.php";
date_default_timezone_set('Asia/Manila');
$date_today = date("Y-m-d H:i:s");
session_start();
$emm=$_SESSION['userid'];

$sql1 = "SELECT MAX(id) as ctr_audit FROM tbl_audit_log";
$result1 = $conn->query($sql1);
$row1 = $result1->fetch_assoc();
$ctr_audit = $row1['ctr_audit'];
$action = 'Logout ' . $emm;
// Increment the audit log ID if audit_no is not empty
	if ($audit_no <> '' || $audit_no <> null) {
		$ctr_audit = $audit_no;
	} else {
		$ctr_audit = $ctr_audit + 1;
	}

	$save_to_dtr1 = "INSERT INTO tbl_audit_log (description, updated_by, date_updated, audit_no) VALUES ('$action', '$emm', '$date_today', '$ctr_audit')";
	$rundtr1 = mysqli_query($conn, $save_to_dtr1);

	$token = $_SESSION['token'];
	
	$sql_update_token = "UPDATE lib_users SET token = '' WHERE empid = '$emm' AND token = '$token'";
	mysqli_query($conn, $sql_update_token);
	
session_unset();
session_destroy();
header("location:index.php");
exit();
?>