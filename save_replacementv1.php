<?php
include "dbconnect.php";  // Database connection

$beneId = $_POST['bene_id'];
$newFname = $_POST['new_fname'];
$newMname = $_POST['new_mname'];
$newLname = $_POST['new_lname'];
$new_birth_date = $_POST['new_birth_date'];
$new_birth_month = $_POST['new_birth_month'];
$new_birth_year = $_POST['new_birth_year'];

$sql = "INSERT INTO tbl_replacement (bene_id, fname, mname, lname, birth_date,birth_month,birth_year) VALUES ('$beneId', '$newFname', '$newMname', '$newLname', '$new_birth_date','$new_birth_month','$new_birth_year')";
$result = $conn->query($sql);

$sqlq = "UPDATE ect_clean_list set status='Replacement' where id='$beneId'";
$resultq = $conn->query($sqlq);

if ($result) {
  echo "Correction details saved successfully!";
} else {
  echo "Error saving correction details. Please try again.";
}
?>