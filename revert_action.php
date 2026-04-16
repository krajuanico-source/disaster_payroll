<?php
include "dbconnect.php";  // Database connection

$beneId      = $_POST['bene_id'];
$user_id     = $_GET['user_id'];
$tl_password = $_POST['tl_password'];

// Query to check TL password
$sqlPay = "SELECT * FROM lib_users WHERE emppassword = '$tl_password'";
$resultPay = $conn->query($sqlPay);

if ($pay = $resultPay->fetch_assoc()) {
    // If password is correct → update beneficiary
    $sqlq = "UPDATE ect_clean_list 
             SET status = NULL, validated_by = '$user_id' 
             WHERE id = '$beneId'";
    $resultq = $conn->query($sqlq);
    
    if ($resultq) {
        header("Location: validation.php?userid=$user_id"); 
        exit();
    } else {
        echo "Error saving correction details. Please try again.";
    }
} else {
    // Invalid password case
    echo "<script>
            alert('Invalid Team Leader password. Please try again.');
            window.history.back();
          </script>";
}
?>
