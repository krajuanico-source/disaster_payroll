<?php

// Initialize database connection
$servername = "localhost";
$username = "root";
$password = "root";
$database ="employee";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define API endpoint functions
function get_products() {
    global $conn;

    $sql = "select * from dtr_new";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = array();
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    } else {
        return array();
    }
}


// Route requests to endpoint functions
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo json_encode(get_products());
} 

// Close database connection
$conn->close();

?>