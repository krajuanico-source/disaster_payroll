<?php
include 'dbconnect.php';  // Include your database connection file

    $fs_id = $_POST['fs_id_2'];
    $fs_name = $_POST['fs_name_2'];
    $fs_stat = $_POST['fs_stat_2'];
    $tl_id = $_POST['tl_id'];
	
	$sql1 = "UPDATE lib_fund_source set fs_name='$fs_name', fs_status='$fs_stat' WHERE id='$fs_id'";
	$run2=mysqli_query($conn,$sql1);
	$conn->close();
?>
