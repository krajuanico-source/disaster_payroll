<?php
include "dbconnect.php";

$payroll_no = $_POST['payroll_no'];
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];

$where = "WHERE payroll_no='$payroll_no' AND status IN ('Validated')";

if (!empty($search)) {
    $where .= " AND (CONCAT(last_name, ', ', first_name, ' ', middle_name, ' ', extension_name) LIKE '%$search%' 
                OR barangay LIKE '%$search%')";
}

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM ect_clean_list WHERE payroll_no='$payroll_no' AND status IN ('Validated')");
$totalData = mysqli_fetch_assoc($totalQuery);
$totalRecords = $totalData['total'];

$filteredQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM ect_clean_list $where");
$filteredData = mysqli_fetch_assoc($filteredQuery);
$filteredRecords = $filteredData['total'];

$sql = "SELECT * FROM ect_clean_list $where ORDER BY status DESC LIMIT $start, $length";
$result = mysqli_query($conn, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $fullName = $row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['extension_name'];
    $dob = $row['birth_month'] . '-' . $row['birth_day'] . '-' . $row['birth_year'];
    $barangay = $row['barangay'] . ', ' . $row['city_municipality'];
    $status = $row['status'];

    $btnClass = ($status === 'Claimed') ? 'view-payrollunclaim' : 'view-payroll';

    $actions = "<button class='btn btn-primary $btnClass' 
                data-bene-id='{$row['id']}'
                data-bene-payroll_id='{$row['payroll_id']}'
                data-full-name='" . strtoupper($fullName) . "'
                data-status='{$status}'>
                " . (($status === 'Claimed') ? "Unclaim" : "Claim") . "</button>";

    $data[] = [
        "payroll_id" => $row['payroll_id'],
        "full_name" => strtoupper($fullName),
        "dob" => $dob,
        "barangay" => $barangay,
        "status_display" => $status,
        "actions" => $actions
    ];
}

echo json_encode([
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
]);
?>
