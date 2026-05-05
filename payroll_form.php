<?php
session_start();
$payroll_no = $_GET['payroll_no'];  
$page_no    = $_GET['page_no'];
$isLastPage = $_GET['isLastPage'];

include "dbconnect.php";

$sql = "SELECT program FROM tbl_payroll_list WHERE payroll_no='$payroll_no'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$program = strtoupper($row['program']);

if ($program == 'ECT') {
    include 'payroll_form_ect.php';
} elseif ($program == 'CCAM') {
    include 'payroll_form_ccam.php';
} else {
    include 'payroll_form_ect.php'; // default fallback
}
?>