<?php
  include "dbconnect.php";  // Database connection

  $payroll_no = $_POST['payroll_no'];
  $program     = $_POST['program'];
  $title = $_POST['title'];
  $province = $_POST['province'];
  $date = $_POST['date'];
  $amount = $_POST['amount'];
  $sdo = $_POST['sdo'];
  $fund_source = $_POST['fund_source'];
  $citymuni = $_POST['citymuni'];
  $date_from = $_POST['date_from'] ?? null;
  $date_to   = $_POST['date_to'] ?? null;

$sql = "UPDATE tbl_payroll_list SET
    project_title = '$title',
    province = '$province',
    date_created = '$date',
    amount = '$amount',
    sdo = '$sdo',
    city_muni = '$citymuni',
    fund_source = '$fund_source',
    program = '$program',
    date_from = " . ($date_from ? "'$date_from'" : "NULL") . ",
    date_to   = " . ($date_to ? "'$date_to'" : "NULL") . "
WHERE payroll_no = '$payroll_no'";
  $result = $conn->query($sql);

  if ($result) {
    echo "Details saved successfully!";
  } else {
    echo "Error saving details. Please try again.";
  }

?>