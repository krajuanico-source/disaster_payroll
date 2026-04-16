<?php
include("dbconnect.php");
error_reporting(0);
$payroll_no = $_POST['payroll_no'];
$search = $_POST['search']['value'];
$start = $_POST['start'];
$length = $_POST['length'];

$where = " WHERE payroll_no = '$payroll_no' ";

if (!empty($search)) {
    $where .= " AND (CONCAT(first_name, ' ', middle_name, ' ', last_name, ' ', extension_name) LIKE '%$search%' 
                OR barangay LIKE '%$search%')";
}

// Get total records (without search filter)
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM ect_clean_list WHERE payroll_no = '$payroll_no'");
$totalData = mysqli_fetch_assoc($totalQuery);
$totalRecords = $totalData['total'];

// Get filtered records
$filteredQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM ect_clean_list $where");
$filteredData = mysqli_fetch_assoc($filteredQuery);
$filteredRecords = $filteredData['total'];

// Get data for display
$sql = "SELECT amount, CONCAT(first_name, ' ', middle_name, ' ', last_name, ' ', extension_name) AS fullname,
        CONCAT(birth_month, '-', birth_day, '-', birth_year) AS dob, status, payroll_id, validated_by, id, barangay 
        FROM ect_clean_list $where
        LIMIT $start, $length";

$result = mysqli_query($conn, $sql);

$data = [];
$ctr = $start + 1;

while ($row = mysqli_fetch_assoc($result)) {
    $statusDisplay = ($row['status'] == 'Validated') 
        ? "<span style='color: green'>PAYROLL ID: {$row['payroll_id']}</span>" 
        : "<span style='color: red'>".strtoupper($row['status'])."</span>";

    $data[] = [
        $ctr++,
        strtoupper(str_replace('Ã', 'Ñ', $row['fullname'])),
        $row['dob'],
        $row['barangay'],
        number_format($row['amount'], 2),
        $statusDisplay,
        $row['validated_by'],
        "<button class='btn btn-primary view-payroll' data-payroll-no='{$row['id']}'>View</button>"
    ];
}

echo json_encode([
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
]);
?>
