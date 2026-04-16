<?php
include "dbconnect.php";  // Database connection

$reportType = $_POST['report_type'];
$payroll_no = $_POST['payrollNo'];
$payroll_date = $_POST['payroll_date'];
if ($reportType == 'Correction') {
    $sql = "SELECT * FROM tbl_correction_details as a
			INNER JOIN ect_clean_list as b on b.id = a.bene_id
			WHERE a.status='Correction' and b.payroll_no='$payroll_no' and payroll_date='$payroll_date'";
			
			
	$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $output = '';
	$output .= 'No.,Last Name,First Name,Middle Name,Extension Name,Birth Date,Birth Month,Birth Year,Province,City/Municipality,Barangay,Purok' . "\n";

    while ($row = $result->fetch_assoc()) {
        $output .= $row['bene_id'] . ',' . $row['new_lname'] .',' . $row['new_fname'] .',' . $row['new_mname'] .',' . $row['new_ename'] .',' . $row['new_dob'] .',' . $row['province'] .',' . $row['city_muni'] .',' . $row['barangay'] .',' . $row['purok'] . "\n";
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report_' . date('m-d-Y_H-i-s') . '.csv"');

    echo $output;
    exit;
} else {
    echo 'No records found.';
}
		
} elseif ($reportType == 'Replacement') {
    $sql = "SELECT * FROM tbl_replacement as a
			INNER JOIN ect_clean_list as b on b.id = a.bene_id
			WHERE b.payroll_no='$payroll_no'";
	$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $output = '';
    $output .= 'No.,Last Name,First Name,Middle Name,Extension Name,Birth Date,Birth Month,Birth Year,Province,City/Municipality,Barangay,Purok' . "\n";

    while ($row = $result->fetch_assoc()) {
        $output .= $row['bene_id'] . ',' . $row['lname'] .',' . $row['fname'] .',' . $row['mname'] .',' .  $row['ename'] .',' . $row['birth_day'] .',' . $row['birth_month'] .',' . $row['birth_year'] .',' . $row['province'] .',' . $row['city_municipality'] .',' . $row['barangay'] .',' . $row['purok'] . "\n";
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report_' . date('m-d-Y_H-i-s') . '.csv"');

    echo $output;
    exit;
} else {
    echo 'No records found.';
}
} elseif ($reportType == 'Claimed') {
	$ctr=1;
    $sql = "SELECT * FROM ect_clean_list WHERE payroll_no = '$payroll_no' AND status ='Claimed' ORDER BY payroll_id ASC";
	$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $output = '';
    $output .= 'No.,Last Name,First Name,Middle Name,Extension Name,Birth Date,Birth Month,Birth Year,Province,City/Municipality,Barangay,Purok' . "\n";

    while ($row = $result->fetch_assoc()) {
        $output .= $ctr . ',' . $row['last_name'] .',' . $row['first_name'] .',' . $row['middle_name'] .',' . $row['extension'] .',' .$row['birth_day'] .',' . $row['birth_month'] .',' . $row['birth_year'] .',' .$row['province'] .',' . $row['city_municipality'] .',' . $row['barangay'] .',' . $row['purok'] . "\n";
    $ctr++;
	}

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report_' . date('m-d-Y_H-i-s') . '.csv"');

    echo $output;
    exit;
} else {
    echo 'No records found.';
}
} elseif ($reportType == 'Validated') {
    $sql = "SELECT * FROM ect_clean_list 
			WHERE status='Validated' and payroll_no='$payroll_no' ORDER BY payroll_id ASC";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$output = '';
		$output .= 'No.,Last Name,First Name,Middle Name,Extension Name,Birth Date,Birth Month,Birth Year,Province,City/Municipality,Barangay,Purok' . "\n";

		while ($row = $result->fetch_assoc()) {
			$output .= $row['id'] . ',' . $row['last_name'] .',' . $row['first_name'] .',' . $row['middle_name'] .',' . $row['extension'] .',' . $row['birth_day'] .',' . $row['birth_month'] .',' . $row['birth_year'] .',' . $row['province'] .',' . $row['city_municipality'] .',' . $row['barangay'] .',' . $row['purok'] . "\n";
		}

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="report_' . date('m-d-Y_H-i-s') . '.csv"');

		echo $output;
		exit;
	} else {
		echo 'No records found.';
	}
} elseif ($reportType == 'No Show') {
    $sql = "SELECT * FROM ect_clean_list 
			WHERE status is null and payroll_no='$payroll_no' ORDER BY payroll_id ASC";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$output = '';
		$output .= 'No.,Last Name,First Name,Middle Name,Extension Name,Birth Date,Birth Month,Birth Year,Province,City/Municipality,Barangay,Purok' . "\n";

		while ($row = $result->fetch_assoc()) {
			$output .= $row['id'] . ',' . $row['last_name'] .',' . $row['first_name'] .',' . $row['middle_name'] .',' . $row['extension'] .',' . $row['birth_day'] .',' . $row['birth_month'] .',' . $row['birth_year'] .',' . $row['province'] .',' . $row['city_municipality'] .',' . $row['barangay'] .',' . $row['purok'] . "\n";
		}

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="report_' . date('m-d-Y_H-i-s') . '.csv"');

		echo $output;
		exit;
	} else {
		echo 'No records found.';
	}
} elseif ($reportType == 'TagClaimed') {
    $sql = "UPDATE ect_clean_list set status='Claimed' where payroll_no='$payroll_no' and status='Validated'";
	$result = $conn->query($sql);

}
?>