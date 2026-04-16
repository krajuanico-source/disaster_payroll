<?php
include "php/db.php";
include "dbconnect.php";
 // Include your database connection script

$payroll_no = $_GET['payrollNo']; // Get payroll_no from AJAX request
echo $payroll_no;
// Query to fetch data
$sql = "SELECT * FROM tbl_bene WHERE payroll_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $payroll_no);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    // Fetch duplicate status
    $sql_duplicate = "SELECT COUNT(*) as claimed_count 
                      FROM tbl_bene 
                      WHERE fname = ? AND mname = ? AND lname = ? AND dob = ? AND status = 'Validated'";
    $stmt_duplicate = $conn->prepare($sql_duplicate);
    $stmt_duplicate->bind_param('ssss', $row['fname'], $row['mname'], $row['lname'], $row['dob']);
    $stmt_duplicate->execute();
    $duplicate_result = $stmt_duplicate->get_result();
    $duplicate = $duplicate_result->fetch_assoc();

    $row['remarks'] = ($duplicate['claimed_count'] > 0) ? 'Duplicate with claimed status' : 'No remarks';
    $data[] = $row; // Add to array
}

echo json_encode($data); // Return data as JSON
?>
