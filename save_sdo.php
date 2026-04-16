<?php
include 'dbconnect.php';  // Include your database connection file

    $empid 		= $_POST['empid'];
    $sdo_status = $_POST['sdo_status'];
    $sdo_no 	= $_POST['sdo_no'];
    $tl_id 		= $_POST['tl_id'];
    $sdo_limit 	= $_POST['sdo_limit'];
	
	
	 $sql2 = "SELECT sdo_id FROM lib_sdo where sdo_id='$empid'";
	$result2 = mysqli_query($conn, $sql2);
	
	if ($row2 = mysqli_fetch_assoc($result2)) {
		$sql1 = "UPDATE lib_sdo set sdo_status='$sdo_status',sdo_no='$sdo_no', sdo_team_leader='$tl_id',sdo_limit='$sdo_limit' WHERE sdo_id='$empid'";
		$run2=mysqli_query($conn,$sql1);
	}else{
		$sql1 = "INSERT INTO lib_sdo (sdo_no,sdo_id,sdo_status, sdo_team_leader) VALUES ('$sdo_no' ,'$empid','$sdo_status', '$tl_id')";
		$run2=mysqli_query($conn,$sql1);
	}
	
$conn->close();
?>