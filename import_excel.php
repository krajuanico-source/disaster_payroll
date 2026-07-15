<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$emm = $_SESSION['userid'] ?? '';
date_default_timezone_set('Asia/Manila');
$date_reg = date("Y-m-d");

include "dbconnect.php"; 

define('EXPECTED_HEADERS', [
    'NO.', 'LAST NAME', 'FIRST NAME', 'MIDDLE NAME', 'EXTENSION NAME',
    'BIRTH DAY', 'BIRTH MONTH', 'BIRTH YEAR', 'SEX', 'GCASH', 'PCN',
    'BARANGAY', 'CITY/MUNICIPALITY', 'PROVINCE', 'AMOUNT', 'CONTROL NUMBER'
]);

$payroll_date    = $_POST['payroll_date'] ?? '';
$payroll_staff   = $_POST['payroll_staff'] ?? '';
$project_name    = $_POST['project_name'] ?? '';
$program_title   = $_POST['program_title'] ?? '';
$sdo_num         = $_POST['sdo_name'] ?? ($_POST['sdo'] ?? '');
$agency          = $_POST['fund_source'] ?? '';
$team_leader     = $_POST['team_leader'] ?? $emm;

$payrollNo = "";
$payrollNumbersMap = [];

$payrollNoTl = "SELECT payroll_no FROM tbl_payroll_list WHERE team_leader = ? AND (payroll_status IS NULL OR payroll_status = 'Active')";
$stmtTl = $conn->prepare($payrollNoTl);
$stmtTl->bind_param("s", $team_leader);
$stmtTl->execute();
$resultTl = $stmtTl->get_result();

if ($resultTl->num_rows > 0) {
    while ($rowTl = $resultTl->fetch_assoc()) {
        $payrollNo = $rowTl['payroll_no'];
    }
} else {
    $createPayroll = "SELECT MAX(payroll_no)+1 as payrollNo FROM tbl_payroll_list";
    $resultPayroll = $conn->query($createPayroll);
    $rowPayroll = $resultPayroll->fetch_assoc();
    $payrollNo = $rowPayroll['payrollNo'] ?? 1;

    $savePayroll = "INSERT INTO tbl_payroll_list (payroll_no, team_leader, sdo) VALUES (?, ?, ?)";
    $stmtSave = $conn->prepare($savePayroll);
    $stmtSave->bind_param("iss", $payrollNo, $team_leader, $sdo_num);
    $stmtSave->execute();
}

if (isset($_POST['submit'])) {
    $file = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'];

    $checkFileQuery = "SELECT * FROM tbl_import_log WHERE file_name = ?";
    $stmtCheck = $conn->prepare($checkFileQuery);
    $stmtCheck->bind_param("s", $file_name);
    $stmtCheck->execute();
    $checkFileResult = $stmtCheck->get_result();

    if ($checkFileResult->num_rows > 0) {
        echo "<script>alert('Error: This file has already been uploaded.'); window.location.href='export_excel.php';</script>";
        exit;
    } else {
        if (($handle = fopen($file, "r")) !== FALSE) {

            $header = fgetcsv($handle);
            if ($header === FALSE) {
                fclose($handle);
                echo "<script>alert('Error: The uploaded file is empty.'); window.location.href='export_excel.php';</script>";
                exit;
            }

            $normalizedHeader = array_map(function ($h) {
                return strtoupper(trim($h));
            }, $header);

            $normalizedHeader = array_slice($normalizedHeader, 0, count(EXPECTED_HEADERS));

            if ($normalizedHeader !== EXPECTED_HEADERS) {
                fclose($handle);
                $expectedStr = implode(', ', EXPECTED_HEADERS);
                $gotStr = implode(', ', $header);
                echo "<script>alert('Invalid file format.\\n\\nExpected columns:\\n" . addslashes($expectedStr) . "\\n\\nFound:\\n" . addslashes($gotStr) . "'); window.location.href='export_excel.php';</script>";
                exit;
            }

            $insertSql = "INSERT INTO ect_clean_list
                (control_number, first_name, last_name, middle_name, extension_name,
                 birth_day, birth_month, birth_year, sex, gcash, pcn, barangay,
                 city_municipality, province, team_leader, payroll_date, sdo_id,
                 payroll_no, date_processed, amount)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($insertSql);
            $importedCount = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rowNo    = trim($data[0] ?? ''); 
                $lname    = trim(iconv('ISO-8859-1', 'UTF-8', $data[1] ?? ''));
                $fname    = trim(iconv('ISO-8859-1', 'UTF-8', $data[2] ?? ''));
                $mname    = trim(iconv('ISO-8859-1', 'UTF-8', $data[3] ?? ''));
                $ename    = trim(iconv('ISO-8859-1', 'UTF-8', $data[4] ?? ''));
                $dob      = trim(htmlentities($data[5] ?? ''));
                $dom      = trim(htmlentities($data[6] ?? ''));
                $doy      = trim(htmlentities($data[7] ?? ''));
                $sex      = trim(iconv('ISO-8859-1', 'UTF-8', $data[8] ?? ''));
                $gcash    = trim(iconv('ISO-8859-1', 'UTF-8', $data[9] ?? ''));
                $pcn      = trim(iconv('ISO-8859-1', 'UTF-8', $data[10] ?? ''));
                $brgy     = trim(iconv('ISO-8859-1', 'UTF-8', $data[11] ?? ''));
                $city     = trim($data[12] ?? '');
                $province = trim($data[13] ?? '');
                $amount   = (isset($data[14]) && is_numeric($data[14]) && $data[14] !== '')
                    ? (float)$data[14]
                    : 0;
                $no       = trim($data[15] ?? ''); 


                if ($no === '' && $fname === '' && $lname === '') {
                    continue;
                }
                if ($no === '' || $fname === '' || $lname === '') {
                    continue;
                }

                $combinationKey = $province . '-' . $city;

                if (!isset($payrollNumbersMap[$combinationKey])) {
                    $payrollNumbersMap[$combinationKey] = true;

                    $action = 'Imported a list of beneficiaries on ' . $payroll_date;
                    $sql1 = "SELECT MAX(id) AS ctr_audit FROM tbl_audit_log";
                    $result1 = $conn->query($sql1);
                    $row1 = $result1->fetch_assoc();
                    $ctr_audit = ($row1['ctr_audit'] ?? 0) + 1;

                    $save_to_dtr1 = "INSERT INTO tbl_audit_log (description, updated_by, date_updated, audit_no) VALUES (?, ?, ?, ?)";
                    $stmtAudit = $conn->prepare($save_to_dtr1);
                    $stmtAudit->bind_param("sssi", $action, $emm, $date_reg, $ctr_audit);
                    $stmtAudit->execute();
                }

                $stmtInsert->bind_param(
                    "sssssssssssssssssisd",
                    $no, $fname, $lname, $mname, $ename,
                    $dob, $dom, $doy, $sex, $gcash, $pcn, $brgy,
                    $city, $province, $team_leader, $payroll_date, $sdo_num,
                    $payrollNo, $date_reg, $amount
                );

                if (!$stmtInsert->execute()) {
                    echo "Error: " . $stmtInsert->error;
                } else {
                    $importedCount++;
                }
            }

            fclose($handle);

            $logFileQuery = "INSERT INTO tbl_import_log (file_name, import_date) VALUES (?, NOW())";
            $stmtLog = $conn->prepare($logFileQuery);
            $stmtLog->bind_param("s", $file_name);
            $stmtLog->execute();
        } else {
            echo "Error opening the CSV file.";
        }
    }

    $selectUser = "SELECT empid FROM lib_users WHERE empid = ?";
    $stmtUser = $conn->prepare($selectUser);
    $stmtUser->bind_param("s", $team_leader);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    $rowUser = $resultUser->fetch_assoc();
    $empid = $rowUser['empid'] ?? null;

    if ($empid === $team_leader) {
        $save_special = "UPDATE lib_users SET payroll_no = ?, date_assigned = ?, user_type = 'Team Leader', team_leader = ? WHERE empid = ?";
        $stmtSpecial = $conn->prepare($save_special);
        $stmtSpecial->bind_param("isss", $payrollNo, $date_reg, $empid, $empid);
        if ($stmtSpecial->execute()) {
            echo "Status updated successfully";
        } else {
            echo "Error: " . $stmtSpecial->error;
        }
    } else {
        $sql2 = "INSERT INTO lib_users (empid, user_type, user_status, team_leader, payroll_no, date_assigned)
                 VALUES (?, 'Team Leader', '1', ?, ?, ?)";
        $stmtNewUser = $conn->prepare($sql2);
        $stmtNewUser->bind_param("ssis", $team_leader, $team_leader, $payrollNo, $date_reg);
        if (!$stmtNewUser->execute()) {
            echo "Error: " . $stmtNewUser->error;
        }
    }

    $conn->close();
    echo "<script>alert('File imported successfully! " . (int)$importedCount . " record(s) imported.'); window.location.href='validation.php';</script>";
}
?>