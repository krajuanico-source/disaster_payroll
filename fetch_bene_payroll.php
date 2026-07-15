<?php
include("dbconnect.php");
error_reporting(0);
$payroll_no = $_POST['payroll_no'];
$search = trim($_POST['search']['value'] ?? '');
$start = intval($_POST['start']);
$length = intval($_POST['length']);

$where = "WHERE payroll_no = ?";
$params = [$payroll_no];
$paramTypes = 'i';

if ($search !== '') {
    $searchWords = preg_split('/\s+/', $search);
    $conditions = [];
    foreach ($searchWords as $word) {
        $conditions[] = "(CONCAT_WS(' ', first_name, middle_name, last_name, IFNULL(extension_name,'')) LIKE ? OR barangay LIKE ?)";
        $params[] = "%$word%";
        $params[] = "%$word%";
        $paramTypes .= "ss";
    }
    $where .= " AND " . implode(" AND ", $conditions);
}

// Total count (unfiltered by search, only by payroll_no)
$totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM ect_clean_list WHERE payroll_no = ?");
$totalStmt->bind_param('i', $payroll_no);
$totalStmt->execute();
$totalRecords = $totalStmt->get_result()->fetch_assoc()['total'];

// Filtered count
$countSql = "SELECT COUNT(*) as total FROM ect_clean_list $where";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param($paramTypes, ...$params);
$countStmt->execute();
$filteredRecords = $countStmt->get_result()->fetch_assoc()['total'];

// Data query
$sql = "SELECT amount, CONCAT_WS(' ', first_name, middle_name, last_name, IFNULL(extension_name,'')) AS fullname,
        CONCAT(birth_month, '-', birth_day, '-', birth_year) AS dob, status, payroll_id, validated_by, id, barangay 
        FROM ect_clean_list $where
        ORDER BY id ASC
        LIMIT ?, ?";
$dataParamTypes = $paramTypes . "ii";
$dataParams = array_merge($params, [$start, $length]);

$dataStmt = $conn->prepare($sql);
$dataStmt->bind_param($dataParamTypes, ...$dataParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

$data = [];
$ctr = $start + 1;

while ($row = $result->fetch_assoc()) {
    $statusDisplay = ($row['status'] == 'Validated') 
        ? "<span style='color: green'>PAYROLL ID: {$row['payroll_id']}</span>" 
        : "<span style='color: red'>".strtoupper($row['status'])."</span>";

    $data[] = [
        $ctr++,
        strtoupper(str_replace('Ã', 'Ñ', $row['fullname'])),
        $row['dob'],
        $row['barangay'],
        number_format($row['amount'], 2),
        $statusDisplay,
        $row['validated_by'],
        "<button class='btn view-payroll' data-payroll-no='{$row['id']}'>View</button>"
    ];
}

echo json_encode([
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
]);
?>