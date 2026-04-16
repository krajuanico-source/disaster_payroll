<?php
include 'dbconnect.php';  // Include your database connection file
date_default_timezone_set('Asia/Manila');
$date_today		= date("Y-m-d");
    $empid = $_POST['empid'];
    $emppass = $_POST['emppass'];
	$empname = $_POST['empname'];
    $status = $_POST['selectedType'];
    $tl_id = $_POST['tl_id'];
    $payroll_no = $_POST['payroll_no'];
	
	$sql2 = "SELECT empid FROM lib_users where empid='$empid'";
	$result2 = mysqli_query($conn, $sql2);
	if ($row2 = mysqli_fetch_assoc($result2)) {
		$sql1 = "UPDATE lib_users SET user_type='$status', empname='$empname', team_leader='$tl_id', payroll_no='$payroll_no' WHERE empid='$empid'";
		$run2 = mysqli_query($conn, $sql1);
	} else {
		$sql1 = "INSERT INTO lib_users (empid, user_type, user_status, team_leader, payroll_no, emppassword, empname) 
				VALUES ('$empid', '$status', 1, '$tl_id', '$payroll_no', '$emppass', '$empname')";
		$run2 = mysqli_query($conn, $sql1); // ← this line was missing
	}
	
$conn->close();
?>
