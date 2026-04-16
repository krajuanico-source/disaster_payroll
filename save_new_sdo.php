<?php
include "php/db.php";
include "dbconnect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sdo_no = $_POST['sdo_no'];
    $sdo_id = $_POST['sdo_id'];
    $sdo_status = $_POST['sdo_status'];
    $sdo_limit = $_POST['sdo_limit'];
    $tl_id = $_POST['tl_id'];

    // Insert the new SDO into the database
    $sql = "INSERT INTO lib_sdo (sdo_no, sdo_id, sdo_status, sdo_team_leader,sdo_limit) VALUES ('$sdo_no', '$sdo_id', '$sdo_status', '$tl_id','$sdo_limit')";

    if (mysqli_query($conn, $sql)) {
        echo "New SDO added successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
