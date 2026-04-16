<?php
include "dbconnect.php"; // database connection

if (isset($_POST['payroll_no'])) {
    $payroll_no = $_POST['payroll_no'];

    function getCount($conn, $payroll_no, $status = null) {
        if ($status === null) {
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM ect_clean_list WHERE payroll_no = ?");
            $stmt->bind_param("s", $payroll_no);
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM ect_clean_list WHERE status = ? AND payroll_no = ?");
            $stmt->bind_param("ss", $status, $payroll_no);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }

    $toBeServed  = getCount($conn, $payroll_no);
    $validated   = getCount($conn, $payroll_no, 'validated');
    $claimed     = getCount($conn, $payroll_no, 'claimed');
    $replacement = getCount($conn, $payroll_no, 'replacement');
    $correction  = getCount($conn, $payroll_no, 'correction');
    $disqualified  = getCount($conn, $payroll_no, 'disqualified');

    echo json_encode([
        'success' => true,
        'toBeServed' => $toBeServed,
        'validated' => $validated,
        'claimed' => $claimed,
        'replacement' => $replacement,
        'correction' => $correction,
        'disqualified' => $disqualified
    ]);
} else {
    echo json_encode(['success' => false]);
}
?>
