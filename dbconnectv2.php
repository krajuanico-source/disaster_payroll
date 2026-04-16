<?php
// $servername = "localhost";
// $username = "root";
// $password = "";
// $database ="disaster_db";

$servername = "localhost";
$username = "root";
$password = "";
$database ="disaster_db";
// Create connection
$conn = mysqli_connect($servername, $username, $password,$database);
mysqli_set_charset($conn, "utf8");


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

} 

?>