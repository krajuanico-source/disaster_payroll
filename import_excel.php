<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$emm = $_SESSION['userid'];
date_default_timezone_set('Asia/Manila');
$date_reg = date("Y-m-d");

include "dbconnect.php";  // Database connection
$payroll_date    = $_POST['payroll_date'];
$payroll_staff   = $_POST['payroll_staff'];
$project_name    = $_POST['project_name'];
$program_title   = $_POST['program_title'];
$amount          = $_POST['amount'];
$sdo_num         = $_POST['sdo'];
$agency          = $_POST['fund_source'];
$team_leader     = $_POST['team_leader'];
$payrollNo		 = "";
$payrollNumbersMap = [];
$currentPayrollNumber = 1; // Start payroll number counter


$payrollNoTl = "SELECT payroll_no FROM tbl_payroll_list WHERE team_leader='$team_leader' AND payroll_status IS NULL";
$resultTl = $conn->query($payrollNoTl);

if ($resultTl->num_rows > 0) {
    while ($rowTl = $resultTl->fetch_assoc()) {
		$payrollNo=$rowTl['payroll_no'];
    }
} else {
   $createPayroll = "SELECT MAX(payroll_no)+1 as payrollNo FROM tbl_payroll_list";
	$resultPayroll = $conn->query($createPayroll);
	$rowPayroll = $resultPayroll->fetch_assoc();
	$payrollNo = $rowPayroll['payrollNo'] ?? 1;
	
	$savePayroll = "INSERT INTO tbl_payroll_list (payroll_no,team_leader,sdo) VALUES ('$payrollNo','$team_leader','$sdo_num')";
	$runPayroll = mysqli_query($conn, $savePayroll);


}



					
// Check if the form was submitted
if (isset($_POST['submit'])) {
    // Get the uploaded file
    $file = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'];

    // Check if the file name already exists in the log
    $checkFileQuery = "SELECT * FROM tbl_import_log WHERE file_name = '$file_name'";
    $checkFileResult = $conn->query($checkFileQuery);

    if ($checkFileResult->num_rows > 0) {
        // If the file name already exists, show an error message and exit
        echo "<script>alert('Error: This file has already been uploaded.')</script>";
    } else {
        // Proceed with file processing since the file is not a duplicate

        // Check if the file exists before processing
        if (($handle = fopen($file, "r")) !== FALSE) {

            // Skip the first row (header)
            fgetcsv($handle); // Ignore the header row

            // Loop through each row in the CSV file
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Sanitize data and prepare for insertion
                $no = $conn->real_escape_string($data[0]);
                $fname = $conn->real_escape_string(iconv('ISO-8859-1', 'UTF-8', $data[2]));
                $lname = $conn->real_escape_string(iconv('ISO-8859-1', 'UTF-8', $data[1]));
                $mname = $conn->real_escape_string(iconv('ISO-8859-1', 'UTF-8', $data[3]));
                $ename = $conn->real_escape_string(iconv('ISO-8859-1', 'UTF-8', $data[4]));
                $dob = $conn->real_escape_string(htmlentities($data[5]));
                $dom = $conn->real_escape_string(htmlentities($data[6]));
                $doy = $conn->real_escape_string(htmlentities($data[7]));
                $city = $conn->real_escape_string(iconv('ISO-8859-1', 'UTF-8', $data[10]));
                $province = $conn->real_escape_string(iconv('ISO-8859-1', 'UTF-8', $data[11]));
                $amount = $conn->real_escape_string(iconv('ISO-8859-1', 'UTF-8', $data[12]));
                $brgy = $conn->real_escape_string(iconv('ISO-8859-1', 'UTF-8', $data[9]));
                $purok = $conn->real_escape_string(iconv('ISO-8859-1', 'UTF-8', $data[8]));

                // Create a unique key for the province, city, and barangay combination
                $combinationKey = $province . '-' . $city;

                // Check if the combination has already been assigned a payroll number
                if (!isset($payrollNumbersMap[$combinationKey])) {
                    $action = 'Imported a list of beneficiaries on ' . $payroll_date;

                    // Get the current audit log ID
                    $sql1 = "SELECT MAX(id) AS ctr_audit FROM tbl_audit_log";
                    $result1 = $conn->query($sql1);
                    $row1 = $result1->fetch_assoc();
                    $ctr_audit = $row1['ctr_audit'] + 1;

                    // Insert into audit log
                    $save_to_dtr1 = "INSERT INTO tbl_audit_log (description, updated_by, date_updated, audit_no) 
                                     VALUES ('$action', '$emm', '$date_reg', '$ctr_audit')";
                    $rundtr1 = mysqli_query($conn, $save_to_dtr1);
					
					
                }

                // Insert the beneficiary details into tbl_bene
                if ($no !== "NO") {
                    $sql2 = "INSERT INTO ect_clean_list (control_number, first_name, last_name,middle_name,extension_name, birth_day,birth_month,birth_year,purok, barangay, city_municipality, province, team_leader, payroll_date, sdo_id,payroll_no,date_processed,amount)
                             VALUES ('$no', '$fname', '$lname', '$mname', '$ename', '$dob', '$dom', '$doy', '$purok', '$brgy', '$city', '$province', '$team_leader', '$payroll_date', '$sdo_num','$payrollNo','$date_reg','$amount')";

                    if ($conn->query($sql2) !== TRUE) {
                        echo "Error: " . $conn->error;
                    }
                }
            }

            // Close the CSV file
            fclose($handle);

            // Log the file name into tbl_import_log to avoid duplicate imports
            $logFileQuery = "INSERT INTO tbl_import_log (file_name, import_date) VALUES ('$file_name', NOW())";
            $conn->query($logFileQuery);
        } else {
            echo "Error opening the CSV file.";
        }
    }
	
$selectUser = "SELECT empid from lib_users where empid='$team_leader'";
$resultUser = $conn->query($selectUser);
$rowUser = $resultUser->fetch_assoc();
	$empid = $rowUser['empid'];

	if($empid==$team_leader){
		 $save_special = "UPDATE lib_users SET payroll_no='$payrollNo',date_assigned='$date_reg',user_type='Team Leader',team_leader='$empid' WHERE empid='$empid'";
			if ($conn->query($save_special) === TRUE) { 
				echo "Status updated successfully";
			} else {
				echo "Error: " . $conn->error;
			}
	}else{
		$sql2 = "INSERT INTO lib_users (empid, user_type, user_status,team_leader, payroll_no, date_assigned)
			VALUES ('$team_leader', 'Team Leader', '1', '$team_leader', '$payrollNo', '$date_reg')";
		if ($conn->query($sql2) !== TRUE) {
			echo "Error: " . $conn->error;
		}
	}

    // Close the database connection
$conn->close();
echo "<script>window.location.href='validation.php';</script>";
}
?>
