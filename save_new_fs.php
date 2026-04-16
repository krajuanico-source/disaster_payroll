<?php
include "dbconnect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newfsname = $_POST['newfsname'];

    // Insert the new SDO into the database
    $sql = "INSERT INTO lib_fund_source (fs_name,fs_status) VALUES ('$newfsname','Active')";

    if (mysqli_query($conn, $sql)) {
        echo "New FS added successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
