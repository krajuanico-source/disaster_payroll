<?php
  include "dbconnect.php";  // Database connection

  $payroll_no = $_POST['payroll_no'];
  $title = $_POST['title'];
  $province = $_POST['province'];
  $date = $_POST['date'];
  $amount = $_POST['amount'];
  $sdo = $_POST['sdo'];
  $fund_source = $_POST['fund_source'];
  $citymuni = $_POST['citymuni'];

  $sql = "UPDATE tbl_payroll_list set program='ECT', project_title='$title', province='$province', date_created='$date', amount='$amount', sdo='$sdo', fund_source='$fund_source',city_muni='$citymuni' where payroll_no='$payroll_no'";
  $result = $conn->query($sql);

  if ($result) {
    echo "Details saved successfully!";
  } else {
    echo "Error saving details. Please try again.";
  }

?>