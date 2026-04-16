<?php
session_start();
include 'dbconnect.php';  // Include your database connection file
$emm = $_SESSION['userid'];
date_default_timezone_set('Asia/Manila');
$date_val = date("Y-m-d H:i:s");

if (
    isset($_POST['bene_id']) && isset($_POST['fname']) && isset($_POST['mname']) &&
    isset($_POST['lname']) && isset($_POST['dob']) && isset($_POST['status']) && isset($_POST['sex'])
) {
    $bene_id = strtoupper($_POST['bene_id']);
    $fname = strtoupper($_POST['fname']);
    $mname = strtoupper($_POST['mname']);
    $lname = strtoupper($_POST['lname']);
    $dob = $_POST['dob'];
    $sex = $_POST['sex'];
    $reason = $_POST['reason'] ?? '';
    $status = $_POST['status'];
    $sdo_limit = 1000000;

    // Step 1: Fetch beneficiary details
    $sql_bene = "SELECT city_municipality, payroll_date, team_leader, sdo_id, payroll_no FROM ect_clean_list WHERE id = ?";
    $stmt_bene = $conn->prepare($sql_bene);
    $stmt_bene->bind_param('i', $bene_id);
    $stmt_bene->execute();
    $result_bene = $stmt_bene->get_result();

    if ($result_bene->num_rows > 0) {
        $payroll = $result_bene->fetch_assoc();
        $city_municipality = $payroll['city_municipality'];
        $payroll_date = $payroll['payroll_date'];
        $team_leader = $payroll['team_leader'];
        $payroll_no = $payroll['payroll_no'];
        $sdo_id = $payroll['sdo_id'];

        $payroll_id = 1;

        // Begin transaction
        $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        try {
            if ($status != 'Disqualified' && $status != 'Replacement') {
                // Lock payroll_no for update
                $sql_max = "SELECT MAX(payroll_no) AS max_payroll_no FROM ect_clean_list FOR UPDATE";
                $result_max = $conn->query($sql_max);
                $max_payroll_no = ($row_max = $result_max->fetch_assoc()) ? $row_max['max_payroll_no'] ?? 0 : 0;

                // Use MAX(payroll_id) instead of COUNT to avoid duplicates
                $sql_max_id = "SELECT MAX(payroll_id) AS max_id FROM ect_clean_list WHERE payroll_no = ?";
                $stmt_max = $conn->prepare($sql_max_id);
                $stmt_max->bind_param('i', $payroll_no);
                $stmt_max->execute();
                $result_max_id = $stmt_max->get_result();
                $row_max_id = $result_max_id->fetch_assoc();
                $max_id = $row_max_id['max_id'] ?? 0;

                if ($max_id >= $sdo_limit || empty($payroll_no)) {
                    $payroll_no = $max_payroll_no + 1;
                    $payroll_id = 1;

                    $insertPayroll = "INSERT INTO tbl_payroll_list (payroll_no, date_created, created_by) VALUES (?, NOW(), ?)";
                    $stmtInsert = $conn->prepare($insertPayroll);
                    $stmtInsert->bind_param('is', $payroll_no, $emm);
                    $stmtInsert->execute();
                } else {
                    $payroll_id = $max_id + 1;
                }

                $sql_update = "UPDATE ect_clean_list SET status = ?, payroll_id = ?, payroll_no = ?, validated_by = ?, date_validated = ?, bene_sex = ?, sdo_id = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param('siissssi', $status, $payroll_id, $payroll_no, $emm, $date_val, $sex, $sdo_id, $bene_id);
                $stmt_update->execute();
            } else {
                $payroll_id = null;
                $payroll_no = null;
                $sql_update = "UPDATE ect_clean_list SET status = ?, payroll_id = ?, validated_by = ?, date_validated = ?, bene_sex = ?, sdo_id = ?, remarks = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param('sisssssi', $status, $payroll_id, $emm, $date_val, $sex, $sdo_id, $reason, $bene_id);
                $stmt_update->execute();
            }

            // Audit trail
            $action = 'Updated Client status to ' . $status . ' with ID no. ' . $bene_id;
            $sql1 = "SELECT MAX(id) AS ctr_audit FROM tbl_audit_log";
            $result1 = $conn->query($sql1);
            $row1 = $result1->fetch_assoc();
            $ctr_audit = $row1['ctr_audit'] + 1;
            $save_to_dtr1 = "INSERT INTO tbl_audit_log (description, updated_by, date_updated, audit_no) VALUES (?, ?, ?, ?)";
            $stmt_audit = $conn->prepare($save_to_dtr1);
            $stmt_audit->bind_param('sssi', $action, $emm, $date_val, $ctr_audit);
            $stmt_audit->execute();

            $conn->commit();
            echo $payroll_id;
        } catch (Exception $e) {
            $conn->rollback();
            echo 'Transaction failed: ' . $e->getMessage();
        }
    } else {
        echo 'Payroll number not found for the given beneficiary ID';
    }

    $conn->close();
} else {
    echo 'Invalid request';
}
?>
