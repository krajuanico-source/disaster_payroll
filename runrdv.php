<?php
include "dbconnect.php";  // Database connection
// error_reporting(0);
date_default_timezone_set('Asia/Manila');
$datetoday = date("Y-m-d");
$payroll_no = "";
$team_leader = "";
$payroll_date = "";
$sql = "SELECT * FROM tbl_correction_details where status is null";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $output = '';

    // ✅ UTF-8 BOM for Excel
    $output .= "\xEF\xBB\xBF";

    $output .= 'S/N,LAST NAME,FIRST NAME,MIDDLE NAME,EXTENSION NAME,BIRTH DAY,BIRTH MONTH,BIRTH YEAR,PROVINCE,CITY/MUNICIPALITY,BARANGAY,PUROK,AMOUNT,PAYROLL NO,TEAM LEADER' . "\n";

    while ($row = $result->fetch_assoc()) {
        // ✅ Replace commas in data values to avoid breaking CSV
        $clean = function ($val) {
            return str_replace([",", "\n", "\r"], [" ", " ", " "], $val ?? '');
        };
        $output .= $row['bene_id'] . ',' . $row['new_lname'] . ',' . $row['new_fname'] . ',' . $row['new_mname'] . ',' . $row['new_ename'] . ',' . $row['birthday'] . ',' . $row['birthmonth'] . ',' . $row['birthyear'] . ',' . $row['province'] . ',' . $row['city'] . ',' . $row['barangay'] . ',' . $row['purok'] .  ',' . $row['amount'] .  ',' .$row['payroll_no'] .  ',' . $row['team_leader'] . "\n";

        $payroll_no = $row['payroll_no'];
        $team_leader = $row['team_leader'];
    }

    // Directory and filename configuration
    $directory = "C:/laragon/www/disaster_payroll/forrdv/"; // Replace with your desired directory path
    $timestamp = date("Ymd_His") . "_" . sprintf("%03d", round(microtime(true) * 1000) % 1000);
    $filename = "rdv_" . $timestamp . ".csv";
    $filepath = $directory . $filename;

    // Write CSV output to the file
    if (file_put_contents($filepath, $output)) {
        $sqlq = "Update tbl_correction_details set status='Correction' where status is null";
        $resultq = $conn->query($sqlq);
    } else {
        echo "Failed to save the file.";
    }
} else {
    echo 'No records found.';
}

// Run Python script
$pythonPath = "C:\\Users\\kajuanico\\AppData\\Local\\Microsoft\\WindowsApps\\python.exe";
$scriptPath = "C:\\laragon\\www\\disaster_payroll\\fuzzy_match.py";

$command = escapeshellcmd("$pythonPath $scriptPath $filename") . " 2>&1"; // Redirect stderr to stdout
$output = [];
$return_var = 0;

exec($command, $output, $return_var);

$sql1 = "UPDATE ect_clean_list 
        SET 
          payroll_no = '$payroll_no', 
          team_leader = '$team_leader', 
          payroll_date = '$datetoday', 
          date_processed = '$datetoday' 
        WHERE payroll_no = 0";

$result1 = $conn->query($sql1);

echo "<script>
	  alert('RDV Done');
	  window.open('validation.php', '_self');
	  </script>";
