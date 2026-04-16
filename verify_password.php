<?php
// verify_password.php

include("dbconnect.php"); 

$password = $_POST['password'];

$sql = "SELECT * FROM lib_users WHERE  emppassword = '$password' ";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo 'true';
} else {
    echo 'false';
}
?>