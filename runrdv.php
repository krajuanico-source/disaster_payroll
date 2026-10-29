<?php
include "dbconnect.php";  // Database connection
// error_reporting(0);
date_default_timezone_set('Asia/Manila');
$datetoday = date("Y-m-d");
$payroll_no = "";
$team_leader = "";

$sql = "SELECT * FROM tbl_correction_details WHERE status IS NULL";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    echo "<script>
          alert('No records found.');
          window.open('validation.php', '_self');
          </script>";
    exit;
}

// Strip commas/newlines so values cannot break the CSV columns
$clean = function ($val) {
    return str_replace([",", "\n", "\r"], " ", (string)($val ?? ''));
};

$csv = '';

// UTF-8 BOM for Excel
$csv .= "\xEF\xBB\xBF";

$csv .= 'S/N,LAST NAME,FIRST NAME,MIDDLE NAME,EXTENSION NAME,BIRTH DAY,BIRTH MONTH,BIRTH YEAR,SEX,GCASH,PCN,PROVINCE,CITY/MUNICIPALITY,BARANGAY,PUROK,AMOUNT,PAYROLL NO,TEAM LEADER' . "\n";

while ($row = $result->fetch_assoc()) {
    // Works whether the column is named sex or bene_sex
    $sex = $row['sex'] ?? $row['bene_sex'] ?? '';

    $csv .= $clean($row['bene_id']) . ','
          . $clean($row['new_lname']) . ','
          . $clean($row['new_fname']) . ','
          . $clean($row['new_mname']) . ','
          . $clean($row['new_ename']) . ','
          . $clean($row['birthday']) . ','
          . $clean($row['birthmonth']) . ','
          . $clean($row['birthyear']) . ','
          . $clean($sex) . ','
          . $clean($row['gcash'] ?? '') . ','
          . $clean($row['pcn'] ?? '') . ','
          . $clean($row['province']) . ','
          . $clean($row['city']) . ','
          . $clean($row['barangay']) . ','
          . $clean($row['purok']) . ','
          . $clean($row['amount']) . ','
          . $clean($row['payroll_no']) . ','
          . $clean($row['team_leader']) . "\n";

    // NOTE: only the last row's values are kept (same behavior as before)
    $payroll_no = $row['payroll_no'];
    $team_leader = $row['team_leader'];
}

// Directory and filename configuration
$directory = "C:/laragon/www/disaster_payroll/forrdv/";
$timestamp = date("Ymd_His") . "_" . sprintf("%03d", round(microtime(true) * 1000) % 1000);
$filename = "rdv_" . $timestamp . ".csv";
$filepath = $directory . $filename;

// Write CSV to the file; stop here if it fails
if (file_put_contents($filepath, $csv) === false) {
    echo "<script>
          alert('Failed to save the file.');
          window.open('validation.php', '_self');
          </script>";
    exit;
}

$conn->query("UPDATE tbl_correction_details SET status='Correction' WHERE status IS NULL");

// Run Python script
$pythonPath = "C:\\Users\\kajuanico\\AppData\\Local\\Microsoft\\WindowsApps\\python.exe";
$scriptPath = "C:\\laragon\\www\\disaster_payroll\\fuzzy_match.py";

$command = escapeshellcmd("$pythonPath $scriptPath $filename") . " 2>&1"; // Redirect stderr to stdout
$pyOutput = [];
$return_var = 0;

exec($command, $pyOutput, $return_var);

// Stamp the newly inserted clean-list rows
$stmt = $conn->prepare(
    "UPDATE ect_clean_list
     SET payroll_no = ?,
         team_leader = ?,
         payroll_date = ?,
         date_processed = ?
     WHERE payroll_no = 0"
);
$stmt->bind_param("ssss", $payroll_no, $team_leader, $datetoday, $datetoday);
$stmt->execute();
$stmt->close();

// Show a failure message if the Python script did not exit cleanly
if ($return_var !== 0) {
    $msg = addslashes(implode(' ', $pyOutput));
    echo "<script>
          alert('RDV failed: $msg');
          window.open('validation.php', '_self');
          </script>";
    exit;
}

echo "<script>
      alert('RDV Done');
      window.open('validation.php', '_self');
      </script>";