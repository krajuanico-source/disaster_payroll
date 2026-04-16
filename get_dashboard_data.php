<?php
session_start();
include "php/db.php";

$emm = $_SESSION['userid'];

// Ensure the user is logged in
if ($emm == '' || $emm == NULL) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Initialize response array
$response = [];

// Get gender distribution
$sql_sex_distribution = "
    SELECT 
        CASE 
            WHEN bene_sex IS NULL THEN 'Unspecified' 
            WHEN bene_sex = 'Male' THEN 'Male' 
            WHEN bene_sex = 'Female' THEN 'Female' 
        END AS gender, 
        COUNT(*) as count
    FROM tbl_bene WHERE status='Validated'
    GROUP BY gender";
$result_sex_distribution = $conn->query($sql_sex_distribution);

$response['gender_distribution'] = [];
while ($row = $result_sex_distribution->fetch_assoc()) {
    $response['gender_distribution'][] = $row;
}

// Get status distribution
$sql_status = "SELECT status, COUNT(*) as count FROM tbl_bene GROUP BY status";
$result_status = $conn->query($sql_status);

$response['status_distribution'] = [];
while ($row = $result_status->fetch_assoc()) {
    $response['status_distribution'][] = $row;
}

// Get age distribution
$sql_age_distribution = "
    SELECT 
        CASE 
            WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 18 AND 20 THEN '18-20'
            WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 21 AND 30 THEN '21-30'
            WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 31 AND 40 THEN '31-40'
            WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 41 AND 50 THEN '41-50'
            WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 51 AND 60 THEN '51-60'
            ELSE '60+'
        END AS age_group, 
        COUNT(*) AS count
    FROM tbl_bene
    WHERE status = 'Validated'
    GROUP BY age_group";
$result_age_distribution = $conn->query($sql_age_distribution);

$response['age_distribution'] = [];
while ($row = $result_age_distribution->fetch_assoc()) {
    $response['age_distribution'][] = $row;
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);
