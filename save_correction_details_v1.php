

<?php
include "dbconnect.php";  // Database connection
session_start();
$emm			= $_SESSION['userid'];
$beneId 		= $_POST['bene_id'];
$newFname 		= $_POST['new_fname'];
$newMname 		= $_POST['new_mname'];
$newLname 		= $_POST['new_lname'];
$new_ename   	= $_POST['new_ename']?? "";
$sex   			= $_POST['sex'];
$birthday   	= $_POST['birthday'];
$birthmonth   	= $_POST['birthmonth'];
$birthyear   	= $_POST['birthyear'];
$civil_status   = $_POST['civil_status'];
$occupation   	= $_POST['occupation'];
$salary   		= $_POST['salary'] ?? 0;
$category   	= $_POST['category'];
$sub_category   = $_POST['sub_category'];
$cnum   		= $_POST['cnum'];
$purok   		= $_POST['purok'];
$barangay   	= $_POST['barangay'];
$citymuni   	= $_POST['citymuni'];
$province   	= $_POST['province'];
$assistanceType = $_POST['assistanceType'];
$amount   		= $_POST['amount'] ?? 0;
$charging   	= $_POST['charging'];
$control_no   	= $_POST['control_no'];

$sql = "INSERT INTO tbl_correction_details (bene_id,new_fname,new_mname,new_lname,new_ename,birthday,birthmonth,birthyear,sex,civilStatus,category,subCategory,occupation,salary,contactNumber,purok,barangay,city,province,typeOfAssistance,amount,charging,controlNo)
VALUES ('$beneId','$newFname','$newMname','$newLname','$new_ename','$birthday','$birthmonth','$birthyear','$sex','$civil_status','$category','$sub_category','$occupation','$salary','$cnum','$purok','$barangay','$citymuni','$province','$assistanceType','$amount','$charging','$control_no');";
$result = $conn->query($sql);


$sql1 = "SELECT * FROM aics_clean_list where control_number='$control_no'";
$result1 = $conn->query($sql1);
if ($row = $result1->fetch_assoc()) {
	$fname 					= str_replace(['Ã', 'Ã±','ÃÂ'], 'Ñ', $row['first_name']);
	$mname 				  = str_replace(['Ã', 'Ã±','ÃÂ'], 'Ñ', $row['middle_name']);
	$lname 					= str_replace(['Ã', 'Ã±','ÃÂ'], 'Ñ', $row['last_name']);
	$ename 				   = str_replace(['Ã', 'Ã±','ÃÂ'], 'Ñ', $row['extension_name']);
	$birth_day 			   = $row['birth_day'];
	$birth_month 	     = $row['birth_month'];
	$birth_year 	       = $row['birth_year'];
	$beneId  		        = $row['id'];
	$civilStatus  		    = $row['civil_status'];
	$category  		       = $row['category'];
	$subcategory  		= $row['subcategory'];
	$sex  					  = $row['sex'];
	$monthly_salary    = $row['monthly_salary'];
								
	$sqli = "INSERT INTO tbl_correction_details
	(bene_id,controlNo,new_fname,new_mname,new_lname,new_ename,birthday,birthmonth,birthyear,status,added_by,civilStatus,category,subCategory,salary,sex) VALUES
	('$beneId','$control_no','$fname','$mname','$lname','$ename','$birth_day','$birth_month','$birth_year','Correction','$emm','$civilStatus','$category','$subcategory','$monthly_salary','$sex');";
	$resulti = $conn->query($sqli);
	

}


if ($result) {
  echo "Correction details saved successfully!";
} else {
  echo "Error saving correction details. Please try again.";
}
?>
