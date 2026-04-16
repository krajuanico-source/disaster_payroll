<?php
include "dbconnect.php";  // Database connection

$beneId = $_POST['bene_id'];
// $newFname = $_POST['new_fname'];
// $newMname = $_POST['new_mname'];
// $new_birth_year = $_POST['new_birth_year'];
// $new_birth_date = $_POST['new_birth_date'];
// $new_birth_month = $_POST['new_birth_month'];
// $new_birth_year = $_POST['new_birth_year'];
$user_id  = $_POST['user_id'];


// $sql = "INSERT INTO tbl_replacement (bene_id, fname, mname, lname, birth_date,birth_month,birth_year)VALUES ('$beneId')";
// $result = $conn->query($sql);

$sqlq = "UPDATE ect_clean_list set status='Replacement',validated_by='$user_id' where id='$beneId'";
$resultq = $conn->query($sqlq);

if ($result) {
  echo "Correction details saved successfully!";
} else {
  echo "Error saving correction details. Please try again.";
}
?>