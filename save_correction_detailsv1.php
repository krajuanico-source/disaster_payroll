<?php
include "dbconnect.php";  // Database connection

$beneId = $_POST['bene_id'];
$newFname = $_POST['new_fname'];
$newMname = $_POST['new_mname'];
$newLname = $_POST['new_lname'];
$newDob = $_POST['new_dob'];
$newDom = $_POST['new_dom'];
$newDoy = $_POST['new_doy'];
$province = $_POST['province'];
$citymuni = $_POST['citymuni'];
$barangay = $_POST['barangay'];
$purok  = $_POST['purok'];

$sqlb = "SELECT payroll_no,team_leader from ect_clean_list where id='$beneId'";
$resultb = mysqli_query($conn, $sqlb);
if ($rowb = mysqli_fetch_assoc($resultb)) 
  // Increment max payroll_no by 1, or set to 1 if it's null
  $payroll_no =  $rowb['payroll_no'];
  $team_leader =  $rowb['team_leader'];



$sql = "INSERT INTO tbl_correction_details (bene_id, new_fname, new_mname, new_lname, birthday,birthmonth,birthyear,province,city,barangay,purok,payroll_no,team_leader) 
VALUES ('$beneId', '$newFname', '$newMname', '$newLname', '$newDob','$newDom','$newDoy', '$province', '$citymuni','$barangay','$purok','$payroll_no','$team_leader')";
$result = $conn->query($sql);


    


$sqlq = "UPDATE ect_clean_list set status='Correction' where id='$beneId'";
$resultq = $conn->query($sqlq);

if ($result) {
  echo "Correction details saved successfully!";
} else {
  echo "Error saving correction details. Please try again.";
}
?>