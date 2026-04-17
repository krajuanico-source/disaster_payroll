<?php
include "dbconnect.php"; 
$conn->set_charset("utf8mb4");
$conn->query("SET NAMES 'utf8mb4'");

date_default_timezone_set('Asia/Manila');
$datetoday = date("Y-m-d H:i:s");
$beneId = $_POST['bene_id'];
$newFname = $_POST['new_fname'];
$newAmount = $_POST['new_amount'];
$newMname = $_POST['new_mname'];
$newLname = $_POST['new_lname'];
$newEname = $_POST['new_ename'];
$newDob = $_POST['new_dob'];
$newDom = $_POST['new_dom'];
$newDoy = $_POST['new_doy'];
$province = $_POST['province'];
$citymuni = $_POST['citymuni'];
$barangay = $_POST['barangay'];
$purok  = $_POST['purok'];
$user_id  = $_POST['user_id'];
$sex = $_POST['sex'];         // NEW
$gcash = $_POST['gcash'];     // NEW
$pcn = $_POST['pcn'];         // NEW

$sqlb = "SELECT payroll_no,team_leader,control_number from ect_clean_list where id='$beneId'";
$resultb = mysqli_query($conn, $sqlb);
if ($rowb = mysqli_fetch_assoc($resultb)) {
    $payroll_no = $rowb['payroll_no'];
    $team_leader = $rowb['team_leader'];
    $control_number = $rowb['control_number'];
}

$newFirstname = str_replace(['Ã', 'Ã±','ÃÂ'], 'Ñ', $newFname);
$newMidname   = str_replace(['Ã', 'Ã±','ÃÂ'], 'Ñ', $newMname);
$newLastname  = str_replace(['Ã', 'Ã±','ÃÂ'], 'Ñ', $newLname);
$newExtensionname  = str_replace(['Ã', 'Ã±','ÃÂ'], 'Ñ', $newEname);

$sql = "INSERT INTO tbl_correction_details 
            (amount, bene_id, new_fname, new_mname, new_lname, new_ename, birthday, birthmonth, birthyear,
             province, city, barangay, purok, payroll_no, team_leader, controlNo,
             added_by, date_added, sex, gcash, pcn)
        VALUES 
            ('$newAmount', '$beneId', '$newFirstname', '$newMidname', '$newLastname', '$newExtensionname',
             '$newDob', '$newDom', '$newDoy', '$province', '$citymuni', '$barangay', '$purok',
             '$payroll_no', '$team_leader', '$control_number', '$user_id', '$datetoday',
             '$sex', '$gcash', '$pcn')";
$result = $conn->query($sql);

$sqlq = "UPDATE ect_clean_list SET status='Correction', validated_by='$user_id'  WHERE id='$beneId'";
$resultq = $conn->query($sqlq);

if ($result) {
    echo "Correction details saved successfully!";
} else {
    echo "Error saving correction details. Please try again.";
}
?>