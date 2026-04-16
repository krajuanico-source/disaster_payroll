<?php
header("Content-Type: application/json");
include "../dbconnect.php";

$sql = "SELECT a.id, lname, mname, fname, dob, a.city_muni, amount, sdo_id, fs_name, a.payroll_no, status
        FROM tbl_bene as a
        INNER JOIN tbl_payroll_list as b ON a.payroll_no = b.payroll_no
        INNER JOIN lib_fund_source as c ON b.fund_source = c.id
        INNER JOIN lib_sdo as d ON b.sdo = d.sdo_no
        INNER JOIN lib_program as e ON b.program = e.id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $payroll_data = [];

    while ($row = $result->fetch_assoc()) {
        $payroll_data[] = [
            'id' => $row['id'],
            'lname' => $row['lname'],
            'mname' => $row['mname'],
            'fname' => $row['fname'],
            'dob' => $row['dob'],
            'city_muni' => $row['city_muni'],
            'amount' => $row['amount'],
            'sdo_id' => $row['sdo_id'],
            'fs_name' => $row['fs_name'],
            'payroll_no' => $row['payroll_no'],
            'status' => $row['status'],
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $payroll_data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No records found']);
}
?>
