<?php
require __DIR__ . '/vendor/autoload.php'; // Load PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include "dbconnect.php"; // Database connection
date_default_timezone_set('Asia/Manila');
$datetoday = date("Y-m-d");
mysqli_set_charset($conn, "utf8mb4");

// Get team leader and payroll number from GET parameters
$teamLeader = isset($_GET['teamLeader']) ? $_GET['teamLeader'] : 'Unknown';
$payrollNo = isset($_GET['payrollNo']) ? $_GET['payrollNo'] : 'Unknown';
$payroll_date = isset($_GET['payroll_date']) ? $_GET['payroll_date'] : 'Unknown';

// Update export date for the given payroll
$sql_update = "UPDATE ect_clean_list SET dateExported='$datetoday' WHERE payroll_no='$payrollNo' ";
if (!$conn->query($sql_update)) {
    die("Error updating ect_clean_list: " . $conn->error);
}

// Fetch one row to get the province name
$sampleRow = $conn->query("SELECT province FROM ect_clean_list WHERE payroll_no = '$payrollNo' and date(date_processed)='$payroll_date' LIMIT 1")->fetch_assoc();
$province = $sampleRow['province'] ?? 'Unknown';

/**
 * Get abbreviation of a province
 */
function getProvinceAbbreviation($province) {
    $map = [
        'DAVAO CITY' => 'DC',
        'DAVAO DEL SUR' => 'DDS',
        'DAVAO DEL NORTE' => 'DDN',
        'DAVAO DE ORO' => 'DDO',
        'DAVAO ORIENTAL' => 'DO',
        'DAVAO OCCIDENTAL' => 'DOCC',
        // Add more province-to-abbreviation mappings here
    ];

    $normalized = strtoupper(trim($province)); // Normalize input
    return $map[$normalized] ?? strtoupper(substr($normalized, 0, 3));
}


// Generate filename
$abbr = getProvinceAbbreviation($province);
$timestamp = date("Ymd_His"); // e.g., 20250604_152500
$filename = $abbr . '_' . $timestamp . '.xlsx';

// Create spreadsheet
$spreadsheet = new Spreadsheet();

// Define headers
$headers = [
    'PAYROLL ID','LAST NAME', 'FIRST NAME', 'MIDDLE NAME', 'EXTENSION NAME',
    'BIRTH DAY', 'BIRTH MONTH', 'BIRTH YEAR','PUROK', 'BARANGAY','CITY/MUNICIPALITY', 'PROVINCE',
    'DATE LAST SERVED', 'LAST SERVED LOCATION', 'PROGRAM','PARTNERS','SDO INCHARGE','OTHER REMARKS','CONTROL NUMBER', 'TEAM LEADER'
];

// Function to populate data per sheet
function addDataToSheet($spreadsheet, $sheetIndex, $sheetName, $sql, $conn, $headers) {
    $sheet = ($sheetIndex == 0) ? $spreadsheet->setActiveSheetIndex($sheetIndex) : $spreadsheet->createSheet();
    $sheet->setTitle($sheetName);
    $spreadsheet->setActiveSheetIndex($sheetIndex);

    if ($sheetName === "Disqualified Beneficiaries") {
    $headers[] = 'REASON FOR DISQUALIFICATION';
}

    $sheet->fromArray([$headers], NULL, 'A1');

    $result = $conn->query($sql);
    if (!$result) {
        die("Error in query for $sheetName: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $formattedData = [];
       foreach ($data as $row) {
            $formattedRow = [
                $row['payroll_id'] ?? '',
                strtoupper($row['last_name'] ?? ''),
                strtoupper($row['first_name'] ?? ''),
                strtoupper($row['middle_name'] ?? ''),
                strtoupper($row['extension_name'] ?? ''),
                $row['birth_day'] ?? '',
                $row['birth_month'] ?? '',
                $row['birth_year'] ?? '',
                $row['purok'] ?? '',
                $row['barangay'] ?? '',
                $row['city_municipality'] ?? '',
                $row['province'] ?? '',
                !empty($row['date_validated']) ? date('m-d-Y', strtotime($row['date_validated'])) : '',
                (isset($row['barangay']) && isset($row['city_municipality']) ? $row['barangay'] . ', ' . $row['city_municipality'] : ''),
                'ECT',
                strtoupper($row['fs_name'] ?? ''),
                $row['sdo'] ?? '',
                '',
                $row['control_number'] ?? '',
                $row['team_leader'] ?? ''
            ];

    // ➕ Add remarks if Disqualified sheet
    if ($sheetName === "Disqualified Beneficiaries") {
        $formattedRow[] = $row['remarks'] ?? '';
    }

    $formattedData[] = $formattedRow;
}

 /*       foreach ($data as $row) {
            $formattedData[] = [
                $row['payroll_id'] ?? '',$row['last_name'] ?? '', $row['first_name'] ?? '',
                $row['middle_name'] ?? '', $row['extension_name'] ?? '',
                $row['birth_day'] ?? '', $row['birth_month'] ?? '',
                $row['birth_year'] ?? '', $row['purok'] ?? '',$row['barangay'] ?? '',$row['city_municipality'] ?? '',
                $row['province'] ?? '', $row['date_validated'] ?? '',
                $row['barangay'].', '.$row['city_municipality'] ?? '', 'ECT', strtoupper($row['fs_name'] ?? ''), $row['sdo'] ?? '','', $row['payroll_no'] ?? '',
                $row['team_leader'] ?? ''
            ];
        }*/
        $sheet->fromArray($formattedData, NULL, 'A2');
    }
}

// Define sheets and SQL queries
$queries = [
    ["Claimed Beneficiaries", "SELECT payroll_id,last_name, first_name, middle_name, extension_name, birth_day, birth_month, birth_year,
 purok,barangay,city_municipality, e.province, payroll_date, e.province, date_validated,fs_name,sdo, control_number, e.team_leader 
 FROM ect_clean_list AS e 
INNER JOIN tbl_payroll_list AS p ON e.payroll_no = p.payroll_no
INNER JOIN lib_fund_source AS u ON u.id = p.fund_source
WHERE status = 'Claimed' AND  e.payroll_no='$payrollNo' and date(date_processed)='$payroll_date' order by payroll_id asc"],
    ["Corrections", "SELECT controlNo AS control_number, new_lname AS last_name, new_fname AS first_name, new_mname AS middle_name, new_ename AS extension_name, birthday AS birth_day, birthmonth AS birth_month, birthyear AS birth_year, purok, barangay, city AS city_municipality, province, team_leader FROM tbl_correction_details WHERE status = 'Correction' and payroll_no='$payrollNo' and date(date_added)='$payroll_date'"],
    ["Disqualified Beneficiaries", "SELECT control_number, last_name, first_name, middle_name, extension_name, birth_day, birth_month, birth_year, purok, barangay, city_municipality, province, team_leader, remarks FROM ect_clean_list WHERE status IN ('Disqualified') AND payroll_no='$payrollNo' and date(date_processed)='$payroll_date'"],
    ["No Show Beneficiaries", "SELECT last_name, first_name, middle_name, extension_name, payroll_date, birth_day, birth_month, birth_year, purok, barangay, city_municipality, province, control_number, team_leader FROM ect_clean_list WHERE (status IS NULL OR status = 'Validated')  and payroll_no='$payrollNo' and date(date_processed)='$payroll_date' "],
    ["Replacement", "SELECT payroll_id, last_name, first_name, middle_name, extension_name, birth_day, birth_month, birth_year, purok, barangay, city_municipality, province, date_validated, control_number, team_leader FROM ect_clean_list WHERE status = 'Replacement' AND payroll_no='$payrollNo' AND date(date_processed)='$payroll_date'"]
];

// Add sheets
foreach ($queries as $index => [$sheetName, $sql]) {
    addDataToSheet($spreadsheet, $index, $sheetName, $sql, $conn, $headers);
}

// Set the first sheet as active
$spreadsheet->setActiveSheetIndex(0);

// Output the Excel file with proper headers
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
