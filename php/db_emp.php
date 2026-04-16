<?php
    $con = mysqli_connect("localhost", "root", "root", "employees");
    //Check connection
    if(mysqli_connect_errno()){
        echo "Failed to connect:".mysqli_connect_errno();
    }
?>