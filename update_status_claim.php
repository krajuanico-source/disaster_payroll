<?php
include 'dbconnect.php';  // Include your database connection file
date_default_timezone_set('Asia/Manila');
session_start();


$emm=$_SESSION['userid'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bene_id = $_POST['bene_id'];
    $status = $_POST['status'];
echo $status;
    // Prepare and execute the update query
    if($status === "Claimed"){
        $save_special = "UPDATE ect_clean_list SET status='Validated' , tagged_by ='$emm' WHERE id='$bene_id'";
        if ($conn->query($save_special) === TRUE) {
            echo "Status updated successfully";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
$claimed_date = date("Y-m-d H:i:s");
        $save_special = "UPDATE ect_clean_list SET status='Claimed' , tagged_by ='$emm', claimed_date= '$claimed_date'  WHERE id='$bene_id'";
        if ($conn->query($save_special) === TRUE) { 
            echo "Status updated successfully";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Close the database connection
    $conn->close();
}
?>